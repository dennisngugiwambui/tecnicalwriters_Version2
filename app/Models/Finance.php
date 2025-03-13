<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Finance extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'finances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'transaction_type',
        'amount',
        'balance_before',
        'balance_after',
        'status',
        'payment_method',
        'payment_reference',
        'description',
        'processed_by',
        'processed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    // Transaction types
    const TYPE_ORDER_PAYMENT = 'order_payment';
    const TYPE_WITHDRAWAL = 'withdrawal';
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_BONUS = 'bonus';
    const TYPE_PENALTY = 'penalty';
    const TYPE_REFUND = 'refund';
    const TYPE_ADMIN_ADJUSTMENT = 'admin_adjustment';

    // Transaction statuses
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    // Payment methods
    const METHOD_MPESA = 'mpesa';
    const METHOD_BANK = 'bank';
    const METHOD_PAYPAL = 'paypal';
    const METHOD_INTERNAL = 'internal';

    /**
     * Get the user associated with the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with the transaction.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the admin user who processed the transaction.
     */
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope a query to only include transactions of a specific type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope a query to only include completed transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include pending transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Add order payment to writer balance.
     *
     * @param int $userId
     * @param int $orderId
     * @param float $amount
     * @param string|null $description
     * @param int|null $processedBy
     * @return Finance
     */
    public static function addOrderPayment($userId, $orderId, $amount, $description = null, $processedBy = null)
    {
        // Get the current balance
        $currentBalance = self::getCurrentBalance($userId);
        $newBalance = $currentBalance + $amount;
        
        // Create the transaction
        $transaction = self::create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'transaction_type' => self::TYPE_ORDER_PAYMENT,
            'amount' => $amount,
            'balance_before' => $currentBalance,
            'balance_after' => $newBalance,
            'status' => self::STATUS_COMPLETED,
            'payment_method' => self::METHOD_INTERNAL,
            'description' => $description ?? 'Payment for order #' . $orderId,
            'processed_by' => $processedBy,
            'processed_at' => now(),
        ]);
        
        // Update writer profile earnings
        $writerProfile = WriterProfile::where('user_id', $userId)->first();
        if ($writerProfile) {
            $writerProfile->increment('earnings', $amount);
            $writerProfile->increment('jobs_completed');
        }
        
        return $transaction;
    }

    /**
     * Process a withdrawal request.
     *
     * @param int $userId
     * @param float $amount
     * @param string $paymentMethod
     * @param string $paymentDetails
     * @param string|null $description
     * @return Finance
     */
    public static function requestWithdrawal($userId, $amount, $paymentMethod, $paymentDetails, $description = null)
    {
        // Get the current balance
        $currentBalance = self::getCurrentBalance($userId);
        
        // Check if user has sufficient balance
        if ($currentBalance < $amount) {
            throw new \Exception('Insufficient balance');
        }
        
        // Create a pending withdrawal transaction
        return self::create([
            'user_id' => $userId,
            'transaction_type' => self::TYPE_WITHDRAWAL,
            'amount' => -$amount, // Negative amount for withdrawals
            'balance_before' => $currentBalance,
            'balance_after' => $currentBalance - $amount,
            'status' => self::STATUS_PENDING,
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentDetails,
            'description' => $description ?? 'Withdrawal request',
        ]);
    }

    /**
     * Process a completed withdrawal.
     *
     * @param int $transactionId
     * @param int $processedBy
     * @param string|null $paymentReference
     * @return Finance
     */
    public static function completeWithdrawal($transactionId, $processedBy, $paymentReference = null)
    {
        $transaction = self::findOrFail($transactionId);
        
        if ($transaction->transaction_type !== self::TYPE_WITHDRAWAL || $transaction->status !== self::STATUS_PENDING) {
            throw new \Exception('Invalid transaction');
        }
        
        // Update the transaction
        $transaction->status = self::STATUS_COMPLETED;
        $transaction->processed_by = $processedBy;
        $transaction->processed_at = now();
        
        if ($paymentReference) {
            $transaction->payment_reference = $paymentReference;
        }
        
        $transaction->save();
        
        return $transaction;
    }

    /**
     * Apply a bonus to a user.
     *
     * @param int $userId
     * @param float $amount
     * @param string $description
     * @param int $processedBy
     * @return Finance
     */
    public static function addBonus($userId, $amount, $description, $processedBy)
    {
        $currentBalance = self::getCurrentBalance($userId);
        $newBalance = $currentBalance + $amount;
        
        return self::create([
            'user_id' => $userId,
            'transaction_type' => self::TYPE_BONUS,
            'amount' => $amount,
            'balance_before' => $currentBalance,
            'balance_after' => $newBalance,
            'status' => self::STATUS_COMPLETED,
            'payment_method' => self::METHOD_INTERNAL,
            'description' => $description,
            'processed_by' => $processedBy,
            'processed_at' => now(),
        ]);
    }

    /**
     * Apply a penalty to a user.
     *
     * @param int $userId
     * @param float $amount
     * @param string $description
     * @param int $processedBy
     * @return Finance
     */
    public static function addPenalty($userId, $amount, $description, $processedBy)
    {
        $currentBalance = self::getCurrentBalance($userId);
        $newBalance = $currentBalance - $amount;
        
        return self::create([
            'user_id' => $userId,
            'transaction_type' => self::TYPE_PENALTY,
            'amount' => -$amount, // Negative amount for penalties
            'balance_before' => $currentBalance,
            'balance_after' => $newBalance,
            'status' => self::STATUS_COMPLETED,
            'payment_method' => self::METHOD_INTERNAL,
            'description' => $description,
            'processed_by' => $processedBy,
            'processed_at' => now(),
        ]);
    }

    /**
     * Get user's current balance.
     *
     * @param int $userId
     * @return float
     */
    public static function getCurrentBalance($userId)
    {
        $latestTransaction = self::where('user_id', $userId)
            ->where('status', self::STATUS_COMPLETED)
            ->latest()
            ->first();
        
        if ($latestTransaction) {
            return $latestTransaction->balance_after;
        }
        
        return 0.00;
    }

    /**
     * Get user's available balance (excluding pending withdrawals).
     *
     * @param int $userId
     * @return float
     */
    public static function getAvailableBalance($userId)
    {
        $currentBalance = self::getCurrentBalance($userId);
        
        // Subtract pending withdrawals
        $pendingWithdrawals = self::where('user_id', $userId)
            ->where('transaction_type', self::TYPE_WITHDRAWAL)
            ->where('status', self::STATUS_PENDING)
            ->sum('amount');
        
        return $currentBalance + $pendingWithdrawals; // Adding because withdrawal amounts are negative
    }

    /**
     * Get transactions for a specific user.
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUserTransactions($userId, $limit = 10)
    {
        return self::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Format amount with sign
     *
     * @return string
     */
    public function getFormattedAmountAttribute()
    {
        $prefix = $this->amount >= 0 ? '+' : '';
        return $prefix . number_format($this->amount, 2);
    }

    /**
     * Get class for transaction amount display
     *
     * @return string
     */
    public function getAmountClassAttribute()
    {
        return $this->amount >= 0 ? 'text-green-600' : 'text-red-600';
    }

    /**
     * Get status badge class
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case self::STATUS_COMPLETED:
                return 'bg-green-100 text-green-800';
            case self::STATUS_PENDING:
                return 'bg-yellow-100 text-yellow-800';
            case self::STATUS_FAILED:
                return 'bg-red-100 text-red-800';
            case self::STATUS_CANCELLED:
                return 'bg-gray-100 text-gray-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
}
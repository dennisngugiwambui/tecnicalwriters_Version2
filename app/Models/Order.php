<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Order extends Model
{
    protected $fillable = [
        'title', 'instructions', 'price', 'deadline', 'task_size',
        'type_of_service', 'discipline', 'software', 'status',
        'client_id', 'writer_id', 'customer_comments'
    ];
    protected $casts = [
        'deadline' => 'datetime',
        'price' => 'decimal:2',
    ];
    // Order status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_UNCONFIRMED = 'unconfirmed';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_DONE = 'done';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_REVISION = 'revision';
    const STATUS_DISPUTE = 'dispute';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_PAID = 'paid';
    const STATUS_FINISHED = 'finished';
    
    // Relationships
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    
    public function writer()
    {
        return $this->belongsTo(User::class, 'writer_id');
    }
    
    public function bids()
    {
        return $this->hasMany(Bid::class);
    }
    
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
    
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    
    // Scopes for different order statuses
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }
    
    public function scopeAssigned($query)
    {
        return $query->whereIn('status', [
            self::STATUS_CONFIRMED, 
            self::STATUS_UNCONFIRMED,
            self::STATUS_IN_PROGRESS,
            self::STATUS_DONE,
            self::STATUS_DELIVERED,
            self::STATUS_REVISION
        ]);
    }
    
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
    
    // New scope for finished orders including completed, paid, and finished statuses
    public function scopeFinished($query)
    {
        return $query->whereIn('status', [
            self::STATUS_COMPLETED,
            self::STATUS_PAID,
            self::STATUS_FINISHED
        ]);
    }
    
    // Check if order is completed or finished
    public function isCompleted()
    {
        return in_array($this->status, [
            self::STATUS_COMPLETED,
            self::STATUS_PAID,
            self::STATUS_FINISHED
        ]);
    }
    
    // Check if payment has been processed
    public function isPaid()
    {
        return in_array($this->status, [
            self::STATUS_PAID,
            self::STATUS_FINISHED
        ]);
    }

    // Add this relationship to your Order model

    /**
     * Get the financial transactions associated with the order.
     */
    public function financialTransactions()
    {
        return $this->hasMany(Finance::class);
    }

    /**
     * Process payment to writer when order is completed
     */
    public function processWriterPayment($processedBy = null)
    {
        if (!$this->writer_id || !$this->isCompleted() || $this->isPaid()) {
            return false;
        }
        
        // Calculate writer payment (you can adjust this calculation based on your business rules)
        $writerAmount = $this->price * 0.70; // 70% goes to writer
        
        // Record the payment
        $transaction = Finance::addOrderPayment(
            $this->writer_id, 
            $this->id, 
            $writerAmount, 
            "Payment for order #{$this->id}: {$this->title}", 
            $processedBy
        );
        
        // Update order status
        $this->status = self::STATUS_PAID;
        $this->save();
        
        return $transaction;
    }
}
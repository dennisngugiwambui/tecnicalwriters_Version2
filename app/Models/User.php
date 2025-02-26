<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'usertype', 
        'status',
        'profile_picture',
        'is_suspended',
        'password',
        'bio',
        'rating',
        'specialization',
        'last_active_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_active_at' => 'datetime',
            'rating' => 'decimal:1',
        ];
    }

    // Role constants (using existing usertype values)
    const ROLE_ADMIN = 'admin';
    const ROLE_CLIENT = 'client';
    const ROLE_WRITER = 'writer';
    const ROLE_SUPPORT = 'support';
    
    // Writer status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_BANNED = 'banned';
    
    // Relationships
    public function ordersAsClient()
    {
        return $this->hasMany(Order::class, 'client_id');
    }
    
    public function ordersAsWriter()
    {
        return $this->hasMany(Order::class, 'writer_id');
    }
    
    public function bids()
    {
        return $this->hasMany(Bid::class);
    }
    
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    
    public function files()
    {
        return $this->hasMany(File::class, 'uploaded_by');
    }
}




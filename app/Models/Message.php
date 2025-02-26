<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Message extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'message', 'read_at'
    ];
    
    protected $casts = [
        'read_at' => 'datetime',
    ];
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}

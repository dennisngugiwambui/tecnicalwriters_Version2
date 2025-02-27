<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'message', 'read_at', 'message_type'
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
    
    // Helper methods to determine message direction
    public function isFromWriter()
    {
        return $this->user->usertype === 'writer';
    }
    
    public function isFromClient()
    {
        return $this->user->usertype === 'client';
    }
    
    public function isFromSupport()
    {
        return in_array($this->user->usertype, ['admin', 'support']);
    }
    
    public function isSentByCurrentUser()
    {
        return $this->user_id == Auth::id();
    }
    
    // Get the appropriate CSS classes based on message sender
    public function getSenderInitial()
    {
        if ($this->isFromWriter()) return 'W';
        if ($this->isFromClient()) return 'C';
        if ($this->isFromSupport()) return 'S';
        return '?';
    }
    
    public function getAvatarClasses()
    {
        $baseClasses = 'w-8 h-8 rounded-full flex items-center justify-center mr-4 flex-shrink-0 mt-1';
        
        if ($this->isSentByCurrentUser()) {
            return $baseClasses . ' bg-blue-100';
        } else if ($this->isFromClient()) {
            return $baseClasses . ' bg-gray-100';
        } else if ($this->isFromSupport()) {
            return $baseClasses . ' bg-green-100';
        }
        
        return $baseClasses . ' bg-gray-100';
    }
    
    public function getAvatarTextClasses()
    {
        $baseClasses = 'font-medium';
        
        if ($this->isSentByCurrentUser()) {
            return $baseClasses . ' text-blue-500';
        } else if ($this->isFromClient()) {
            return $baseClasses . ' text-gray-500';
        } else if ($this->isFromSupport()) {
            return $baseClasses . ' text-green-500';
        }
        
        return $baseClasses . ' text-gray-500';
    }
    
    public function getMessageBubbleClasses()
    {
        $baseClasses = 'max-w-lg rounded-lg p-4';
        
        if ($this->isSentByCurrentUser()) {
            return $baseClasses . ' bg-blue-50';
        } else if ($this->isFromClient()) {
            return $baseClasses . ' bg-gray-100';
        } else if ($this->isFromSupport()) {
            return $baseClasses . ' bg-green-50';
        }
        
        return $baseClasses . ' bg-gray-100';
    }
}
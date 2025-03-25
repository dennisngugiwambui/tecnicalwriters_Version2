<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class File extends Model
{

    protected $fillable = [
        'name', 'path', 'size', 'fileable_id', 'fileable_type', 
        'uploaded_by', 'description', 'mime_type', 'original_name'
    ];
    
    public function fileable()
    {
        return $this->morphTo();
    }
    
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    
    // Add this accessor to check if file exists
    public function getExistsAttribute()
    {
        return Storage::disk('public')->exists($this->path);
    }
}

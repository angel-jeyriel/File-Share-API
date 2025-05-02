<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadFile extends Model
{
    protected $fillable = [
        'filename',
        'original_name',
        'mime_type',
        'size'
    ];

    public function uploadSession()
    {
        return $this->belongsTo(UploadSession::class, 'upload_session_id');
    }
}

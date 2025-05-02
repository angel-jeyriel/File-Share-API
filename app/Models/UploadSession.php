<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadSession extends Model
{
    public function uploadFiles()
    {
        return $this->hasMany(UploadFile::class);
    }
}

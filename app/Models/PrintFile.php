<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrintFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'original_name',
        'storage_path',
        'mime_type',
        'file_extension',
        'file_size',
        'status',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'metadata'  => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function printJobs()
    {
        return $this->hasMany(PrintJob::class);
    }
}


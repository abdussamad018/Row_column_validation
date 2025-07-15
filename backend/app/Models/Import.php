<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_filename',
        'status',
        'total_rows',
        'processed_rows',
        'valid_rows',
        'invalid_rows',
        'error_file_path',
        'started_at',
        'completed_at',
        'error_message'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function records()
    {
        return $this->hasMany(ImportRecord::class);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->total_rows === 0) {
            return 0;
        }
        
        return round(($this->processed_rows / $this->total_rows) * 100, 2);
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            default => 'Unknown'
        };
    }
} 
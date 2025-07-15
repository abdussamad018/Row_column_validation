<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_id',
        'row_number',
        'name',
        'email',
        'phone',
        'gender',
        'is_valid',
        'errors',
        'processed_at'
    ];

    protected $casts = [
        'errors' => 'array',
        'processed_at' => 'datetime',
        'is_valid' => 'boolean',
    ];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }

    public function getErrorsTextAttribute()
    {
        if (empty($this->errors)) {
            return '';
        }
        // Flatten the errors array (in case it's an array of arrays)
        $flat = [];
        foreach ($this->errors as $field => $messages) {
            if (is_array($messages)) {
                foreach ($messages as $msg) {
                    $flat[] = $msg;
                }
            } else {
                $flat[] = $messages;
            }
        }
        return implode(', ', $flat);
    }

    public function getStatusBadgeAttribute()
    {
        return $this->is_valid ? 'Valid' : 'Invalid';
    }
} 
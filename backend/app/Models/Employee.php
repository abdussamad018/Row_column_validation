<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'import_id'
    ];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkInProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name', 'stage', 'quantity', 'started_at', 'expected_completion', 'status'
    ];
}

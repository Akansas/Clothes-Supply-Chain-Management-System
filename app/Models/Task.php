<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Task
 *
 * @property int $assigned_count
 */
class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'required_skill', 'quantity', 'center_id', 'assigned_count'
    ];

    public function center()
    {
        return $this->belongsTo(SupplyCenter::class, 'center_id');
    }
}

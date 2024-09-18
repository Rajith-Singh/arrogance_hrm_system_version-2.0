<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveDeletionRequest extends Model
{
    // Define the table name if not following the Laravel naming convention
    protected $table = 'leave_deletion_requests';

    // Allow mass assignment for these fields
    protected $fillable = ['leave_id', 'reason', 'attachment'];

    // Define relationship with Leave (if required)
    public function leave()
    {
        return $this->belongsTo(Leave::class, 'leave_id');
    }
}


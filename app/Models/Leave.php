<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;
//    use SoftDeletes;

    protected $fillable = [
        'user_id', 'leave_type', 'start_date', 'end_date', 'reason', 'additional_notes', 'covering_person', 'supervisor_approval', 'supervisor_note', 'management_approval', 'management_note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coveringPerson()
    {
        return $this->belongsTo(User::class, 'covering_person');
    }
}

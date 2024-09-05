<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'leave_id', 'emp_id', 'message', 'read'];

    public function markAsRead()
    {
        $this->update(['read' => true]);
    }
}

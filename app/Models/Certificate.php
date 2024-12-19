<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    // Define the fields that are mass assignable
    protected $fillable = [
        'employee_id',         // ID of the employee
        'certificate_type',    // Type of certificate
        'issued_date',         // Certificate issued date
        'expire_date',         // Certificate expiration date
        'certificate_file',    // Path to the uploaded certificate file
    ];

    // Calculate remaining days for expiration
    public function getRemainingDaysAttribute()
    {
        if ($this->expire_date) {
            return Carbon::now()->diffInDays(Carbon::parse($this->expire_date), false);
        }
        return null;
    }

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
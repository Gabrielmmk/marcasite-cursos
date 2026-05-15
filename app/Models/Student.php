<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'cpf', 'birth_date', 'address', 'city', 'state'];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}

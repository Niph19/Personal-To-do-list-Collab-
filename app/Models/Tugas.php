<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'deadline',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
        ];
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

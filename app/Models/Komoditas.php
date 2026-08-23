<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komoditas extends Model
{
    use HasFactory;

    protected $table = 'komoditas';

    protected $fillable = [
        'user_id',
        'name',
    ];

    /**
     * Get the user that owns the komoditas.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

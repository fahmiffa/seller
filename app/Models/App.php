<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    protected $fillable = [
        'user_id',
        'nama_aplikasi',
        'logo',
        'saldo',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get the user that owns the app.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'customer_id';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'user_id',
        'nama',
        'alamat',
        'telepon',
        'email',
    ];

    /**
     * Get the user that owns the customer.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

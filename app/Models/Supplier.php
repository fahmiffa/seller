<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $primaryKey = 'supplier_id';

    protected $fillable = [
        'user_id',
        'nama_supplier',
        'alamat',
        'telepon',
        'email',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get the user that owns the supplier.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

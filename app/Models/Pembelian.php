<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $primaryKey = 'pembelian_id';

    protected $fillable = [
        'supplier_id',
        'user_id',
        'tanggal_pembelian',
        'total_pembelian',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(DetailPembelian::class, 'pembelian_id');
    }
}

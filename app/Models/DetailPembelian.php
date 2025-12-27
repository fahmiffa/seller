<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    protected $primaryKey = 'detail_pembelian_id';

    protected $fillable = [
        'pembelian_id',
        'item_id',
        'qty',
        'harga_beli',
        'subtotal',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}

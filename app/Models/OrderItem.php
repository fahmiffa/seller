<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'komoditas_id',
        'satuan_id',
        'jumlah',
        'harga_unit',
        'harga_supplier',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_unit' => 'decimal:2',
        'harga_supplier' => 'decimal:2',
    ];

    /**
     * Get the order that owns this item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Get the komoditas for this item.
     */
    public function komoditas()
    {
        return $this->belongsTo(Komoditas::class, 'komoditas_id');
    }

    /**
     * Get the satuan for this item.
     */
    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id', 'satuan_id');
    }
}

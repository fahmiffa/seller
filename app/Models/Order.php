<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'po_number',
        'po_document',
        'user_id',
        'supplier_id',
        'unit_id',
        'keterangan',
        'status',
        'tanggal_po',
    ];

    protected $casts = [
        'tanggal_po' => 'date',
    ];

    /**
     * Get the user (creator) for the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the supplier for the order.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    /**
     * Get the unit for the order.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Get the order items (multi-commodity line items).
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Get the invoice for the order.
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'order_id');
    }
}

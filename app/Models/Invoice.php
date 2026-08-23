<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'order_id',
        'inv_number',
        'inv_document',
        'subtotal',
        'ppn',
        'total',
        'payment_status',
        'inv_date',
    ];

    protected $casts = [
        'inv_date'  => 'date',
        'subtotal'  => 'decimal:2',
        'ppn'       => 'decimal:2',
        'total'     => 'decimal:2',
    ];

    /**
     * Get the order that owns the invoice.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    protected $primaryKey = 'detail_transaksi_id';

    protected $fillable = [
        'transaksi_id',
        'item_id',
        'qty',
        'harga_satuan',
        'subtotal',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}

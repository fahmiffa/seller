<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $primaryKey = 'transaksi_id';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'customer_id',
        'user_id',
        'tanggal_transaksi',
        'total_harga',
        'metode_pembayaran',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id');
    }
}

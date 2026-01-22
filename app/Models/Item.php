<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $primaryKey = 'item_id';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'user_id',
        'satuan_id',
        'supplier_id',
        'nama_item',
        'image',
        'tipe_item',
        'harga_beli',
        'harga_jual',
        'stok',
    ];


    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Get the user that owns the item.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    protected $table = 'satuans';
    protected $primaryKey = 'satuan_id';

    protected $fillable = [
        'nama_satuan',
        'keterangan',
    ];
}

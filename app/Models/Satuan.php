<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    use HasFactory;
    protected $table      = 'satuans';
    protected $primaryKey = 'satuan_id';

    protected $fillable = [
        'user_id',
        'nama_satuan',
        'keterangan',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get the user that owns the satuan.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

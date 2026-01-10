<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;
    protected $table      = 'kategoris';
    protected $primaryKey = 'kategori_id';
    protected $hidden     = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'user_id',
        'nama_kategori',
        'tipe',
    ];

    /**
     * Get the user that owns the kategori.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

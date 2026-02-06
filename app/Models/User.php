<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'saldo',
        'status',
        'parent_id',
    ];

    /**
     * Get the parent user.
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Get the children users.
     */
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'email_verified_at'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the effective owner user for data relations and saldo.
     * If Operator (role 3), return parent. Otherwise return self.
     */
    public function getOwner()
    {
        if ($this->role == 3 && $this->parent_id) {
            return $this->parent ?: User::find($this->parent_id);
        }
        return $this;
    }

    /**
     * Get the effective owner ID for data relations.
     * If Operator (role 3), return parent_id. Otherwise return id.
     */
    public function getOwnerId()
    {
        return $this->getOwner()->id;
    }

    /**
     * Accessor for saldo.
     * If Operator (role 3), use parent's saldo.
     */
    public function getSaldoAttribute($value)
    {
        if ($this->role == 3 && $this->parent_id) {
            $owner = $this->getOwner();
            if ($owner->id !== $this->id) {
                return $owner->saldo;
            }
        }
        return $value;
    }

    /**
     * Mutator for saldo.
     * If Operator (role 3), update parent's saldo instead.
     */
    public function setSaldoAttribute($value)
    {
        if ($this->role == 3 && $this->parent_id) {
            $owner = $this->getOwner();
            if ($owner->id !== $this->id) {
                // Update parent directly in DB or on the object
                // If we update the object, it might trigger another setSaldoAttribute on parent (which is fine)
                $owner->update(['saldo' => $value]);
                return;
            }
        }
        $this->attributes['saldo'] = $value;
    }

    /**
     * Get the apps for the user.
     */
    public function apps()
    {
        return $this->hasMany(App::class);
    }
}

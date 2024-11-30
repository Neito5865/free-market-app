<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'postCode',
        'address',
        'building',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites(){
        return $this->belongsToMany(Item::class, 'favorites', 'user_id', 'item_id')->withTimestamps();
    }

    public function favorite($itemId){
        $exist = $this->isFavorite($itemId);
        if($exist){
            return false;
        }else{
            $this->favorites()->attach($itemId);
            return true;
        }
    }

    public function unfavorite($itemId){
        $exist = $this->isFavorite($itemId);
        if($exist){
            $this->favorites()->detach($itemId);
            return true;
        }else{
            return false;
        }
    }

    public function isFavorite($itemId){
        return $this->favorites()->where('item_id', $itemId)->exists();
    }
}

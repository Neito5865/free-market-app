<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'price',
        'description',
        'image',
        'brand',
        'condition_id',
    ];

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_category');
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    public function address()
    {
        return $this->hasOneThrough(Address::class, Purchase::class, 'item_id', 'id', 'id', 'address_id');
    }

    public function favoriteUsers(){
        return $this->belongsToMany(User::class, 'favorites', 'item_id', 'user_id')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}

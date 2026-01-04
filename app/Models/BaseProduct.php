<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseProduct extends Model
{
    protected $fillable = ["name" , "images" , "user_id"];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function sizeColors()
    {
        return $this->hasMany(SizeColor::class, 'product_id');
    }
    protected $casts = [
        'images' => 'array',
    ];
}

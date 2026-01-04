<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseProduct extends Model
{
    protected $fillable = ["name" , "images"];

    public function sizeColors(){
        return $this->hasMany(SizeColor::class, 'product_id');
    }
    protected $casts = [
        'images' => 'array',
    ];
}

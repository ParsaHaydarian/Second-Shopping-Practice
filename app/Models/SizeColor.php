<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizeColor extends Model
{
    protected $fillable = ['product_id' , 'size' , 'color' , 'price' , 'quantity'];

    public function product(){
        return $this->belongsTo(BaseProduct::class);
    }
}

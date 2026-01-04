<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sell extends Model
{
    protected $fillable = ['user_id', 'orders'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $gaurded = [];

    public function product()
    {
        return $this->hasMany(Product::class,'id','product_id');
    }
    public function productsum()
    {
        return $this->hasMany(Product::class,'id','product_id')->withSum('price');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    public function productslist(){
        $products=Product::select('id','product_name','price')->get();
        return response()->json(['productslist'=>$products,'status'=>200]);
    }
}

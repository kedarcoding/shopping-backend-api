<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    //

    public function createOrder(Request $request){
        
       
       $order= Order::create([
            'user_id'=>$request->user()->id,
            'total_amount'=>0.00,
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);
       return response()->json(['order_id'=>$order->id,'msg'=>'order created','status'=>200,]);

    }
    public function addtoCart(Request $request) {

        $validator = Validator::make($request->all(),[ 
            'items.*.product_id' => 'required|integer',
            'order_id' => 'required|integer',
            'items.*.quantity' => 'required|integer',
        ]);
       if ($validator->fails()) {
            return response()->json(['errors'=>$validator->messages()],400);
        }
       $data=$request->all();
       $cartdata=[];
       foreach($data['items'] as $d){
        $cartdata[]= [
                'user_id' => $request->user()->id, 
                'product_id' => $d['product_id'], 
                'order_id' => $request->order_id, 
                'quantity' => $d['quantity'],
                'created_at'=>now(),
                'updated_at'=>now()
        ];
       }
       try{
        Cart::insert($cartdata);
         return response()->json(['msg'=>'items added to cart','status'=>200,]);
       }catch(Exception $error){
        Log::error($error);
        return response()->json(['msg'=>'items not added to cart'],400);
       }
       
    }

    public function showWishlist(Request $request) {
    $validator = Validator::make($request->all(),[ 
        'order_id' => 'required',
    ]);
     if ($validator->fails()) {
        return response()->json(['errors'=>$validator->messages(),'status'=>400]);
    }
    $data= Cart::leftjoin('products','carts.product_id','products.id')
    ->select('products.id as pid','products.*','carts.*')
    ->where('order_id',$request->order_id)
    ->get();
     $total=0;

     foreach($data as $d){
        $sum=$d['price']*$d['quantity'];
        $total= $total+$sum;
     } 

       return response()->json(['wishlist'=>$data,'total_amount'=>$total,'msg'=>'wish list showing succssfully','status'=>200,]);

    }

    public function updateCartItems(Request $request) {
        $validator = Validator::make($request->all(),[ 
            'items.*.id' => 'required|integer',
            'order_id' => 'required|integer',
            'items.*.quantity' => 'required|integer',
        ]);
       if ($validator->fails()) {
            return response()->json(['errors'=>$validator->messages()],400);
        }
       $data=$request->all();
       $cartdata=[];
       foreach($data['items'] as $d){
        $cartdata[]= [
                'id'=>$d['id'],
                'user_id' => $request->user()->id, 
                'product_id' => $d['product_id'], 
                'order_id' => $request->order_id,
                'quantity' => $d['quantity'],
                'updated_at'=>now()
        ];
       }
        Cart::upsert($cartdata, ['id','user_id','order_id'], ['quantity','updated_at']);
        return response()->json(['msg'=>'items quantities has updated','status'=>200]);
    }

    public function deleteCartitem(Request $request) {
        $validator = Validator::make($request->all(),[ 
            'id' => 'required',
        ]);
       if ($validator->fails()) {
            return response()->json(['errors'=>$validator->messages(),'status'=>400]);
        }
       $res=  Cart::where(['id' => $request->id,'user_id'=>$request->user()->id])->delete();
        if($res){
       return response()->json(['msg'=>'item deleted','status'=>200]);
        }else{
       return response()->json(['msg'=>'item not deleted'],400);
        }
    }

    
}

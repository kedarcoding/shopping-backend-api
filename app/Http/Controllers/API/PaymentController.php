<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class PaymentController extends Controller
{
    //
    public $paymentintendapi='https://fakedata.nanocorp.io/api/payment/create';
    public $confirmpaymentintend='https://fakedata.nanocorp.io/api/payment/confirm';


    public function makePayment(Request $request) {
        $validator = Validator::make($request->all(),[ 
            'order_id' => 'required|integer',
            'total_amount' => 'required|numeric',
        ]);
       if ($validator->fails()) {
            return response()->json(['errors'=>$validator->messages()],400);
        }
        try{
        $response = Http::post($this->paymentintendapi, [
            'order_id' => $request->order_id,
            'customer_email'=>$request->user()->email,
            'amount' => $request->total_amount,
        ]);
        $response=$response->json();

        Payment::create([
            'user_id'=>$request->user()->id,
            'order_id'=>$request->order_id,
            'amount'=>$request->total_amount,
            'status'=>$response['result'],
            'payment_intend'=>$response['data']['payment_intend']
        ]);
        return response()->json(['order_id'=>$request->order_id,'payemnt_intend'=>$response['data']['payment_intend'],'result'=>$response['result']]);
    }catch(Exception $error){
        Log::error($error);
        return response()->json(['msg'=>'payment tranction failed','error'=>$error],400);
    }
    }

    public function confirmorderPaymentIntend(Request $request){
        $validator = Validator::make($request->all(),[ 
            'order_id' => 'required|integer',
            'payment_intend' => 'required|string',
        ]);
       if ($validator->fails()) {
            return response()->json(['errors'=>$validator->messages()],400);
        }
        try{
        $response = Http::post($this->confirmpaymentintend, [
            'payment_intend'=>$request->payment_intend,
        ]);

        $response=$response->json();
    
        if($response['result']=='success'){
        Order::where(['id'=>$request->order_id])->update([
            'payment_status'=>'success',
            'total_amount'=>$response['data']['payment_intend']['amount'],
            'is_placed'=>1,
            'updated_at'=>now()
        ]);
        return response()->json(['payemnt_intend'=>$response['data']['payment_intend'],'result'=>$response['result']]);
          }
    }catch(Exception $error){
            Log::error($error);
            return response()->json(['msg'=>'payment tranction failed','error'=>$error],400);
        }
    }
}

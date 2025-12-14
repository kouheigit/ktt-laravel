<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Reservation;
use Auth;

use DB;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $cart = Cart::with([
            'cartDetails.service',
            'cartDetails.serviceOption'
        ])->where('user_id',$user->id)->first();

        if(!$cart){
            return view('cart.index',['cart'=>null,'total_price'=> 0]);
        }

        $total_price = $cart->cartDetails->sum('total_price');

        //最新予約
        $last_reservation = REservation::getLastReservation();

        return view('cart.index',compact('cart','total_price','last_reservation'));
    }
    //カート明細削除
    public function delete(CartDetail $cart_detail)
    {
        
    }


}

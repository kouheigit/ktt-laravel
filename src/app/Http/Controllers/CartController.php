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
use Illuminate\Http\Request;

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
        $last_reservation = Reservation::getLastReservation();

        return view('cart.index',compact('cart','total_price','last_reservation'));
    }
    //カート明細削除
    public function delete(CartDetail $cart_detail)
    {
        if($cart_detail->cart->user_id != Auth::id()) {
            abort(403);
        }

        $cart_detail->delete();

        return redirect()->route('cart.index')
            ->with('success','カートから削除しました');
    }

    //確認画面
    public function confirm(Cart $cart)
    {
        if($cart->user_id != Auth::id()){
            abort(403);
        }
        $cart->load([
           'cartDetails.service',
           'cartDetails.serviceOption'
        ]);

        $total_price = $cart->cartDetails->sum('total_price');

        $last_reservation = Reservation::getLastReservation();

        return view('cart.confirm',compact('cart','total_price','last_reservation'));
    }

    public function store(Cart $cart, Request $request)
    {
        
    }
}

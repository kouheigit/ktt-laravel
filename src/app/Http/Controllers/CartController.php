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

        
    }
}

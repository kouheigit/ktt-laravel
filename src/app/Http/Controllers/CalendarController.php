<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceOption;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Reservation;
use Auth;

class CalendarController extends Controller
{
    public function index()
    {
        //現地注文可能なサービス
        $service = Service::where('status',1)
            ->where('tab',2) //現地注文タブ
            ->orderBy('sort','asc')
            ->with('sercviceOptions')
            ->get();

        //最新の予約取得
        $last_reservation = Resevation::getLastReservation();

        return view('services.index',compact('services','last_reservation'));
    }
  
}

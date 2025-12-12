<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;
use App\Models\Service;
use App\Models\ServiceOption;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Reservation;
use Auth;


class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('status',1)
            ->where('tab',2) //現地注文タブ
            ->orderBy('sort','asc')
            ->with('serviceOptions')
            ->get();

        //最新の予約取得
        $last_reservation = Reservation::getLastReservation();

        return view('services.index',compact('services','last_reservation'));
    }

    public function show(Service $service,Request $request){

    }
}

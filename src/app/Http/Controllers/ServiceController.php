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

    public function show(Service $service,Request $request)
    {
        $service->load('serviceOptions');

        //予約情報(任意)
        $reservartion_id = $request->input('reservation_id');
        $reservaiton = null;

        if($reservartion_id)
        {
            $reservation_id = Reservation::findOrFail($reservartion_id);
            $reservation = null;

            if($reservation_id){
                $reservation = $request->input('reservation_id');

                if($reservation->user_id != Auth::id()){
                    abort(403);
                }
            }
            return view('services.show',compact('service','reservation'));

        }


    }

}

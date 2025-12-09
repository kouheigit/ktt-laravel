<?php

namespace App\Http\Controllers;
use App\Models\Reservation;
use App\Models\Calendar;
use App\Models\Service;
use App\Models\TmpOrderDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Consts\ReservationConst;
use App\Services\FreedayService;
use Illuminate\Http\Request;
use Auth;
use DB;
use Carbon\Carbon;

class ReservationController extends Controller
{
    private $freeday_service;

    public function__construct(FreedayService $freeday_service)
    {
        $this->freeday_service = $freeday_service;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $start_date = Carbon::now()->firstOfYear();
        $end_date = $start_date->copy()->addYears(2)->endOfYear();

        $calendars = Calendar::where('user_id',$user->id)->whereBetween('start_date',[$start_date,$end_date])
            ->orderBy('start_date','asc')
            ->get();

        // FREEDAYSを取得
        $freedays = $this->freeday_service->getFreedays($user);

        //予約を取得
        $reservations = Reservation::where('owner_id',$user->id)
            ->whereIn('status',[
                ReservationConst::STATUS_APPLYING,
                ReservationConst::STATUS_UNDER_RESERVATION,
                ReservationConst::STATUS_RESERVED,
                ])
            ->orderBy('checkin_date','asc')
            ->get();
        return view('reservation.index',compact('calendars','freedays','reservations'));
    }

    public function create(Request $request)
    {
        $calendar_id = $request->calendar_id;

        $fr = $request->fr;//フリーデイID

        //FIXDAYが予約失敗した時の処理
        if($calendar_id){
            //FIXDAY予約
            $calendar_id = Calendar::findOrfail($calendar_id);

            return view('reservation.create',compact('calendar'));
        }

        //FREEDAYの予約失敗した時の処理
        if($fr){
            //FREEDAY予約
            $freeday = Freeday::findOrFail($fr);

            return view('reservation.create_freeday',compact('freeday'));
        }
        abort(404);
    }
    

}

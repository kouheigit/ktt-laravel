<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Freeday;
use App\Models\UserPoint;
use App\Services\FreedayService;
use App\Services\PointService;
use App\Consts\ReservationConst;
use App\Consts\UserConst;
use Auth;
use Carbon\Carbon;

class MypageController extends Controller
{
    private $freeday_service;
    private $point_service;
    public function __construct(
        FreedayService $freeday_service,
        PointService $point_service
    ){
        $this->freeday_service = $freeday_service;
        $this->point_service = $point_service;
    }

    public function index(){
        $user = Auth::user();

        //ポイント残高取得
        $user_point = $this->point_service->getAvailablePoints($user->id);
        $pointbalance = $this->point_service->getPointBalanceByExpiry($user->id);

        $freedays = collect();

        if($user->type == UserConst::TYPE_OWNER){
            $freedays = $this->freeday_service->getFreedays($user);
        }

        $reservations = Reservation::where('user_id',$user->id)
            ->whereIn('status',[
               ReservationConst::STATUS_APPLYING,
               ReservationConst::STATUS_UNDER_RESERVATION,
               ReservationContst::STATUS_RESERVED
            ])
        ->where('checkin_date','>=',Carbon::now()->format('Y-m-d'))
            ->with('hotel')
            ->orderBy('checkin_date','asc')
            ->get();
        return view('mypage.index',compact(
            'user_point',
            'pointbalance',
            'freedays',
            'reservations'
        ));
    }
}

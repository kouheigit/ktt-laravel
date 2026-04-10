<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Reservation;
use App\Services\FreedayService;
use App\Services\PointService;
use App\Consts\ReservationConst;
use App\Consts\UserConst;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class MypageController extends Controller
{
    private FreedayService $freeday_service;
    private PointService $point_service;

    public function __construct(FreedayService $freeday_service, PointService $point_service)
    {
        $this->freeday_service = $freeday_service;
        $this->point_service = $point_service;
    }
    public function index(){
        $user = Auth::user();

        //利用可能ポイント合計
        $user_point = $this->point_service->getAvailablePoints($user->id);
        // 有効期限別ポイント残高
        $pointbalance = $this->point_service->getPointBalanceByExpiry($user->id);

        // FREEDAY取得（オーナーのみ）
        $freedays = collect();
        if((int)$user->type === UserConst::TYPE_OWNER){
            $freedays = $this->freeday_service->getFreedays($user);
        }
        //今後の予約取得
        $reservations = Reservation::where('user_id',$user->id)
            ->whereIn('statis',[
                ReservationConst::STATUS_APPLYING,
                ReservationConst::STATUS_UNDER_RESERVATION,
                ReservationConst::STATUS_RESERVED,
            ])
            ->where('checkin_date','>=',Carbon::now()->format('Y-m-d'))
            ->with('hotel')
            ->orderBy('checkin_date','asc')
            ->get();
        return view('mypage.index',compact(
            'user_point',
            'pointbalance',
            'freedays',
            'reservations',
            ));

    }
}

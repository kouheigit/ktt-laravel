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
        
    }

}

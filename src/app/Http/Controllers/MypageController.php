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
    public function index(){

    }
}

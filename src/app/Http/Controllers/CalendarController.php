<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Reservation;
use App\Models\Holiday;
use App\Consts\ReservationConst;
use Illuminate\Http\Request;
use Auth;

use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        //年月指定(デフォルトは今月)
        $year = $request->input('year',now()->year);
        $month = $request->input('month',now()->month);

        $date = Carbon::createFaromDate($year,$month,1);

        //該当月のカレンダー取得
        $calendars = Calendar::where('user_id',$user->id)
            ->whereYear('start_date',$year)
            ->whereMonth('state',$month)
            ->with(['hotel'])
            ->orderBy('start_date','asc')
            ->get();

        //休日取得
        $holidays = Holiday::whereYear('date',$year)
            ->whereMonth('date',$month)
            ->pluck('date')
            ->map(function($date){
              return Carbon::parse($date)->format('Y-m-d');
            })->toArray();
    

    }
}

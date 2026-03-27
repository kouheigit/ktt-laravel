<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Calendar;
use App\Models\Reservation;
use App\Models\Holiday;
use App\Consts\ReservationConst;
use Auth;
use Carbon\Carbon;


class CalendarOptionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        //年月指定（デフォルトは今月）
        $year = $request->input('year',now()->year);
        $month = $request->input('month',now()->month);

        $date = Carbon::createFromDate($year,$month,1);
        //該当つきのカレンダー取得
        $calendars = Calendar::where('user_id',$user->id)
            ->whereYear('start_date',$year)
            ->whereMonth('start_date',$month)
            ->with(['hotel'])
            ->orderBy('start_date','asc')
            ->get();

    }
}



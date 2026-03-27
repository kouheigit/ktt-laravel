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
        //休日取得
        $holidays = Holiday::whereYear('date',$year)
            ->whereMonth('date',$month)
            ->pluck('date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })->toArray();
        //予約済みの日程
        $reservations = Reservation::where('user_id',$user->id)
            ->where(function($query) use ($date){
                $query->whereYear('checkin_date',$date->year)
                    ->whereMonth('checkin_date',$date->month);
            })
            ->orWhere(function($query) use ($date){
                $query->whereYear('checkin_date',$date->year)
                    ->whereMonth('checkout_date',$date->month);
            })->get();
    }
}


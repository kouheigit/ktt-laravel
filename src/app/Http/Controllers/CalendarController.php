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

        //予約済み日程
        $reservations = Reservation::where('user_id', $user->id)
            ->where(function($query) use ($date) {
                $query->whereYear('checkin_date', $date->year)
                    ->whereMonth('checkin_date', $date->month);
            })
            ->orWhere(function($query) use ($date) {
                $query->whereYear('checkout_date', $date->year)
                    ->whereMonth('checkout_date', $date->month);
            })
            ->get();

        // 前月・次月
        $prevMonth = $date->copy()->subMonth();
        $nextMonth = $date->copy()->addMonth();

        return view('calendar.index',compact(
           'calendars',
           'holidays',
           'reservations',
           'year',
           'month',
           'prevMonth',
           'nextMonth',
        ));
    }
    //日付詳細
    public function detail($year, $month, $day){
        $user = Auth::user();
        $date = Carbon::createFromDate($year, $month, $day);

        // その日のカレンダー情報取得
        $calendar = Calendar::where('user_id', $user->id)
            ->where('date', $date->format('Y-m-d'))
            ->orWhere(function($query) use ($date) {
                $query->where('start_date', '<=', $date->format('Y-m-d'))
                    ->where('end_date', '>=', $date->format('Y-m-d'));
            })
            ->first();

        // その日の予約
        $reservation = Reservation::where('user_id', $user->id)
            ->where('checkin_date', '<=', $date->format('Y-m-d'))
            ->where('checkout_date', '>=', $date->format('Y-m-d'))
            ->first();

        return view('calendar.detail', compact('calendar', 'reservation', 'date'));
    }
}

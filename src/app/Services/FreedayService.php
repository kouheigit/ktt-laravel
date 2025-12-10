<?php
// app/Services/FreedayService.php

namespace App\Services;
use App\Models\Freeday;
use App\Models\User;
use Carbon\Carbon;

class FreedayService{
    public function getFreedays(User $user){
        $now = Carbon::now();

        return Freeday::where('user_id',$user->id)
            ->where('end_date','>=',$now->format('Y-m-d'))
            ->where('freedays','>',0)
            ->where('status',1)
            ->orderBy('end_date','asc')
            ->get();

    }

    //今年度の最大フリーデイ泊数を取得
    public function getYearMaxFreedaysNum(User $user)
    {
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        return Freeday::where('user_id',$user->id)
            ->whereBetween('start_date',[$startOfYear,$endOfYear])
            ->sum('freedays');
    }

    //フリーデイ利用可能チェック

     public function canUseFreeday(Freeday $freeday, $days)
     {
         if($freeday->freedays < $days)
         {
             return false;
         }
     }
    
}

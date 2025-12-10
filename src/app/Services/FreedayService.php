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
         //残り泊数チェック
         if($freeday->freedays < $days)
         {
             return false;
         }
         //有効期限チェック
         if(Carbon::parse($freeday->end_date)->isPast())
         {
             return false;
         }
         //利用開始日チェック
         $availableFrom = Carbon::parse($freeday->start_date)->firstOfMonth()->subMonths(18);
         if(Carbon::now()->isBefore($availableFrom)){
             return false;
         }
         return true;
     }
    //フリーデイ消費
     public function consumeFreeday(Freeday $freeday,$days)
     {
         if(!$this->canUseFreeday($freeday,$days))
         {
             throw new Exception('フリーデイが利用できません');
         }
         $freeday->decrement('freedays',$days);

         return $freeday;
     }


    //フリーデイ返却（キャンセル時）
    public function returnFreeday(Freeday $freeday, $days)
    {
        $freeday->increment('freedays',$days);

        return $freeday;
    }
    
}


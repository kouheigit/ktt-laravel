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
}

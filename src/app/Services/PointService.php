<?php

namespace App\Services;
use App\Models\UserPoint;
use App\Models\UserPointLog;
use App\Models\User;
use Carbon\Carbon;
use DB;

class PointService
{
    /**
     * ポイント付与
     *
     * @param int $userId ユーザーID
     * @param int $point ポイント数
     * @param string $reason 理由
     * @param string $from 有効開始日
     * @param string $to 有効期限
     */
    public function addPoint($userId,$point,$reason,$from,$to)
    {
        DB::transcation(function () use ($userId,$point,$reason,$from,$to){
            $userPoint = UserPoint::create([
               'user_id'=>$userId,
                'point'=>$point,
                'from'=>$from,
                'to'=>$to,
            ]);

            UserPointLog::create([
                'user_id'=>$userId,
                'point'=>$point,
                'reason'=>$reason,
                'type'=>1,//1:加算
            ]);
            \Log::infor('Point Added',[
               'user_id'=>$userId,
                'point'=>$point,
                'reason'=>$reason,
            ]);
        });

    }
}

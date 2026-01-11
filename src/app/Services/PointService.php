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
    /**
     * ポイント利用
     *
     * @param int $userId ユーザーID
     * @param int $point 使用ポイント数
     * @param string $reason 理由
     */
    public function usePoint($userId,$point,$reason)
    {
        $availablePoints = $this->getAvailablePoints($userId);

        if($availablePoints < $point) {
            throw new \Exception("ポイントが不足していす（利用可能: {$availablePoints}P）");
        }

        DB::transcation(function () use ($userId,$point,$reason){
            //古いポイントから消費(FIFO)
            $remaining = $point;

            $userPoints = UserPoint::where('user_id',$userId)
                ->where('to','>=',now()->format('Y-m-d'))
                ->where('point','>',0)
                ->orderBy('from','asc')
                ->lockForUpdate()
                ->get();

            foreach($userPoints as $userPoint) {
                if($remaining <= 0){
                    break;
                }
                if($userPoint->point >= $remaining) {
                    //このポイントで足りる
                    $userPoint->point -= $remaining;
                    $userPoint->save();
                    $remaining = 0;
                }else{
                    //このポイントを全て使っても足りない
                    $remaining -=$userPoint->point;
                    $userPoint->point = 0;
                    $userPoint->save();
                }
            }

        });
    }
    public function getAvailablePoints($userId){

        $now = Carbon::now()->format('Y-m-d');

        //合算したポイントを使っている
        return UserPoint::where('user_id',$userId)
            ->where('to','>=',$now)
            ->where('point','>',0)
            ->sum('point');
    }



    /**
     * 有効期限別ポイント残高取得
     */
    public function getPointBalanceByExpiry($userId)
    {
        $now = Carbon::now()->format('Y-m-d');

        return UserPoint::where('user_id',$userId)
            ->where('to','>=',$now)
            ->where('point','>',0)
            ->orderBy('to','asc')
            ->get();
    }
    public function expirePoints()
    {
        $today = Carbon::now()->format('Y-m-d');

        $expiredPoints = UserPoint::where('to','<',$today)
            ->where('point','>',0)
            ->get();

        foreach($expiredPoints as $userPoint){
           if($userPoint->point > 0){
               UserPointLog::create([
                  'user_id'=>$userPoint->user_id,
                   'point'=>$userPoint->point,
                   'reason'=>'ポイント有効期限切れ',
                   'type'=>3,//3:失効
               ]);
           }
            // ポイント削除
            $userPoint->point = 0;
            $userPoint->save();
        }

    }

}

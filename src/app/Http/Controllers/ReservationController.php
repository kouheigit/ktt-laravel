<?php
// app/Http/Controllers/ReservationController.php

namespace App\Http\Controllers;
use App\Models\Reservation;
use App\Models\Calendar;
use App\Models\Service;
use App\Models\TmpOrderDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Consts\ReservationConst;
use App\Services\FreedayService;
use Illuminate\Http\Request;
use Auth;
use DB;
use Carbon\Carbon;

class ReservationController extends Controller
{
    private $freeday_service;

    public function __construct(FreedayService $freeday_service)
    {
        $this->freeday_service = $freeday_service;
    }
    public function index(Request $request)
    {
        $user = Auth::user();

        //2年分のFIXDAYを取得
        $start_date = Carbon::now()->firstOfYear();
        $end_date = $start_date->copy()->addYear(2)->endOfyear();

        $calendars = Calendar::where('user_id',$user->id)
            ->whereBetween('start_date',[$start_date,$end_date])
            ->orderBy('start_date',[$start_date,$end_date])
            ->get();

        //FREEDAYSを取得
        $freedays = $this->freeday_service->getFreedays($user);

        // 予約を取得
        $reservations = Reservation::where('owner_id',$user->id)
            ->whereIn('status',[
                ReservationConst::STATUS_APPLYING,
                ReservationConst::STATUS_UNDER_RESERVATION,
                ReservationConst::STATUS_RESERVED
            ])
            ->orderBy('checkin_date','asc')
            ->get();
        return view('reservation.index',compact('calendars','freedays','reservations'));
    }

    //予約作成画面

    public function create(Request $request)
    {
        $calendar_id = $request->calendar_id;
        $fr = $request->fr; //フリーディID

        //カレンダーの予約生成が失敗した時
        if($calendar_id) {
            //FIXDAY予約
            $calendar = Calendar::findOrFail($calendar_id);
            return view('reservation.create',compact('calendar'));
        }
        //フリーディの予約生成が失敗した時
        if($fr){
            $freeday = Freeday::findOrfail($fr);
            return view('reservation.create',compact('freeday'));
        }
        abort(404);
    }

    public function service(Request $request)
    {
        $user = Auth::user();

        // セッションから予約情報取得
        $reservation_data = session('reservation_data');

        if (!$reservation_data) {
            return redirect()->route('reservation.index');
        }

        // 事前予約可能なサービス取得
        $services = Service::where('hotel_id', $reservation_data['hotel_id'])
            ->where('status', 1)
            ->where('tab', 1) // 事前予約タブ
            ->orderBy('sort', 'asc')
            ->with('serviceOptions')
            ->get();

        // 一時保存済みサービス取得
        $tmp_orders = TmpOrderDetail::where('user_id',$user->id)
            ->with(['service','serviceOption'])
            ->get();

        return view('reservation.service',compact('services','tmp_orders','reservation_data'));
    }

    //カートに追加
    public function cart_add(Request $request)
    {
        $validated = $request->validate([
           'service_id'=>'required|exists:services,id',
           'service_option_id'=>'nullable|exists:service_options,id',
            'quantity'=>'required|integer|min:1',
        ]);

        $service = Service::findOrFail($request->service_id);

        //在庫チェック
        if($service->stock > 0 && $service->stock < $request->quantity) {
            return back()->withErrors(['quantity'=>'在庫が不足してます']);
        }
        // 価格計算
        $price = $service->price;

        if($request->service_option_id){
            $option = ServiceOption::findOrFail($request->service_option_id);
            $price += $option->price;
        }

        // 一時保存
        TmpOrderDetail::create([
            'user_id' => Auth::id(),
            'service_id' => $service->id,
            'service_option_id' => $request->service_option_id,
            'price'=> $price,
            'quantity'=> $request->quantity,
            'total_price'=> $price * $request->quantity,
            'type'=> 1,
            ]);

        return redirect()->route('reservation.cart');
    }


    //カート画面
    public function cart(Request $request)
    {
        $user = Auth::user();
        $reservation_data = session('reservation_data');

        $tmp_orders = TmpOrderDetail::where('user_id',$user->id)
            ->with(['service','serviceOption'])
            ->get();

        $total_price  = $tmp_orders->sum('total_price');

        return view('reservation.cart',compact('tmp_orders','total_price','reservation_data'));
    }
    //カート削除
    public function cart_delete(TempOrderDetail $tmp_order_detail)
    {
        if($tmp_order_detail->user_id != Auth::id()){
            abort(403);
        }
        $tmp_order_detail->delete();
        return redirect()->route('reservation.cart');
    }
    //予約確認画面
    public function confirm(Request $request)
    {
        $user = Auth::user();
        $reservation_data = session('reservation_data');

        if(!$reservation_data){
            return redirect()->route('reservation.index');
        }

        $tmp_orders = TmpOrderDetail::where('user_id',$user->id)
            ->with(['service','serviceOption'])
            ->get();

        $service_total = $tmp_orders->sum('total_price');
        $total_price = $service_total;

        return view('reservation.confirm',compact(
           'reservation_data',
           'tmp_orders',
           'service_total',
            'total_price',
        ));
    }
    public function store(Request $request){
        DB::beginTranscation();
        try{
            $user = Auth::user();
            $reservation_data = session('reservation_data');

            if(!$reservation_data){
                throw new \Exception('予約情報がありません');
            }

            //予約作成
            $reservation = Reservation::create([
                'hotel_id'=>$reservation_data['hotel_id'],
                'user_id'=>$user->id,
                'owner_id' => $user->type == 2 ? $user->id : $user->user_id,
                'calendar_id'=>$reservation_data['calendar_id'] ?? null,
                'checkin_date'=>$reservation_data['checkin_date'],
                'checkout_date'=>$reservation_data['checkout_date'],
                'days'=>$reservation_data['days'],
                'adult'=>$reservation_data['adult'],
                'child' => $reservation_data['child'] ?? 0,
                'dog' => $reservation_data['dog'] ?? 0,
                'note' => $request->note,
                'payment' => $request->payment ?? 0,
                'status' => ReservationConst::STATUS_UNDER_RESERVATION,
            ]);
            // サービス注文作成
            $tmp_orders = TmpOrderDetail::where('user_id',$user->id)->get();
            foreach($tmp_orders as $tmp){
                $order = Order::create([
                    'user_id'=>$user->id,
                    'reservation_id'=>$reservation->id,
                    'service_id'=>$tmp->service_id,
                    'price'=>$tmp->price,
                    'quantity'=>$tmp->quantity,
                    'total_price'=>$tmp->total_price,
                    'payment'=>$request->payment ?? 0,
                    'type'=>1,
                    'status'=>1
                ]);
                OrderDetail::create([
                    'order_id'=>$order->id,
                    'service_id'=>$tmp->service_id,
                    'service_option_id'=>$tmp->service_option_id,
                    'price'=>$tmp->price,
                    'quantity'=> $tmp->quantity,
                    'total_price' => $tmp->total_price,
                ]);

            }

            // カレンダーステータス更新
            if($reservation_data['calendar_id']){
                Calendar::where('id',$reservation_data['calendar_id'])
                    ->update(['status' => ReservationConst::STATUS_UNDER_RESERVATION]);
            }
            // フリーデイの場合は残数減少
            if(isset($reservation_data['freeday_id'])){
                $freeday = Freeday::findOrFail($reservation_data['freeday_id']);
                $freeday->decrement('freedays',$reservation_data['days']);
            }
            // 一時データ削除
            TmpOrderDetail::where('user_id',$user->id)->delete();
            session()->forget('reservation_data');

            // 予約ログ保存
            ReservationLog::create([
               'reservation_id'=>$reservation->id,
               'user_id'=>$user->id,
               'action'=>'create',
               'data'=>json_encode($reservation),
            ]);

            DB::commit();

            return redirect()->route('reservation.complete')
                ->with('reservation_id',$reservation->id);

            }catch(\Exception $e){
            DB::rollBack();
            \Log::error('Reservation Error: ' . $e->getMessage());
            return back()->withErrors(['error' => '予約に失敗しました: ' . $e->getMessage()]);
        }
    }
}

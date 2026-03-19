<?php
// app/Http/Controllers/ServiceController.php

namespace App\Http\Controllers;

use App\Models\Service;

use App\Models\ServiceOption;

use App\Models\Cart;

use App\Models\CartDetail;

use App\Models\Reservation;

use Illuminate\Http\Request;

use Auth;

class ServiceController extends Controller
{
    /**
     * サービス一覧
     */
    public function index()
    {
        // 現地注文可能なサービス
        $services = Service::where('status', 1)
            ->where('tab', 2) // 現地注文タブ
            ->orderBy('sort', 'asc')
            ->with('serviceOptions')
            ->get();

        // 最新の予約取得
        $last_reservation = Reservation::getLastReservation();

        return view('services.index', compact('services', 'last_reservation'));
    }

    /**
     * サービス詳細・注文画面
     */
    public function show(Service $service, Request $request)
    {
        $service->load('serviceOptions');

        // 予約情報（任意）
        $reservation_id = $request->input('reservation_id');
        $reservation = null;

        if ($reservation_id) {
            $reservation = Reservation::findOrFail($reservation_id);

            if ($reservation->user_id != Auth::id()) {
                abort(403);
            }
        }

        return view('services.show', compact('service', 'reservation'));
    }

    /**
     * カート追加
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'service_option_id' => 'nullable|exists:service_options,id',
            'quantity' => 'required|integer|min:1',
            'reservation_id' => 'nullable|exists:reservations,id',
        ]);

        $service = Service::findOrFail($request->service_id);
        $user = Auth::user();

        // 最小注文数チェック
        if ($request->quantity < $service->minimum) {
            return back()->withErrors([
                'quantity' => "最小注文数は{$service->minimum}{$service->unit}です"
            ]);
        }

        // 在庫チェック
        if ($service->stock > 0 && $service->stock < $request->quantity) {
            return back()->withErrors(['quantity' => '在庫が不足しています']);
        }

        // 価格計算
        $price = $service->price;
        if ($request->service_option_id) {
            $option = ServiceOption::findOrFail($request->service_option_id);
            $price += $option->price;
        }

        // カート取得または作成
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // カート明細追加
        CartDetail::create([
            'cart_id' => $cart->id,
            'service_id' => $service->id,
            'service_option_id' => $request->service_option_id,
            'price' => $price,
            'quantity' => $request->quantity,
            'total_price' => $price * $request->quantity,
        ]);

        return redirect()->route('cart.index')
            ->with('success', 'カートに追加しました');
    }
}

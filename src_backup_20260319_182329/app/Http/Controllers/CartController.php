<?php
// app/Http/Controllers/CartController.php

namespace App\Http\Controllers;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Auth;
use DB;

class CartController extends Controller
{
    /**
     * カート一覧
     */
    public function index()
    {
        $user = Auth::user();

        $cart = Cart::with([
            'cartDetails.service',
            'cartDetails.serviceOption'
        ])->where('user_id', $user->id)->first();

        if (!$cart) {
            return view('cart.index', ['cart' => null, 'total_price' => 0]);
        }

        $total_price = $cart->cartDetails->sum('total_price');

        // 最新予約
        $last_reservation = Reservation::getLastReservation();

        return view('cart.index', compact('cart', 'total_price', 'last_reservation'));
    }

    /**
     * カート明細削除
     */
    public function delete(CartDetail $cart_detail)
    {
        if ($cart_detail->cart->user_id != Auth::id()) {
            abort(403);
        }

        $cart_detail->delete();

        return redirect()->route('cart.index')
            ->with('success', 'カートから削除しました');
    }

    /**
     * 確認画面
     */
    public function confirm(Cart $cart)
    {
        if ($cart->user_id != Auth::id()) {
            abort(403);
        }

        $cart->load([
            'cartDetails.service',
            'cartDetails.serviceOption'
        ]);

        $total_price = $cart->cartDetails->sum('total_price');

        $last_reservation = Reservation::getLastReservation();

        return view('cart.confirm', compact('cart', 'total_price', 'last_reservation'));
    }

    /**
     * 注文確定（決済なし版）
     */
    public function store(Cart $cart, Request $request)
    {
        if ($cart->user_id != Auth::id()) {
            abort(403);
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();

            // 各カート明細を注文に変換
            foreach ($cart->cartDetails as $detail) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'reservation_id' => $request->reservation_id,
                    'service_id' => $detail->service_id,
                    'price' => $detail->price,
                    'quantity' => $detail->quantity,
                    'total_price' => $detail->total_price,
                    'payment' => $request->payment ?? 0,
                    'type' => 1,
                    'status' => 1,
                ]);

                OrderDetail::create([
                    'order_id' => $order->id,
                    'service_id' => $detail->service_id,
                    'service_option_id' => $detail->service_option_id,
                    'price' => $detail->price,
                    'quantity' => $detail->quantity,
                    'total_price' => $detail->total_price,
                ]);
            }

            // カート削除
            $cart->cartDetails()->delete();
            $cart->delete();

            DB::commit();

            return redirect()->route('cart.complete')
                ->with('success', '注文が完了しました');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Cart Order Error: ' . $e->getMessage());
            return back()->withErrors(['error' => '注文に失敗しました']);
        }
    }

    /**
     * 完了画面
     */
    public function complete()
    {
        return view('cart.complete');
    }
}

<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentApiService
{
    /**
     * 画像の「エラーハンドリング」方針に合わせて、タイムアウト/レスポンス異常/ステータス異常を例外にします。
     *
     * 実際の決済APIの仕様が未確定のため、呼び出し先URL/キーは env から受け取ります。
     */
    public function charge(array $payload): array
    {
        $url = (string) env('PAYMENT_API_URL', '');
        $apiKey = (string) env('PAYMENT_API_KEY', '');

        if ($url === '') {
            throw new \RuntimeException('決済APIのURLが未設定です（PAYMENT_API_URL）');
        }

        $log = [
            'user_id' => $payload['user_id'] ?? null,
            'reservation_id' => $payload['reservation_id'] ?? null,
            'order_id' => $payload['order_id'] ?? null,
            'amount' => $payload['amount'] ?? null,
        ];

        try {
            $response = Http::timeout((int) env('PAYMENT_API_TIMEOUT', 15))
                ->retry(0, 0)
                ->withHeaders(array_filter([
                    'Authorization' => $apiKey !== '' ? "Bearer {$apiKey}" : null,
                    'Accept' => 'application/json',
                ]))
                ->post($url, $payload);

            // タイムアウトに近い「空レスポンス」や不正JSONを弾く
            $data = $response->json();
            if (!is_array($data)) {
                throw new \RuntimeException('決済APIからレスポンスが読み取れません');
            }

            // 画像の「結果判定」：ステータス/結果コードで異常なら例外
            $itemStatus = $data['item_status'] ?? ($data['status'] ?? null);
            $resultCode = $data['item_result_code'] ?? ($data['result_code'] ?? null);
            $errMessage = $data['err_message'] ?? ($data['message'] ?? null);

            Log::info('payment_api.response', $log + [
                'http_status' => $response->status(),
                'item_status' => $itemStatus,
                'item_result_code' => $resultCode,
            ]);

            if ($response->failed()) {
                // 4xx/5xx は例外
                throw new \RuntimeException('決済APIがエラーを返しました');
            }

            if ($itemStatus !== null && (string) $itemStatus !== 'success') {
                throw new \RuntimeException("決済エラー（status={$itemStatus}, code={$resultCode}）");
            }

            if ($resultCode !== null && (string) $resultCode !== '0') {
                throw new \RuntimeException("決済エラー（code={$resultCode}）");
            }

            if ($errMessage) {
                // 成功扱いでもメッセージがエラーなら弾く（安全側）
                if (stripos((string) $errMessage, 'error') !== false) {
                    throw new \RuntimeException("決済エラー（message={$errMessage}）");
                }
            }

            return $data;
        } catch (ConnectionException $e) {
            Log::warning('payment_api.timeout_or_connection', $log + ['error' => $e->getMessage()]);
            throw new \RuntimeException('決済APIからレスポンスが返りませんでした');
        } catch (RequestException $e) {
            Log::warning('payment_api.request_exception', $log + ['error' => $e->getMessage()]);
            throw new \RuntimeException('決済APIリクエストに失敗しました');
        } catch (\Throwable $e) {
            Log::warning('payment_api.unexpected', $log + ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}


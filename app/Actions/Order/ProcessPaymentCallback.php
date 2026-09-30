<?php

namespace App\Actions\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class ProcessPaymentCallback
{
    public function execute(array $payload, ?string $signature)
    {
        if (! isset($payload['order_id']) || ! isset($payload['status'])) {
            return ['error' => 'Invalid payload format.', 'code' => 400];
        }

        $secretKey = config('services.payment.secret_key');
        $stringToHash = (string) $payload['order_id'].(string) $payload['status'];
        $expectedSignature = hash_hmac('sha256', $stringToHash, $secretKey);

        Log::info('Debug Signature:', [
            'payload_string' => $payload['order_id'].$payload['status'],
            'secret_key_used' => $secretKey,
            'generated_signature' => $expectedSignature,
            'incoming_signature' => $signature,
        ]);

        if (! hash_equals($expectedSignature, (string) $signature)) {
            return ['error' => 'Invalid signature.', 'code' => 401];
        }

        $order = Order::find($payload['order_id']);
        if (! $order) {
            return ['error' => 'Order not found.', 'code' => 404];
        }

        if ($payload['status'] === 'success') {
            $order->update(['status' => 'paid']);
        } elseif ($payload['status'] === 'failed' || $payload['status'] === 'expired') {
            $order->update(['status' => 'cancelled']);
        }

        return $order;
    }
}

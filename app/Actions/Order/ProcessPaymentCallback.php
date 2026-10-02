<?php

namespace App\Actions\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class ProcessPaymentCallback
{
    public function execute(array $payload, ?string $signature = null)
    {
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $incomingSignature = $payload['signature_key'] ?? null;

        if (! $orderId || ! $statusCode || ! $grossAmount || ! $incomingSignature) {
            return ['error' => 'Invalid payload format from Midtrans.', 'code' => 400];
        }

        $serverKey = config('services.midtrans.server_key');

        $stringToHash = $orderId.$statusCode.$grossAmount.$serverKey;
        $expectedSignature = hash('sha512', $stringToHash);

        Log::info('Midtrans Webhook Debug:', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'generated_signature' => $expectedSignature,
            'incoming_signature' => $incomingSignature,
        ]);

        if (! hash_equals($expectedSignature, (string) $incomingSignature)) {
            return ['error' => 'Invalid signature key.', 'code' => 401];
        }

        $order = Order::where('order_number', $orderId)->first();

        if (! $order) {
            return ['error' => 'Order not found.', 'code' => 404];
        }

        if (in_array($order->status, ['paid', 'cancelled'])) {
            return $order;
        }

        $transactionStatus = $payload['transaction_status'] ?? '';
        $type = $payload['payment_type'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? '';

        if ($transactionStatus == 'capture') {
            if ($type == 'credit_card') {
                if ($fraudStatus == 'challenge') {
                    $order->update(['status' => 'pending']);
                } else {
                    $order->update(['status' => 'paid']);
                }
            }
        } elseif ($transactionStatus == 'settlement') {
            $order->update(['status' => 'paid']);
        } elseif ($transactionStatus == 'pending') {
            $order->update(['status' => 'pending']);
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $order->update(['status' => 'cancelled']);
            $this->restockProduct($order);
        }

        return $order;
    }

    private function restockProduct(Order $order)
    {
        $order->load('orderItems.product');

        foreach ($order->orderItems as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }
    }
}

<?php

namespace App\Http\Controllers\Webhook;

use App\Actions\Order\ProcessPaymentCallback;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, ProcessPaymentCallback $processPayment)
    {
        Log::info('Payment Webhook Received', $request->all());

        $payload = $request->all();
        $signature = $request->header('X-Callback-Signature');

        $result = $processPayment->execute($payload, $signature);

        if (isset($result['error'])) {
            Log::warning('Payment Webhook Failed', ['reason' => $result['error']]);
            return response()->json(['message' => $result['error']], $result['code']);
        }

        return response()->json(['message' => 'Payment webhook processed successfully.']);
    }
}

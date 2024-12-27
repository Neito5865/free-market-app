<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Address;
use App\Models\Purchase;
use App\Models\Item;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Log::info('Webhook received', ['payload' => $request->all()]);

        try {
            $payload = $request->getContent();
            $sigHeader = $request->header('Stripe-Signature');
            $secret = config('services.stripe.webhook_secret');

            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $secret
            );

            Log::info('Webhook successfully verified', ['event' => $event]);

        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        // 決済成功イベントを処理
        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;

            // コンビニ払いが完了しているかチェック
            if ($session->payment_status === 'paid') {
                $this->handleSuccessfulPayment($session);
                Log::info('Payment processing completed', ['session' => $session]);
            } else {
                Log::warning('Payment status is not paid', ['status' => $session->payment_status]);
            }
        }
        return response('Webhook Handled', 200);
    }

    protected function handleSuccessfulPayment($session)
    {
        // セッションIDなどを元に購入データを保存
        $itemId = $session->metadata->item_id ?? null;
        $userId = $session->metadata->user_id ?? null;

        $item = Item::find($itemId);

        if ($item) {
            $address = Address::create([
                'name' => $session->metadata->name,
                'post_code' => $session->metadata->post_code,
                'address' => $session->metadata->address,
                'building' => $session->metadata->building,
            ]);

            $purchase = Purchase::create([
                'user_id' => $userId,
                'item_id' => $item->id,
                'address_id' => $address->id,
                'payment_method' => 1,
            ]);

            Log::info('Processing successful payment', ['session_id' => $session->id]);
        } else {
            Log::error("Item not found for ID $itemId");
        }
    }
}

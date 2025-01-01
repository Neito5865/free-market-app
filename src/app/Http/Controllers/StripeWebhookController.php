<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Address;
use App\Models\Purchase;
use App\Models\Item;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

    try {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        // Webhookの署名検証
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sigHeader, $secret
        );

        // イベントタイプごとの処理
        if ($event->type === 'checkout.session.completed') {
            $this->handleSuccessfulPayment($event->data->object);
        } elseif ($event->type === 'payment_intent.succeeded') {
            // 必要なら他のイベントも処理
            Log::info('Payment intent succeeded', ['event' => $event]);
        }

    } catch (\UnexpectedValueException $e) {
        Log::error('Invalid payload', ['error' => $e->getMessage()]);
        return response('Invalid payload', 400);
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        Log::error('Invalid signature', ['error' => $e->getMessage()]);
        return response('Invalid signature', 400);
    } catch (\Exception $e) {
        Log::error('Webhook processing error', ['error' => $e->getMessage()]);
        return response('Webhook processing error', 400);
    }

    return response('Webhook Handled', 200);
    }

    protected function handleSuccessfulPayment($session)
    {
        Log::info('Session Object', ['session' => $session]);

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
                'payment_method' => 1, // 支払い方法
            ]);

            session()->forget('selected_address');
        } else {
            Log::error("Item not found for ID $itemId");
        }
    }
}

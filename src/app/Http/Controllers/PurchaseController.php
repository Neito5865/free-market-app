<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Address;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    public function create($id)
    {
        $user = Auth::user();
        $item = Item::find($id);
        if (!$item) {
            return response()->view('errors.error-page', ['message' => '該当の商品が存在しません。'], 404);
        }
        $selectedAddress = session('selected_address', null);

        return view('purchase.create', compact('item', 'user', 'selectedAddress'));
    }

    public function payment(PurchaseRequest $request, $id)
    {
        $user = Auth::user();
        $item = Item::find($id);
        if (!$item) {
            return response()->view('errors.error-page', ['message' => '該当の商品が見つかりません。'], 404);
        }

        session(['selected_payment_method' => $request->input('payment_method')]);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $selectedAddress = session('selected_address', null);

        if (!$selectedAddress) {
            $selectedAddress = [
                'name' => $user->name,
                'post_code' => $user->post_code,
                'address' => $user->address,
                'building' => $user->building,
            ];
        }

        $metadata = [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'name' => $selectedAddress['name'],
            'post_code' => $selectedAddress['post_code'],
            'address' => $selectedAddress['address'],
            'building' => $selectedAddress['building'],
        ];

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card', 'konbini'],
                'payment_method_options' => [
                    'konbini' => [
                        'expires_after_days' => 7,
                    ],
                ],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $item->name,
                        ],
                        'unit_amount' => $item->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('purchase.completed', ['id' => $id]),
                'cancel_url' => route('purchase.create', ['id' => $id]),
                'metadata' => $metadata,
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            return back()->with('error', '決済に失敗しました: ' . $e->getMessage());
        }
    }

    public function completed(Request $request, $id)
    {
        $user = Auth::user();
        $item = Item::find($id);
        if (!$item) {
            return response()->view('errors.error-page', ['message' => '該当の商品が存在しません。'], 404);
        }

        $addressData = session('selected_address') ?? [
            'name' => $user->name,
            'post_code' => $user->post_code,
            'address' => $user->address,
            'building' => $user->building,
        ];

        $paymentMethod = session('selected_payment_method');

        try{
            DB::beginTransaction();

            $address = Address::create([
                'name' => $addressData['name'],
                'post_code' => $addressData['post_code'],
                'address' => $addressData['address'],
                'building' => $addressData['building'],
            ]);

            $purchase = Purchase::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'address_id' => $address->id,
                'payment_method' => $paymentMethod,
            ]);

            DB::commit();

            session()->forget('selected_address');
            session()->forget('selected_payment_method');

            return redirect()->route('item.index')->with('success', '購入が完了しました');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('購入処理中にエラーが発生しました' . $e->getMessage());

            return redirect()->route('item.index')->with('error', '購入処理中にエラーが発生しました。');
        }
    }
}

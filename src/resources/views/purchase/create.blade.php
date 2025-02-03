@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase/create.css') }}">
@endsection

@section('content')
    @if (count($errors) > 0)
        <div class="purchase-error">
            <ul class="alert-error">
                @foreach ($errors->all() as $error)
                    <li class="alert-error__item">{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="purchase__content">
        <div class="purchase__inner-f">
            <div class="purchase__left">
                <div class="purchase__section--item">
                    <div class="item__image">
                        <img src="{{ asset('storage/' . $item->image) }}" alt="商品画像">
                    </div>
                    <div class="item__text">
                        <div class="item__name">
                            <h2>{{ $item->name }}</h2>
                        </div>
                        <div class="item__price">
                            <p>&yen; <span>{{ $item->formatted_price }}</span></p>
                        </div>
                    </div>
                </div>
                <div class="purchase__section--paymentMethod">
                    <div class="payment__hading">
                        <h3>支払い方法</h3>
                    </div>
                    <div class="payment__select">
                        <select name="payment_method" id="payment-select">
                            <option value="" disabled {{ old('payment_method') ? '' : 'selected' }}>選択してください</option>
                            <option value="1" {{ old('payment_method') == '1' ? 'selected' : '' }}>コンビニ払い</option>
                            <option value="2" {{ old('payment_method') == '2' ? 'selected' : '' }}>カード支払い</option>
                        </select>
                        <div class="triangle"></div>
                    </div>
                </div>
                <div class="purchase__section--address">
                    <div class="address__heading-flex">
                        <div class="address__hading">
                            <h3>配送先</h3>
                        </div>
                        <div class="address__link--edit">
                            <a href="{{ route('address.create', $item->id) }}">変更する</a>
                        </div>
                    </div>
                    <div class="address__text">
                        @if (session('selected_address'))
                            <p>{{ session('selected_address.name') }}</p>
                            <p>〒 {{ session('selected_address.post_code') }}</p>
                            <p>{{ session('selected_address.address') }}</p>
                            <p>{{ session('selected_address.building') }}</p>
                        @elseif ($user->post_code && $user->address && $user->building)
                            <p>{{ $user->name }}</p>
                            <p>〒 {{ $user->post_code }}</p>
                            <p>{{ $user->address }}</p>
                            <p>{{ $user->building }}</p>
                        @else
                            <p>配送先が未設定または不完全です。</p>
                            <p>「変更する」から配送先を登録してください。</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="purchase__right">
                <div class="price-payment-box">
                    <table>
                        <tr class="box__row">
                            <td class="box__label">商品代金</td>
                            <td class="box__value"><span class="box__value--yen">&yen; </span>{{ $item->price }}</td>
                        </tr>
                        <tr class="box__row">
                            <td class="box__label">支払い方法</td>
                            <td class="box__value" id="selected-payment-method">選択してください</td>
                        </tr>
                    </table>
                </div>
                <div class="purchase-form">
                    <form method="POST" action="{{ route('purchase.payment', $item->id) }}">
                        @csrf
                        <input type="hidden" name="payment_method" id="hidden-payment-method">
                        <input type="hidden" name="selected_address[name]" id="hidden-address-name" value="{{ session('selected_address.name', $user->name) }}">
                        <input type="hidden" name="selected_address[post_code]" id="hidden-address-post_code" value="{{ session('selected_address.post_code', $user->post_code) }}">
                        <input type="hidden" name="selected_address[address]" id="hidden-address-address" value="{{ session('selected_address.address', $user->address) }}">
                        <input type="hidden" name="selected_address[building]" id="hidden-address-building" value="{{ session('selected_address.building', $user->building) }}">
                        <button class="purchase-form__btn" type="submit">購入する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const paymentSelect = document.getElementById('payment-select');
            const selectedPaymentMethod = document.getElementById('selected-payment-method');
            const hiddenPaymentMethod = document.getElementById('hidden-payment-method');

            let firstOption = paymentSelect.querySelector('option[value=""]');

            paymentSelect.addEventListener('focus', function () {
                if (firstOption) {
                    firstOption.remove(); // 「選択してください」を削除
                    firstOption = null;  // 一度削除したら再度削除しないようにする

                    // 選択状態をリセットして、再選択を強制する
                    paymentSelect.selectedIndex = -1;
                }
            });

            paymentSelect.addEventListener('change', function () {
                const selectedValue = this.value;
                let paymentText = '';

                if (selectedValue === '1') {
                    paymentText = 'コンビニ払い';
                } else if (selectedValue === '2') {
                    paymentText = 'カード支払い';
                }

                selectedPaymentMethod.textContent = paymentText;
                hiddenPaymentMethod.value = selectedValue;
            });

            if (paymentSelect.value) {
                paymentSelect.dispatchEvent(new Event('change'));
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const addressFields = {
                name: document.getElementById('hidden-address-name'),
                postCode: document.getElementById('hidden-address-post_code'),
                address: document.getElementById('hidden-address-address'),
                building: document.getElementById('hidden-address-building'),
            };

            const selectedAddress = @json(session('selected_address'));

            if (selectedAddress) {
                addressFields.name.value = selectedAddress.name;
                addressFields.postCode.value = selectedAddress.post_code;
                addressFields.address.value = selectedAddress.address;
                addressFields.building.value = selectedAddress.building;
            }
        });
    </script>
@endsection

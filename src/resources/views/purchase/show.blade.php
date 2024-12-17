@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase/show.css') }}">
@endsection

@section('content')
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
                            <option value="" disabled selected>選択してください</option>
                            <option value="1">コンビニ払い</option>
                            <option value="2">カード支払い</option>
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
                        @elseif ($user->post_code || $user->address)
                            <p>{{ $user->name }}</p>
                            <p>〒 {{ $user->post_code ?? '未登録'}}</p>
                            <p>{{ $user->address ?? '未登録'}}</p>
                            <p>{{ $user->building ?? ''}}</p>
                        @else
                            <p>配送先が未設定です。</p>
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
                    <form method="" action="">
                        <button class="purchase-form__btn" type="submit">購入する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.getElementById('payment-select').addEventListener('click', function() {
            const firstOption = this.querySelector('option[value=""]');
            if (firstOption) {
                firstOption.style.display = 'none';
            }
        });

        document.getElementById('payment-select').addEventListener('change', function() {
            const selectedValue = this.value;

            let paymentText = '';
            if (selectedValue === '1') {
                paymentText = 'コンビニ払い';
            } else if (selectedValue === '2') {
                paymentText = 'カード支払い';
            } else {
                paymentText = '選択してください';
            }

            document.getElementById('selected-payment-method').textContent = paymentText;
        })
    </script>
@endsection

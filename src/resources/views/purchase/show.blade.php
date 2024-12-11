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
                            <p>&yen;<span>{{ $item->formatted_price }}</span></p>
                        </div>
                    </div>
                </div>
                <div class="purchase__section--paymentMethod">
                    <div class="payment__hading">
                        <h3>支払い方法</h3>
                    </div>
                    <div class="payment__select"></div>
                </div>
                <div class="purchase__section--address">
                    <div class="address__heading-flex">
                        <div class="address__hading"></div>
                        <div class="address__link--edit"></div>
                    </div>
                    <div class="address__text">
                        <p></p>
                        <p></p>
                    </div>
                </div>
            </div>
            <div class="purchase__right"></div>
        </div>
    </div>
@endsection

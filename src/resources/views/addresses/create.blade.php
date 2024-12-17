@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/addresses/create.css') }}">
@endsection

@section('content')
    <div class="address__container">
        <div class="address__heading">
            <h2>住所の変更</h2>
        </div>
        <div class="address__form">
            <form method="POST" action="">
                @csrf
                <div class="address-form__group">
                    <div class="address-form__group--content">
                        <label class="address-form__label" for="name">お名前</label>
                        <input class="address-form__input" type="text" name="name" id="name" value="{{ old('name') }}">
                    </div>
                    <div class="address-form__error">
                        @error('name')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="address-form__group">
                    <div class="address-form__group--content">
                        <label class="address-form__label" for="post_code">郵便番号</label>
                        <input class="address-form__input" type="text" name="post_code" id="post_code" value="{{ old('post_code') }}">
                    </div>
                    <div class="address-form__error">
                        @error('post_code')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="address-form__group">
                    <div class="address-form__group--content">
                        <label class="address-form__label" for="address">住所</label>
                        <input class="address-form__input" type="text" name="address" id="address" value="{{ old('address') }}">
                    </div>
                    <div class="address-form__error">
                        @error('address')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="address-form__group">
                    <div class="address-form__group--content">
                        <label class="address-form__label" for="building">建物名</label>
                        <input class="address-form__input" type="text" name="building" id="building" value="{{ old('building') }}">
                    </div>
                    <div class="address-form__error">
                        @error('building')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="address-form__button">
                    <input class="address-form__button-submit" type="submit" value="更新する">
                </div>
            </form>
        </div>
    </div>
@endsection

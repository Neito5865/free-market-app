@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
    @include('session_message.session_message')
    <div class="login__container">
        <div class="login__heading">
            <h2>ログイン</h2>
        </div>
        <div class="login__form">
            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf
                <div class="login-form__group">
                    <div class="login-form__group--content">
                        <label class="login-form__label" for="email">メールアドレス</label>
                        <input class="login-form__input" type="email" name="email" id="email" value="{{ old('email')}}">
                    </div>
                    <div class="login-form__error">
                        @error('email')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="login-form__group">
                    <div class="login-form__group--content">
                        <label class="login-form__label" for="password">パスワード</label>
                        <input class="login-form__input" type="password" name="password" id="password" value="{{ old('password')}}">
                    </div>
                    <div class="login-form__error">
                        @error('password')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="login-form__button">
                    <input class="login-form__button-submit" type="submit" value="ログインする">
                </div>
            </form>
        </div>
        <div class="login__link">
            <a href="{{ route('register') }}">会員登録はこちら</a>
        </div>
    </div>
@endsection

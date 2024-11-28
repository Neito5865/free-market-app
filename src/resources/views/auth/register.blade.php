@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
    <div class="register__container">
        <div class="register__heading">
            <h2>会員登録</h2>
        </div>
        <div class="register__form">
            <form method="POST" action="{{ route('register') }}" novalidate>
                @csrf
                <div class="register-form__group">
                    <div class="register-form__group--content">
                        <label class="register-form__label" for="name">ユーザー名</label>
                        <input class="register-form__input" type="text" name="name" id="name" value="{{ old('name')}}">
                    </div>
                    <div class="register-form__error">
                        @error('name')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="register-form__group">
                    <div class="register-form__group--content">
                        <label class="register-form__label" for="email">メールアドレス</label>
                        <input class="register-form__input" type="email" name="email" id="email" value="{{ old('email')}}">
                    </div>
                    <div class="register-form__error">
                        @error('email')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="register-form__group">
                    <div class="register-form__group--content">
                        <label class="register-form__label" for="password">パスワード</label>
                        <input class="register-form__input" type="password" name="password" id="password" value="{{ old('password')}}">
                    </div>
                    <div class="register-form__error">
                        @error('password')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="register-form__group">
                    <div class="register-form__group--content">
                        <label class="register-form__label" for="password_confirmation">確認用パスワード</label>
                        <input class="register-form__input" type="password" name="password_confirmation" id="password_confirmation" value="{{ old('password_confirmation')}}">
                    </div>
                    <div class="register-form__error">
                        @error('password_confirmation')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="register-form__button">
                    <input class="register-form__button-submit" type="submit" value="登録する">
                </div>
            </form>
        </div>
        <div class="register__link">
            <a href="{{ route('login') }}">ログインはこちら</a>
        </div>
    </div>
@endsection

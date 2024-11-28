@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
@endsection

@section('content')
    <div class="verify__content">
        <div class="verify__heading">
            <h2>認証メールを送信しました</h2>
        </div>
        <div class="verify__text">
            @if (session('resent'))
                <div class="alert alert-success" role="alert">
                    {{ __('A fresh verification link has been sent to your email address.') }}
                </div>
            @endif

            <p>あなたの登録メールアドレスへ本人確認のための認証メールが送信されました。<br>操作を続ける前に、認証メール本文内のリンクをクリックしてください。</p>

            <p>認証メールが確認できない場合は、以下のボタンで再送してください。</p>
            <form class="verify-form" method="POST" action="{{ route('verification.send') }}">
            @csrf
                <input class="verify-submit" type="submit" value="{{ __('認証メールを再送する') }}">
            </form>
            <div class="backtop">
                <a class="backtop-link" href="{{ route('login') }}">ログインページへ戻る</a>
            </div>
        </div>
    </div>
@endsection

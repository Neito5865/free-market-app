@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/users/show.css') }}">
@endsection

@section('content')
    @if (session('successMessage'))
        <div class="message__success">
            {{ session('successMessage') }}
        </div>
    @endif
    <div class="mypage__container">
        <div class="profile__content">
            <div class="profile-inner">
                <div class="profile__image">
                    <div class="profile-image-preview" id="profileImagePreview" style="background-image: url('{{ $user->image ? asset('storage/' . $user->image) : "" }}');"></div>
                </div>
                <div class="profile__name">
                    <p>{{ $user->name }}</p>
                </div>
            </div>
            <div class="profile__btn--edit">
                <a href="{{ route('user.edit') }}">プロフィールを編集</a>
            </div>
        </div>
        <div class="tabs">
            <div class="tabs__labels">
                <a href="{{ url('/mypage') }}?tab=sell" class="tab {{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a>
                <a href="{{ url('/mypage') }}?tab=buy" class="tab {{ $tab === 'buy' ? 'active' : '' }}">購入した商品</a>
            </div>
            <div class="item-list">
                @include('items.items')
            </div>
        </div>
    </div>
@endsection

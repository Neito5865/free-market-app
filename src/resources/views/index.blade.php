@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
    @include('session_message.session_message')
    <div class="index__container">
        <div class="tabs">
            <div class="tabs__labels">
                <a href="{{ url('/') }}?page=recommend&keyword={{ request('keyword') }}" class="tab {{ $page === 'recommend' ? 'active' : '' }}">おすすめ</a>
                <a href="{{ url('/') }}?page=mylist&keyword={{ request('keyword') }}" class="tab {{ $page === 'mylist' ? 'active' : '' }}">マイリスト</a>
            </div>
            <div class="item-list">
                @if ($items->isEmpty())
                    <p>該当する商品が見つかりませんでした。</p>
                @else
                    @include('items.items')
                @endif
            </div>
        </div>
    </div>
@endsection

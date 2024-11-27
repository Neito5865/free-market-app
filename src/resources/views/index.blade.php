@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
    <div class="index__container">
        <div class="tabs">
            <div class="tabs__labels">
                <a href="{{ url('/') }}" class="tab {{ $page === 'recommend' ? 'active' : '' }}">おすすめ</a>
                <a href="{{ url('/') }}?page=mylist" class="tab {{ $page === 'mylist' ? 'active' : '' }}">マイリスト</a>
            </div>
            <div class="item-list">
                @include('items.items')
            </div>
        </div>
    </div>
@endsection

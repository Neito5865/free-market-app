@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/errors/error-page.css') }}">
@endsection

@section('content')
    <div class="error-page__container">
        <h2>{{ $message }}</h2>
        <p><a href="{{ route('item.index')}}">トップページへ戻る</a></p>
    </div>
@endsection

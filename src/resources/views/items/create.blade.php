@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/create.css') }}">
@endsection

@section('content')
    <div class="item-create__container">
        <div class="item-create__header">
            <h2>商品の出品</h2>
        </div>
        <div class="item-create__form">
            <form method="" action="">
                @csrf
                <div class="item-create-form__group">
                    <div class="item-create-form__group--content">
                        <label class="item-create-form__label" for="image">商品画像</label>
                        <input class="item-create-form__input" type="file" name="image" id="image">
                    </div>
                    <div class="item-create-form__error">
                        @error('image')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="item-create-form__section">
                    <h3>商品の詳細</h3>
                </div>
                <div class="item-create-form__group">
                    <div class="item-create-form__group--content">
                        <label class="item-create-form__label" for="category">カテゴリー</label>
                        <input class="item-create-form__input" type="file" name="category" id="category">
                    </div>
                    <div class="item-create-form__error">
                        @error('category')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="item-create-form__group">
                    <div class="item-create-form__group--content">
                        <label class="item-create-form__label" for="condition">商品の状態</label>
                        <select class="item-create-form__select" name="condition_id" id="condition">
                            <option value="">選択してください</option>
                            <option value="">良好</option>
                            <option value="">テスト</option>
                        </select>
                    </div>
                    <div class="item-create-form__error">
                        @error('condition_id')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="item-create-form__section">
                    <h3>商品名と説明</h3>
                </div>
                <div class="item-create-form__group">
                    <div class="item-create-form__group--content">
                        <label class="item-create-form__label" for="name">商品名</label>
                        <input class="item-create-form__input" type="text" name="name" id="name">
                    </div>
                    <div class="item-create-form__error">
                        @error('name')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="item-create-form__group">
                    <div class="item-create-form__group--content">
                        <label class="item-create-form__label" for="description">商品の説明</label>
                        <textarea class="item-create-form__textarea" name="description" id="description">{{ old('description') }}</textarea>
                    </div>
                    <div class="item-create-form__error">
                        @error('name')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

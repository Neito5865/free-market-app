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
                        <div id="preview-container" class="image-preview-area"></div>
                        <div class="image-preview-container">
                            <label for="image">
                                <p>画像を選択する</p>
                            </label>
                        </div>
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
                        <div class="category-selection">
                            @foreach ($categories as $category)
                                <label class="category-label">
                                    <input class="category-input" type="checkbox" name="categories[]" value="{{ $category->id }}">
                                    <span class="category-name">{{ $category->category }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="item-create-form__error">
                        @error('categories')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="item-create-form__group">
                    <div class="item-create-form__group--content">
                        <label class="item-create-form__label" for="condition">商品の状態</label>
                        <div class="condition-select">
                            <select class="item-create-form__select" name="condition_id" id="condition">
                                <option value="">選択してください</option>
                                @foreach ($conditions as $condition)
                                    <option value="{{ $condition->id }}" {{ old('condition') ? 'selected' : '' }}>{{ $condition->condition }}</option>
                                @endforeach
                            </select>
                            <div class="triangle"></div>
                        </div>
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
                        <input class="item-create-form__input" type="text" name="name" id="name" value="{{ old('name') }}">
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
                        @error('description')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="item-create-form__group">
                    <div class="item-create-form__group--content">
                        <label class="item-create-form__label" for="price">販売価格</label>
                        <input class="item-create-form__input" type="text" name="price" id="price">
                    </div>
                    <div class="item-create-form__error">
                        @error('price')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="item-create-form__btn">
                    <button class="item-create-form__btn--submit" type="submit">出品する</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.getElementById('image').addEventListener('change', function(event) {
            const previewContainer = document.getElementById('preview-container');
            const file = event.target.files[0];

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewContainer.innerHTML = '';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.innerHTML = '';
            }
        });
    </script>
@endsection

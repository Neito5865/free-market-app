@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection

@section('content')
<div class="item-show__container">
    <div class="item-show__content-f">
        <div class="item-show__content-left">
            <div class="item-show__image">
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
            </div>
        </div>
        <div class="item-show__content-right">
            <div class="content-right__inner">
                <div class="item-show__header">
                    <h2>{{ $item->name }}</h2>
                    <small class="item-show__brand">ブランド名</small>
                </div>
                <div class="item-show__price">
                    <p>&yen;<span>{{ $item->price}}</span>(税込)</p>
                </div>
                <div class="item-show__actions">
                    <div class="action-favorite">
                        @if(Auth::check())
                            @if(Auth::user()->isFavorite($item->id))
                                <form class="favorite-icon__form" method="POST" action="">
                                    @method('DELETE')
                                    @csrf
                                    <button class="favorite-icon__form-btn favorited" type="submit"><i class="fa-solid fa-star"></i></button>
                                </form>
                            @else
                                <form class="favorite-icon__form" method="POST" action="">
                                    @csrf
                                    <button class="favorite-icon__form-btn" type="submit"><i class="fa-regular fa-star"></i></button>
                                </form>
                            @endif
                        @else
                            <div class="favorite-icon">
                                <i class="fa-regular fa-star"></i>
                            </div>
                        @endif
                        <small class="favorite-count">1</small>
                    </div>
                    <div class="action-comment">
                        <div class="comment-icon">
                            <i class="fa-regular fa-comment"></i>
                        </div>
                        <small class="comment-count">1</small>
                    </div>
                </div>
                <div class="item-show__purchase-btn">
                    <a href="">購入手続きへ</a>
                </div>
                <div class="item-show__description">
                    <h3 class="description__header">商品説明</h3>
                    <p class="description__text">{!! nl2br(e($item->description)) !!}</p>
                </div>
                <div class="item-show__about">
                    <h3 class="about__header">商品の情報</h3>
                    <dl class="about__list">
                        <div class="about__list-item">
                            <dt>カテゴリー</dt>
                            <div class="about__list-item--category-container">
                                @foreach($categories as $category)
                                    <dd class="about__list-item--category">{{ $category->category }}</dd>
                                @endforeach
                            </div>
                        </div>
                        <div class="about__list-item">
                            <dt>商品の状態</dt>
                            <dd>{{ $item->condition->condition }}</dd>
                        </div>
                    </dl>
                </div>
                <div class="item-show__comment">
                    <h3 class="comment__header">コメント(1)</h3>
                    <div class="comment__about">
                        <div class="profile-img">
                            <img src="{{ asset('storage/profile-img/person-default.png') }}" alt="プロフィール画像">
                        </div>
                        <div class="comment__text">
                            <p>こちらにコメントが入ります。</p>
                        </div>
                    </div>
                    <div class="comment-form">
                        <h4 class="comment-form__header">商品へのコメント</h4>
                        <form method="" action="">
                            @csrf
                            <textarea class="comment-form__textarea" name="comment" id="comment"></textarea>
                            <input class="comment-form__submit" type="submit" value="コメントを送信する">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
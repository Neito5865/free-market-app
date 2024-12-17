@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/profile-form.css') }}">
@endsection

@section('content')
    <div class="profile__container">
        <div class="profile__heading">
            <h2>プロフィール設定</h2>
        </div>
        <div class="profile__form">
            <form method="POST" action="{{ route('user.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="profile-form__group">
                    <div class="profile-form__group--content">
                        <div class="profile-image-preview" id="profileImagePreview" style="background-image: url('{{ $user->image ? asset('storage/' . $user->image) : asset('storage/profile-img/person-default.png') }}');"></div>
                        <label class="profile-form__label--image" for="image">画像を選択する</label>
                        <input class="profile-form__input" id="image" type="file" name="image" accept="image/*">
                    </div>
                    <div class="profile-form__error">
                        @error('image')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="profile-form__group">
                    <div class="profile-form__group--content">
                        <label class="profile-form__label" for="name">ユーザー名</label>
                        <input class="profile-form__input" type="text" name="name" id="name" value="{{ old('name', $user->name) }}">
                    </div>
                    <div class="profile-form__error">
                        @error('name')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="profile-form__group">
                    <div class="profile-form__group--content">
                        <label class="profile-form__label" for="post_code">郵便番号</label>
                        <input class="profile-form__input" type="text" name="post_code" id="post_code" value="{{ old('post_code', $user->post_code) }}">
                    </div>
                    <div class="profile-form__error">
                        @error('post_code')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="profile-form__group">
                    <div class="profile-form__group--content">
                        <label class="profile-form__label" for="address">住所</label>
                        <input class="profile-form__input" type="text" name="address" id="address" value="{{ old('address', $user->address) }}">
                    </div>
                    <div class="profile-form__error">
                        @error('address')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="profile-form__group">
                    <div class="profile-form__group--content">
                        <label class="profile-form__label" for="building">建物名</label>
                        <input class="profile-form__input" type="text" name="building" id="building" value="{{ old('building', $user->building) }}">
                    </div>
                    <div class="profile-form__error">
                        @error('building')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="profile-form__button">
                    <input class="profile-form__button-submit" type="submit" value="更新する">
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.getElementById('image').addEventListener('change', function(event){
            const file = event.target.files[0];
            const preview = document.getElementById('profileImagePreview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.style.backgroundImage = `url('${e.target.result}')`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.backgroundImage = '';
            }
        });
    </script>
@endsection

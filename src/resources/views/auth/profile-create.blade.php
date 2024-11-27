@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/profile-create.css') }}">
@endsection

@section('content')
    <div class="profile-create__container">
        <div class="profile-create__heading">
            <h2>プロフィール設定</h2>
        </div>
        <div class="profile-create__form">
            <form method="POST" action="" enctype="multipart/form-data">
                @csrf
                <div class="profile-create-form__group">
                    <div class="profile-image-preview" id="profileImagePreview"></div>
                    <label class="profile-create-form__label--image" for="image">画像を選択する</label>
                    <input class="profile-create-form__input" id="image" type="file" name="image" accept="image/*">
                </div>
                <div class="profile-create-form__group">
                    <label class="profile-create-form__label" for="name">ユーザー名</label>
                    <input class="profile-create-form__input" type="text" name="name" id="name" value="{{ old('name')}}">
                </div>
                <div class="profile-create-form__group">
                    <label class="profile-create-form__label" for="postCode">郵便番号</label>
                    <input class="profile-create-form__input" type="text" name="postCode" id="postCode" value="{{ old('postCode')}}">
                </div>
                <div class="profile-create-form__group">
                    <label class="profile-create-form__label" for="address">住所</label>
                    <input class="profile-create-form__input" type="text" name="address" id="address" value="{{ old('address')}}">
                </div>
                <div class="profile-create-form__group">
                    <label class="profile-create-form__label" for="building">建物名</label>
                    <input class="profile-create-form__input" type="text" name="building" id="building" value="{{ old('building')}}">
                </div>
                <div class="profile-create-form__button">
                    <input class="profile-create-form__button-submit" type="submit" value="更新する">
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

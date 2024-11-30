<h3 class="comment__header">コメント(1)</h3>
@if($comments->isEmpty())
    <p>表示するコメントがありません。</p>
@else
    @foreach ($comments as $comment)
        <div class="comment__about">
            <div class="profile-img">
                <img src="{{ asset('storage/' . $comment->user->image) }}" alt="プロフィール画像">
            </div>
            <div class="profile-name">
                <p>{{ $comment->user->name }}</p>
            </div>
            <div class="comment__text">
                <p>{{ $comment->comment }}</p>
            </div>
        </div>
    @endforeach
@endif

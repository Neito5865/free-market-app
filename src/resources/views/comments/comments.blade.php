<h3 class="comment__header">コメント({{ $countComments }})</h3>
@if($comments->isEmpty())
    <p>表示するコメントがありません。</p>
@else
    @foreach ($comments as $comment)
        <div class="comment__about">
            <div class="profile-content-f">
                <div class="profile-img">
                    <img src="{{ asset('storage/' . $comment->user->image) }}" alt="プロフィール画像">
                </div>
                <div class="profile-name">
                    <p>{{ $comment->user->name }}</p>
                </div>
            </div>
            <div class="comment__text">
                <p>{!! nl2br(e($comment->comment)) !!}</p>
            </div>
        </div>
    @endforeach
@endif

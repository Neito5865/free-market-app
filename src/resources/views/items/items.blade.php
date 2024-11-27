<div class="item-list__content-f">
    @if($items->isEmpty())
        <p>表示する商品がありません。</p>
    @else
        @foreach ($items as $item)
                <div class="item-card">
                    <a href="{{ route('item.show', $item->id) }}">
                        <div class="item-card__img">
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                        </div>
                        <div class="item-card__name">
                            <p>{{ $item->name }}</p>
                        </div>
                    </a>
                </div>
        @endforeach
    @endif
</div>

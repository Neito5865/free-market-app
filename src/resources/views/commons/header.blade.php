<header class="header">
    <div class="header__container">
        <div class="header__content-f">
            <div class="header__logo">
                <h1><a href="{{ route('item.index') }}"><img src="/images/logo.svg" alt="COACHTECH"></a></h1>
            </div>
            <div class="header__search-form">
                <form class="search-form" method="GET" action="{{ route('item.index') }}">
                    @csrf
                    <input class="search-form__item-input" type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？">
                    <input type="hidden" name="page" value="{{ request('page', 'recommend') }}">
                </form>
            </div>
            <div class="header__nav">
                <nav>
                    <ul class="header-nav__items">
                        @if(Auth::check())
                            <li class="header-nav__item">
                                <form class="header-nav__logout-form" method="post" action="{{ route('logout') }}">
                                    @csrf
                                    <input class="header-nav__logout-form--submit" type="submit" value="ログアウト">
                                </form>
                            </li>
                            <li class="header-nav__item"><a class="header-nav__link" href="{{ route('user.show') }}">マイページ</a></li>
                            <li class="header-nav__item"><a class="header-nav__link" href="{{ route('item.create') }}">出品</a></li>
                        @else
                            <li class="header-nav__item"><a class="header-nav__link" href="{{ route('login') }}">ログイン</a></li>
                            <li class="header-nav__item"><a class="header-nav__link" href="{{ route('user.show') }}">マイページ</a></li>
                            <li class="header-nav__item"><a class="header-nav__link" href="{{ route('item.create') }}">出品</a></li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>

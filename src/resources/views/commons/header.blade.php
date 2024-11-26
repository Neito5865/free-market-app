<header class="header">
    <div class="header__container">
        <div class="header__content-f">
            <div class="header__logo">
                <h1><a href=""><img src="/images/logo.svg" alt="COACHTECH"></a></h1>
            </div>
            <div class="header__search-form">
                <form class="search-form" method="" action="">
                    @csrf
                    <input class="search-form__item-input" type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？">
                </form>
            </div>
            <div class="header__nav">
                <nav>
                    <ul class="header-nav__items">
                        @if(Auth::check())
                            <li class="header-nav__item">
                                <form class="header-nav__logout-form" method="" action="">
                                    @csrf
                                    <input class="header-nav__logout-form--submit" type="submit" value="ログアウト">
                                </form>
                            </li>
                            <li class="header-nav__item"><a class="header-nav__link" href="">マイページ</a></li>
                            <li class="header-nav__item"><a class="header-nav__link" href="">出品</a></li>
                        @else
                            <li class="header-nav__item"><a class="header-nav__link" href="">ログイン</a></li>
                            <li class="header-nav__item"><a class="header-nav__link" href="">マイページ</a></li>
                            <li class="header-nav__item"><a class="header-nav__link" href="">出品</a></li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>

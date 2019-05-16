<div id="header" v-cloak>
    <div>
        <div id="i-logo">
            <h1><a href="/"><img src="/web/img/logo.png"/></a></h1>
        </div>
        <div class="phone-menu" @click="menu_trig">
            <div :class="{open1: menuOpen}"></div>
            <div :class="{open2: menuOpen}"></div>
            <div :class="{open3: menuOpen}"></div>
        </div>
        <div id="i-nav">
<!--            {notempty name="user"}-->
            <span>
                <!-- {:url('user/index/index')} -->
                <a href="/">{$user['username']}</a> |
                <a href="/" class="exit">退出</a>
            </span>
<!--            {else/}-->
            <!-- {:url('user/user/login')}  {:url('user/user/reg')} -->
            <span><a href="">登录</a>|<a href="">注册</a></span>
<!--            {/notempty}-->
            <span><button @click="search_change" class="btn" :class="search ? 'btn-search':'btn-close'"></button></span>
            <div>
                <div v-if="!search">
                    <!-- {:url('search/index')} -->
                    <form action="" method="get">
                        <input type="text" name="keyword" placeholder="请输入书名或作者名" required>
                        <button class="btn btn-search"></button>
                    </form>
                </div>
                <ul v-if="search" id="i-menu">
<!--                    {nav id="vo"}-->
<!--                    <li {eq name="vo['current']" value="1"}class="curr"{/eq}><a href="/">{$vo['title']}</a></li>-->
<!--                    {/nav}-->
                </ul>
            </div>
        </div>

        <!-- 移动端 nav -->
        <div id="phone-nav" v-if="menuOpen">
            <ul>
                <!-- {:url('search/index')} -->
                <li><form action="" method="get">
                        <input type="text" name="keyword" placeholder="请输入书名或作者名" required>
                        <button class="btn btn-search"></button>
                    </form>
                </li>
<!--                {notempty name="user"}-->
                <span>
                    <a href="">{$user['username']}</a> |
                    <a href="" class="exit">退出</a>
                </span>
<!--                {else/}-->
                <!-- {:url('user/user/login')} -->
                <span><a href="">登录</a>|<a href="">注册</a></span>
<!--                {/notempty}-->
<!--                {nav id="vo"}-->
                <li {eq name="vo['current']" value="1"}class="curr"{/eq}><a href="">{$vo['title']}</a></li>
<!--                {/nav}-->
            </ul>
        </div>
    </div>
</div>
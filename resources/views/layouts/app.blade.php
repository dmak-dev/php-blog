<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Блог') | {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/blog.css') }}">
    <script>
        // Тема выбирается до отрисовки, иначе светлый фон мелькает при загрузке.
        (function () {
            const saved = localStorage.getItem('blog-theme');
            const system = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            document.documentElement.dataset.theme = saved || system;
        })();
    </script>
</head>
<body>
    <header class="site-header">
        <div class="header-row">
            <h1><a href="{{ route('home') }}">{{ config('app.name') }}</a></h1>
            <button class="theme-toggle" type="button" id="theme-toggle" title="Переключить тему">
                <span class="theme-icon">◐</span> Тема
            </button>
        </div>

        <nav class="site-nav">
            <a href="{{ route('home') }}">Главная</a>
            <a href="{{ route('posts.index') }}">Посты</a>
            <a href="{{ route('about') }}">Об авторе</a>
            <a href="{{ route('contacts') }}">Контакты</a>
            @auth
                <a href="{{ route('posts.admin') }}">Панель управления</a>
                <form class="inline-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="link-button" type="submit">Выйти</button>
                </form>
            @else
                <a href="{{ route('login') }}">Вход</a>
            @endauth
        </nav>
    </header>

    <main>
        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Все права защищены.</p>
    </footer>

    <script>
        document.getElementById('theme-toggle').addEventListener('click', function () {
            const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            document.documentElement.dataset.theme = next;
            localStorage.setItem('blog-theme', next);
        });
    </script>
</body>
</html>

@extends('layouts.app')

@section('title', $title)

@section('content')
    <h2>Вход в панель управления</h2>

    <div class="form-card">
        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div class="form-field">
                <label for="email">Почта</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}">
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password">
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="check-row">
                <input type="checkbox" id="remember" name="remember" value="1" @checked(old('remember'))>
                <label for="remember">Запомнить меня</label>
            </div>

            <button class="btn" type="submit">Войти</button>
            <a class="btn-link" href="{{ route('posts.index') }}">Отмена</a>
        </form>
    </div>
@endsection

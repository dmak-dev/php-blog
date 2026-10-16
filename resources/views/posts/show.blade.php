@extends('layouts.app')

@section('title', $title)

@section('content')
    <article class="post-card">
        <h1>{{ $post->title }}</h1>

        <p class="post-meta">
            {{ $post->author }} | {{ $post->created_at->format('d.m.Y') }}
        </p>

        <div class="post-content">{{ $post->content }}</div>
    </article>

    @auth
        <div class="actions">
            <a class="btn" href="{{ route('posts.edit', $post) }}">Изменить</a>

            <form class="inline-form" method="POST" action="{{ route('posts.destroy', $post) }}"
                  onsubmit="return confirm('Удалить запись «{{ $post->title }}»?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger" type="submit">Удалить</button>
            </form>
        </div>
    @endauth

    <p>
        <a href="{{ route('posts.index') }}">&larr; Назад к списку постов</a>
    </p>
@endsection

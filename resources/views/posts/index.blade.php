@use('Illuminate\Support\Str')

@extends('layouts.app')

@section('title', $title)

@section('content')
    <h2>{{ $title }}</h2>

    @auth
        <div class="actions">
            <a class="btn" href="{{ route('posts.create') }}">Создать пост</a>
            <a class="btn-link" href="{{ route('posts.admin') }}">Панель управления</a>
        </div>
    @endauth

    @if ($posts->isEmpty())
        <p class="empty">
            Постов пока нет.
            @auth
                <a href="{{ route('posts.create') }}">Создать первый пост</a>
            @endauth
        </p>
    @else
        @foreach ($posts as $post)
            <article class="post-card">
                <h3>
                    <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
                </h3>

                <p class="post-meta">
                    {{ $post->author }} | {{ $post->created_at->format('d.m.Y') }}
                </p>

                <p class="post-excerpt">{{ Str::words($post->content, 30) }}</p>
            </article>
        @endforeach

        @include('pagination.blog', ['paginator' => $posts])
    @endif
@endsection

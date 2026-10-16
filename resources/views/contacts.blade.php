@extends('layouts.app')

@section('title', $title)

@section('content')
    <h2>{{ $title }}</h2>

    <div class="post-card">
        <p class="post-excerpt">
            Почта для связи: <a href="mailto:{{ $email }}">{{ $email }}</a>
        </p>
        <p class="post-excerpt">
            Исходный код блога: <a href="{{ $project }}">{{ $project }}</a>
        </p>
    </div>
@endsection

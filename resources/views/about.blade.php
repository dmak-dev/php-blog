@extends('layouts.app')

@section('title', $title)

@section('content')
    <h2>{{ $title }}</h2>

    <div class="post-card">
        <p><strong>{{ $name }}</strong></p>
        <p class="post-excerpt">{{ $role }}</p>
        <p class="post-excerpt">
            Исходный код блога: <a href="{{ $project }}">{{ $project }}</a>
        </p>
    </div>
@endsection

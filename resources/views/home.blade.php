@extends('layouts.app')

@section('title', $title)

@section('content')
    <h2>{{ $title }}</h2>
    <p>{{ $message }}</p>

    <p>
        <a class="btn btn-primary" href="{{ route('posts.index') }}">Читать посты</a>
    </p>
@endsection

@extends('layouts.app')

@section('title', 'Редактирование поста')

@section('content')
    <h2>Редактировать пост</h2>

    <div class="form-card">
        <x-post-form :post="$post" />
    </div>
@endsection

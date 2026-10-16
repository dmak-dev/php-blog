@extends('layouts.app')

@section('title', 'Создать новый пост')

@section('content')
    <h2>Создать новый пост</h2>

    <div class="form-card">
        <x-post-form />
    </div>
@endsection

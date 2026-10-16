@extends('layouts.app')

@section('title', $title)

@section('content')
    <h2>{{ $title }}</h2>

    <div class="actions">
        <a class="btn" href="{{ route('posts.create') }}">Создать пост</a>
        <span class="page-info">Всего записей: {{ $posts->total() }}</span>
    </div>

    @if ($posts->isEmpty())
        <p class="empty">Записей пока нет.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Заголовок</th>
                    <th>Автор</th>
                    <th>Дата</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posts as $post)
                    <tr>
                        <td>
                            <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
                        </td>
                        <td>{{ $post->author }}</td>
                        <td>{{ $post->created_at->format('d.m.Y') }}</td>
                        <td>
                            <a class="btn-link" href="{{ route('posts.edit', $post) }}">Изменить</a>

                            <form class="inline-form" method="POST" action="{{ route('posts.destroy', $post) }}"
                                  onsubmit="return confirm('Удалить запись «{{ $post->title }}»?');">
                                @csrf
                                @method('DELETE')
                                <button class="link-button danger-link" type="submit">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @include('pagination.blog', ['paginator' => $posts])
    @endif
@endsection

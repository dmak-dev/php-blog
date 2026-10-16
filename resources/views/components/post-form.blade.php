@props(['post' => null])

<form method="POST" action="{{ $post ? route('posts.update', $post) : route('posts.store') }}">
    @csrf
    @if ($post)
        @method('PUT')
    @endif

    <div class="form-field">
        <label for="title">Заголовок</label>
        <input type="text" id="title" name="title" value="{{ old('title', $post->title ?? '') }}">
        @error('title')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-field">
        <label for="author">Автор</label>
        <input type="text" id="author" name="author" value="{{ old('author', $post->author ?? '') }}">
        @error('author')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-field">
        <label for="content">Содержание</label>
        <textarea id="content" name="content" rows="8">{{ old('content', $post->content ?? '') }}</textarea>
        @error('content')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <button class="btn btn-primary" type="submit">
        {{ $post ? 'Сохранить изменения' : 'Создать пост' }}
    </button>

    <a class="btn-link" href="{{ $post ? route('posts.show', $post) : route('posts.index') }}">Отмена</a>
</form>

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Работа с постами блога: список, просмотр, создание, изменение, удаление.
 */
class PostController extends Controller
{
    /**
     * Правила проверки полей формы. Одинаковы для создания и изменения.
     *
     * @var array<string, string>
     */
    private const RULES = [
        'title' => 'required|string|min:3|max:255',
        'content' => 'required|string|min:10',
        'author' => 'required|string|min:2|max:100',
    ];

    /**
     * Тексты сообщений об ошибках проверки: интерфейс блога на русском.
     *
     * @var array<string, string>
     */
    private const MESSAGES = [
        'title.required' => 'Укажите заголовок поста.',
        'title.string' => 'Заголовок должен быть текстом.',
        'title.min' => 'Заголовок короче трёх символов.',
        'title.max' => 'Заголовок длиннее 255 символов.',
        'content.required' => 'Введите содержание поста.',
        'content.string' => 'Содержание должно быть текстом.',
        'content.min' => 'Содержание короче десяти символов.',
        'author.required' => 'Укажите автора поста.',
        'author.string' => 'Имя автора должно быть текстом.',
        'author.min' => 'Имя автора короче двух символов.',
        'author.max' => 'Имя автора длиннее 100 символов.',
    ];

    /**
     * Сколько записей показывать на одной странице списка.
     */
    private const PER_PAGE = 10;

    /**
     * Показывает список всех постов: сначала новые.
     */
    public function index(): View
    {
        $title = 'Все посты';
        $posts = Post::orderBy('created_at', 'desc')->paginate(self::PER_PAGE);

        return view('posts.index', compact('title', 'posts'));
    }

    /**
     * Показывает панель управления: записи с действиями изменения и удаления.
     */
    public function admin(): View
    {
        $title = 'Панель управления';
        $posts = Post::orderBy('created_at', 'desc')->paginate(self::PER_PAGE);

        return view('posts.admin', compact('title', 'posts'));
    }

    /**
     * Показывает один пост или отвечает кодом 404, если поста нет.
     *
     * @param int $id Идентификатор поста из адреса.
     */
    public function show(int $id): View
    {
        $post = Post::findOrFail($id);
        $title = $post->title;

        return view('posts.show', compact('title', 'post'));
    }

    /**
     * Показывает форму создания поста.
     */
    public function create(): View
    {
        return view('posts.create');
    }

    /**
     * Проверяет данные формы и сохраняет новый пост.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(self::RULES, self::MESSAGES);

        Log::info('Создан новый пост', [
            'title' => $validated['title'],
            'author' => $validated['author'],
        ]);

        Post::create($validated);

        return redirect()->route('posts.index')->with('success', 'Пост успешно создан!');
    }

    /**
     * Показывает форму изменения поста, заполненную его данными.
     *
     * @param Post $post Пост, найденный по адресу (связывание модели).
     */
    public function edit(Post $post): View
    {
        return view('posts.edit', [
            'title' => 'Редактирование поста',
            'post' => $post,
        ]);
    }

    /**
     * Проверяет данные формы и сохраняет изменения поста.
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate(self::RULES, self::MESSAGES);

        $post->update($validated);

        return redirect()->route('posts.show', $post)
            ->with('success', 'Пост успешно обновлён!');
    }

    /**
     * Удаляет пост.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Пост успешно удалён!');
    }
}

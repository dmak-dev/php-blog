<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Статические страницы блога: главная, об авторе, контакты.
 */
class PageController extends Controller
{
    /**
     * Главная страница блога.
     */
    public function home(): View
    {
        return view('home', [
            'title' => 'Главная',
            'message' => 'Блог о разработке на PHP и Laravel: заметки, разборы и примеры.',
        ]);
    }

    /**
     * Страница об авторе блога.
     */
    public function about(): View
    {
        return view('about', [
            'title' => 'Об авторе',
            'name' => 'Автор блога',
            'role' => 'Веб-разработчик: PHP, Laravel, базы данных',
            'project' => 'https://github.com/dmak-dev/php-blog',
        ]);
    }

    /**
     * Страница с контактами.
     */
    public function contacts(): View
    {
        return view('contacts', [
            'title' => 'Контакты',
            'email' => 'mail@example.com',
            'project' => 'https://github.com/dmak-dev/php-blog',
        ]);
    }
}

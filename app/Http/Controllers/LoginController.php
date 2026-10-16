<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Вход в панель управления блогом и выход из неё.
 */
class LoginController extends Controller
{
    /**
     * Тексты сообщений об ошибках проверки формы входа.
     *
     * @var array<string, string>
     */
    private const MESSAGES = [
        'email.required' => 'Укажите почту.',
        'email.email' => 'Почта указана неверно.',
        'password.required' => 'Укажите пароль.',
    ];

    /**
     * Показывает форму входа.
     */
    public function show(): View
    {
        return view('auth.login', ['title' => 'Вход']);
    }

    /**
     * Проверяет почту и пароль, открывает доступ к панели управления.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ], self::MESSAGES);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Неверная почта или пароль.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()
            ->intended(route('posts.admin'))
            ->with('success', 'Вы вошли в панель управления.');
    }

    /**
     * Завершает сеанс и возвращает читателя к списку постов.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('posts.index')->with('success', 'Вы вышли из панели управления.');
    }
}

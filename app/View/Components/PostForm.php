<?php

declare(strict_types=1);

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Форма поста: используется и для создания, и для изменения.
 */
class PostForm extends Component
{
    /**
     * Получает пост для заполнения формы, если он передан.
     *
     * @param mixed $post Пост для изменения или null при создании.
     */
    public function __construct(
        public mixed $post = null,
    ) {
    }

    /**
     * Возвращает представление формы.
     */
    public function render(): View|Closure|string
    {
        return view('components.post-form');
    }
}

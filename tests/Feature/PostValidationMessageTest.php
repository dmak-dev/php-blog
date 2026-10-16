<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Проверки подсказок об ошибках в форме поста.
 */
class PostValidationMessageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ведение записей доступно только после входа в панель управления.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    /** Сообщения об ошибках выводятся на русском языке. */
    public function test_validation_messages_are_russian(): void
    {
        $response = $this->from('/posts/create')->post('/posts', [
            'title' => 'ab',
            'content' => 'коротко',
            'author' => 'a',
        ]);

        $response->assertRedirect('/posts/create');
        $response->assertSessionHasErrors([
            'title' => 'Заголовок короче трёх символов.',
            'content' => 'Содержание короче десяти символов.',
            'author' => 'Имя автора короче двух символов.',
        ]);
    }

    /** Пустая форма сообщает, что поля обязательны. */
    public function test_required_messages_are_russian(): void
    {
        $response = $this->from('/posts/create')->post('/posts', [
            'title' => '',
            'content' => '',
            'author' => '',
        ]);

        $response->assertSessionHasErrors([
            'title' => 'Укажите заголовок поста.',
            'content' => 'Введите содержание поста.',
            'author' => 'Укажите автора поста.',
        ]);
    }

    /** Ошибка показывается на странице формы, а введённые данные не теряются. */
    public function test_form_shows_error_and_keeps_input(): void
    {
        $response = $this->from('/posts/create')->post('/posts', [
            'title' => 'Короткий',
            'content' => 'Мало текста',
            'author' => '',
        ]);

        // Ошибки проверяются до следующего запроса: мигающие данные живут один запрос.
        $response->assertSessionHasErrors('author');
        $response->assertRedirect('/posts/create');
        $this->assertSame(0, Post::count());

        $page = $this->get('/posts/create');
        $page->assertOk();
        $page->assertSee('name="title"', false);
    }
}

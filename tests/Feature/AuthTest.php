<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Проверки входа в панель управления и защиты действий с записями.
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** Форма входа доступна посетителю. */
    public function test_login_page_is_available(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
    }

    /** Вход с неверными данными возвращает подсказку и не открывает панель. */
    public function test_login_with_wrong_credentials_shows_error(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'неверный',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email' => 'Неверная почта или пароль.']);
        $this->assertGuest();
    }

    /** Вход с верными данными открывает панель управления. */
    public function test_login_with_correct_credentials_opens_admin_panel(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('posts.admin'));
        $this->assertAuthenticatedAs($user);

        $this->get('/admin')->assertOk();
    }

    /** Выход завершает сеанс. */
    public function test_logout_ends_session(): void
    {
        $response = $this->actingAs(User::factory()->create())->post('/logout');

        $response->assertRedirect(route('posts.index'));
        $this->assertGuest();
    }

    /** Запись нельзя создать без входа. */
    public function test_guest_cannot_open_create_form(): void
    {
        $this->get('/posts/create')->assertRedirect('/login');
    }

    /** Запись нельзя сохранить без входа. */
    public function test_guest_cannot_store_post(): void
    {
        $response = $this->post('/posts', [
            'title' => 'Запись без входа',
            'content' => 'Содержание записи, созданной без входа в панель.',
            'author' => 'Кто-то',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('posts', 0);
    }

    /** Панель управления не открывается без входа. */
    public function test_guest_cannot_open_admin_panel(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    /** Изменение и удаление записи без входа не выполняются. */
    public function test_guest_cannot_change_or_delete_post(): void
    {
        $post = Post::create([
            'title' => 'Запись для проверки',
            'content' => 'Содержание записи для проверки доступа.',
            'author' => 'Автор',
        ]);

        $this->get('/posts/' . $post->id . '/edit')->assertRedirect('/login');
        $this->put('/posts/' . $post->id, [
            'title' => 'Изменённый заголовок',
            'content' => 'Изменённое содержание записи.',
            'author' => 'Автор',
        ])->assertRedirect('/login');
        $this->delete('/posts/' . $post->id)->assertRedirect('/login');

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Запись для проверки',
        ]);
    }

    /** Кнопки ведения записей не показываются посетителю. */
    public function test_guest_does_not_see_management_buttons(): void
    {
        $post = Post::create([
            'title' => 'Запись блога',
            'content' => 'Содержание записи блога для проверки.',
            'author' => 'Автор',
        ]);

        $this->get('/posts')->assertDontSee('Создать пост');
        $this->get('/posts/' . $post->id)->assertDontSee('Удалить');
    }

    /** После входа кнопки ведения записей появляются. */
    public function test_author_sees_management_buttons(): void
    {
        $post = Post::create([
            'title' => 'Запись блога',
            'content' => 'Содержание записи блога для проверки.',
            'author' => 'Автор',
        ]);

        $this->actingAs(User::factory()->create());

        $this->get('/posts')->assertSee('Создать пост');
        $this->get('/posts/' . $post->id)->assertSee('Удалить');
    }
}

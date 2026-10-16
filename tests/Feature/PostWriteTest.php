<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Проверки создания, изменения и удаления постов.
 */
class PostWriteTest extends TestCase
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

    /** Форма создания доступна и содержит поля поста. */
    public function test_create_form_is_available(): void
    {
        $response = $this->get('/posts/create');

        $response->assertOk();
        $response->assertSee('name="title"', false);
        $response->assertSee('name="author"', false);
        $response->assertSee('name="content"', false);
    }

    /** Новый пост сохраняется, пользователь возвращается к списку с сообщением. */
    public function test_store_creates_post(): void
    {
        $response = $this->post('/posts', [
            'title' => 'Новый пост блога',
            'content' => 'Достаточно длинное содержание нового поста.',
            'author' => 'Дмитрий',
        ]);

        $response->assertRedirect(route('posts.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('posts', [
            'title' => 'Новый пост блога',
            'author' => 'Дмитрий',
        ]);
    }

    /** Пустая форма возвращает ошибки по каждому полю и ничего не сохраняет. */
    public function test_store_requires_all_fields(): void
    {
        $response = $this->post('/posts', [
            'title' => '',
            'content' => '',
            'author' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'content', 'author']);
        $this->assertDatabaseCount('posts', 0);
    }

    /** Слишком короткие значения отклоняются. */
    public function test_store_rejects_short_values(): void
    {
        $response = $this->post('/posts', [
            'title' => 'ab',
            'content' => 'коротко',
            'author' => 'a',
        ]);

        $response->assertSessionHasErrors(['title', 'content', 'author']);
        $this->assertDatabaseCount('posts', 0);
    }

    /** Слишком длинный заголовок отклоняется. */
    public function test_store_rejects_long_title(): void
    {
        $response = $this->post('/posts', [
            'title' => str_repeat('а', 256),
            'content' => 'Достаточно длинное содержание нового поста.',
            'author' => 'Дмитрий',
        ]);

        $response->assertSessionHasErrors(['title']);
        $this->assertDatabaseCount('posts', 0);
    }

    /** Поля, которых нет в форме, не попадают в базу. */
    public function test_store_ignores_unknown_fields(): void
    {
        $this->post('/posts', [
            'title' => 'Пост с лишним полем',
            'content' => 'Достаточно длинное содержание нового поста.',
            'author' => 'Дмитрий',
            'id' => 999,
        ]);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseMissing('posts', ['id' => 999]);
    }

    /** Форма редактирования заполнена данными поста. */
    public function test_edit_form_is_prefilled(): void
    {
        $post = Post::create([
            'title' => 'Пост для правки',
            'content' => 'Содержание поста для проверки правки.',
            'author' => 'Дмитрий',
        ]);

        $response = $this->get('/posts/' . $post->id . '/edit');

        $response->assertOk();
        $response->assertSee('Пост для правки');
        $response->assertSee('Содержание поста для проверки правки.');
    }

    /** Изменения сохраняются, пользователь возвращается к посту. */
    public function test_update_changes_post(): void
    {
        $post = Post::create([
            'title' => 'Старый заголовок',
            'content' => 'Старое содержание поста блога.',
            'author' => 'Дмитрий',
        ]);

        $response = $this->put('/posts/' . $post->id, [
            'title' => 'Обновлённый заголовок',
            'content' => 'Обновлённое содержание поста блога.',
            'author' => 'Дмитрий',
        ]);

        $response->assertRedirect(route('posts.show', $post));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Обновлённый заголовок',
        ]);
    }

    /** Изменение с пустыми полями отклоняется. */
    public function test_update_validates_input(): void
    {
        $post = Post::create([
            'title' => 'Заголовок поста',
            'content' => 'Содержание поста блога для проверки.',
            'author' => 'Дмитрий',
        ]);

        $response = $this->put('/posts/' . $post->id, [
            'title' => '',
            'content' => '',
            'author' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'content', 'author']);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Заголовок поста',
        ]);
    }

    /** Изменение несуществующего поста отвечает кодом 404. */
    public function test_update_missing_post_returns_not_found(): void
    {
        $response = $this->put('/posts/999', [
            'title' => 'Заголовок поста',
            'content' => 'Содержание поста блога для проверки.',
            'author' => 'Дмитрий',
        ]);

        $response->assertNotFound();
    }

    /** Удаление убирает пост и возвращает к списку. */
    public function test_destroy_deletes_post(): void
    {
        $post = Post::create([
            'title' => 'Пост на удаление',
            'content' => 'Содержание поста, который будет удалён.',
            'author' => 'Дмитрий',
        ]);

        $response = $this->delete('/posts/' . $post->id);

        $response->assertRedirect(route('posts.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseCount('posts', 0);
    }

    /** Удаление несуществующего поста отвечает кодом 404. */
    public function test_destroy_missing_post_returns_not_found(): void
    {
        $this->delete('/posts/999')->assertNotFound();
    }
}

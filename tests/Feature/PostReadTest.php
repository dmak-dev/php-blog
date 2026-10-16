<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Проверки страниц блога: список, просмотр, поиск, страницы без постов.
 */
class PostReadTest extends TestCase
{
    use RefreshDatabase;

    /** Список постов показывает заголовки и авторов. */
    public function test_index_shows_posts(): void
    {
        Post::create([
            'title' => 'Первый пост в блоге',
            'content' => 'Содержание первого поста блога для проверки.',
            'author' => 'Дмитрий',
        ]);

        $response = $this->get('/posts');

        $response->assertOk();
        $response->assertSee('Первый пост в блоге');
        $response->assertSee('Дмитрий');
    }

    /** Пустой список сообщает, что постов нет, а не показывает пустую страницу. */
    public function test_index_without_posts_shows_message(): void
    {
        $response = $this->get('/posts');

        $response->assertOk();
        $response->assertSee('Постов пока нет');
    }

    /** Посты выводятся от новых к старым. */
    public function test_index_orders_posts_by_creation(): void
    {
        $old = Post::create([
            'title' => 'Старый пост блога',
            'content' => 'Содержание старого поста блога.',
            'author' => 'Автор',
        ]);
        // Поле created_at не входит в массовое присваивание, поэтому дата задаётся отдельно.
        $old->created_at = now()->subDay();
        $old->save();

        Post::create([
            'title' => 'Новый пост блога',
            'content' => 'Содержание нового поста блога.',
            'author' => 'Автор',
        ]);

        $response = $this->get('/posts');

        $response->assertOk();
        $response->assertSeeInOrder(['Новый пост блога', 'Старый пост блога']);
    }

    /** Страница поста показывает заголовок, автора и содержание. */
    public function test_show_page_displays_post(): void
    {
        $post = Post::create([
            'title' => 'Пост о Laravel',
            'content' => 'Laravel делает разработку на PHP быстрой и приятной.',
            'author' => 'Дмитрий',
        ]);

        $response = $this->get('/posts/' . $post->id);

        $response->assertOk();
        $response->assertSee('Пост о Laravel');
        $response->assertSee('Laravel делает разработку на PHP быстрой и приятной.');
        $response->assertSee('Дмитрий');
    }

    /** Несуществующий пост отвечает кодом 404. */
    public function test_show_missing_post_returns_not_found(): void
    {
        $this->get('/posts/999')->assertNotFound();
    }

    /** Разметка из базы выводится экранированной, а не как HTML. */
    public function test_post_content_is_escaped(): void
    {
        $post = Post::create([
            'title' => 'Пост с разметкой',
            'content' => '<script>alert("xss")</script> содержание поста',
            'author' => 'Автор',
        ]);

        $response = $this->get('/posts/' . $post->id);

        $response->assertOk();
        $response->assertDontSee('<script>alert("xss")</script>', false);
        $response->assertSee('&lt;script&gt;', false);
    }

    /** Страницы об авторе и контактов доступны. */
    public function test_static_pages_are_available(): void
    {
        $this->get('/')->assertOk();
        $this->get('/about')->assertOk();
        $this->get('/contacts')->assertOk();
    }

    /** Главная страница ведёт к списку постов. */
    public function test_home_page_links_to_posts(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('posts.index'));
    }

    /** Неизвестный адрес отвечает кодом 404. */
    public function test_unknown_page_returns_not_found(): void
    {
        $this->get('/unknown-page')->assertNotFound();
    }

    /** Список выводится постранично: десять записей на страницу. */
    public function test_index_shows_ten_posts_per_page(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            Post::create([
                'title' => 'Запись номер ' . $i,
                'content' => 'Содержание записи номер ' . $i . ' для проверки страниц.',
                'author' => 'Автор',
            ]);
        }

        $first = $this->get('/posts');
        $first->assertOk();
        $this->assertSame(10, substr_count($first->getContent(), 'class="post-card"'));
        $first->assertSee('Страница 1 из 2');

        $second = $this->get('/posts?page=2');
        $second->assertOk();
        $this->assertSame(2, substr_count($second->getContent(), 'class="post-card"'));
        $second->assertSee('Страница 2 из 2');
    }
}

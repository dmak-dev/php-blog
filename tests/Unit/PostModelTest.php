<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Post;
use PHPUnit\Framework\TestCase;

/**
 * Проверки модели поста без обращения к базе данных.
 */
class PostModelTest extends TestCase
{
    /** Модель разрешает массовое присваивание только своих полей. */
    public function test_fillable_fields(): void
    {
        $post = new Post();

        $this->assertSame(['title', 'content', 'author'], $post->getFillable());
    }

    /** Посторонние поля при массовом присваивании отбрасываются. */
    public function test_unknown_field_is_ignored(): void
    {
        $post = new Post();
        $post->fill([
            'title' => 'Заголовок поста',
            'is_published' => true,
            'created_at' => '2026-01-01 00:00:00',
        ]);

        $this->assertSame(['title' => 'Заголовок поста'], $post->getAttributes());
    }
}

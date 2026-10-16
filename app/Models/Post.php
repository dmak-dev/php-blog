<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Пост блога.
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $author
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Post extends Model
{
    /**
     * Поля, доступные для массового присваивания.
     *
     * @var array<int, string>
     */
    protected $fillable = ['title', 'content', 'author'];
}

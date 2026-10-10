<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'slug', 'title', 'category', 'published_at', 'read_minutes', 'image', 'excerpt', 'body', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'read_minutes' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Published and not scheduled for a future date.
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true)->whereDate('published_at', '<=', now());
    }

    public function scopeLatestFirst(Builder $query): void
    {
        $query->orderByDesc('published_at')->orderByDesc('id');
    }

    protected function categoryLabel(): Attribute
    {
        return Attribute::get(fn () => config('blog.categories')[$this->category] ?? null);
    }

    /**
     * Body split into blocks: ['type' => 'heading'|'paragraph', 'text' => ...].
     * Paragraphs are separated by blank lines; a line starting with "## " is a heading.
     */
    protected function bodyBlocks(): Attribute
    {
        return Attribute::get(function () {
            $blocks = [];
            $paragraph = [];
            $flush = function () use (&$blocks, &$paragraph) {
                if ($paragraph) {
                    $blocks[] = ['type' => 'paragraph', 'text' => implode(' ', $paragraph)];
                    $paragraph = [];
                }
            };

            foreach (preg_split('/\R/', (string) $this->body) as $line) {
                $line = trim($line);

                if ($line === '') {
                    $flush();
                } elseif (str_starts_with($line, '## ')) {
                    $flush();
                    $blocks[] = ['type' => 'heading', 'text' => trim(substr($line, 3))];
                } else {
                    $paragraph[] = $line;
                }
            }
            $flush();

            return $blocks;
        });
    }
}

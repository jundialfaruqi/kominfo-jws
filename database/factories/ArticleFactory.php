<?php

namespace Database\Factories;

use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence;
        return [
            'user_id'             => User::factory(),
            'article_category_id' => ArticleCategory::factory(),
            'title'               => $title,
            'slug'                => Str::slug($title) . '-' . now()->format('YmdHis'),
            'description'         => $this->faker->paragraph,
            'content'             => '<p>' . implode('</p><p>', $this->faker->paragraphs(5)) . '</p>',
            'status'              => $this->faker->randomElement(['Draft', 'Published']),
            'published_at'        => now(),
        ];
    }
}

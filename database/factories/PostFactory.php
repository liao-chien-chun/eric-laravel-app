<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => fake()->sentence(rand(5, 10)),
            'content' => fake()->paragraphs(rand(3, 8), true),
            'status' => 2, // 預設為已發布
            'views_count' => rand(0, 1000),
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * 設定文章狀態為草稿
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }

    /**
     * 設定文章狀態為已發布
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 2,
        ]);
    }

    /**
     * 設定文章狀態為隱藏
     */
    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 3,
        ]);
    }

    /**
     * 設定文章有較高的觀看次數
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'views_count' => rand(5000, 50000),
        ]);
    }

    /**
     * 設定文章為最近發布
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}

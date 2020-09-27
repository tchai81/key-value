<?php

namespace Database\Factories;

use App\Models\KeyValue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class KeyValueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = KeyValue::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'value' => Str::random(5),
            'created_at' => now()->addMinutes(rand(5,60))
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Request;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Request::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'owner_id' => $this->faker->randomDigitNotNull(),
            'photographer_id' => $this->faker->randomDigitNotNull(),
            'product' => $this->faker->company(),
            'location' => $this->faker->address(),
            'LQT' => $this->faker->imageUrl(360, 360, 'animals', true, 'cats'),
            'HRI' => $this->faker->imageUrl(640, 480, 'animals', true, 'cats'),
            'status' => 2,
            'approve' => 0,
        ];
    }
}

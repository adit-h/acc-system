<?php

namespace Database\Factories;

use App\Models\TransactionIn;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransactionInFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TransactionIn::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = $this->faker->dateTimeBetween('-1 month', '+1 month');
        //$from = $this->faker->randomDigitNot(0, 3, 5, 7, 8, 9);
        //$to = $this->faker->randomDigitNot(0, 1, 2, 4, 5, 7, 8, 9);
        $from = $this->faker->randomElement(['1', '2', '4', '6']);
        $to = $this->faker->randomElement(['3', '6']);
        $value = $this->faker->numberBetween(100000000, 99999999999);
        $ref = $this->faker->numerify('-in-####');
        $desc = $this->faker->text(100);

        return [
            'trans_date' => $date,
            'receive_from' => intval($from),
            'store_to' => intval($to),
            'value' => $value,
            'reference' => $ref,
            'description' => $desc,
        ];
    }
}

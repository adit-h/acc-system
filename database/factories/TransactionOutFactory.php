<?php

namespace Database\Factories;

use App\Models\TransactionOut;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransactionOutFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TransactionOut::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = $this->faker->dateTimeBetween('-1 month', '+1 month');
        // cat : 1
        $from = $this->faker->randomElement(['1', '2', '3']);
        // cat : 2, 3, 4, 8, 9
        $to = $this->faker->randomElement(['6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22',
            '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57',
            '58', '59', '60', '61', '62', '63', '64', '65', '66', '67', '68', '69', '70']);
        $value = $this->faker->numberBetween(100000000, 99999999999);
        $ref = $this->faker->numerify('out-#####');
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

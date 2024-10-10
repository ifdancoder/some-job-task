<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EquipmentType>
 */
class EquipmentTypeFactory extends Factory
{
    /**
     * Define the model"s default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $acceptedCharsForMask = ["N", "A", "a", "X", "Z"];
        $randomNumber = rand(3, count($acceptedCharsForMask) * 2);
        $serialNumberMask = "";
        for ($i = 0; $i < $randomNumber; $i++) {
            $serialNumberMask .= $acceptedCharsForMask[array_rand($acceptedCharsForMask)];
        }
        return [
            "equipment_type_name" => $this->faker->word,
            "serial_number_mask" => $serialNumberMask
        ];
    }
}

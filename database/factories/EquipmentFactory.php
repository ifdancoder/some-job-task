<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomEquipmentType = \App\Models\EquipmentType::inRandomOrder()->first();
        $mask = $randomEquipmentType->serial_number_mask;
        $acceptedForZ = ["-", "_", "@"];
        $serialNumber = '';
        for ($i = 0; $i < strlen($mask); $i++) {
            switch ($mask[$i]) {
                case 'N':
                    $serialNumber .= rand(0, 9);
                    break;
                case 'A':
                    $serialNumber .= chr(rand(ord('A'), ord('Z')));
                    break;
                case 'a':
                    $serialNumber .= chr(rand(ord('a'), ord('z')));
                    break;
                case 'X':
                    if ($this->faker->boolean()) {
                        $serialNumber .= chr(rand(ord('A'), ord('Z')));
                    } else {
                        $serialNumber .= chr(rand(ord('0'), ord('9')));
                    }
                    break;
                case 'Z':
                    $serialNumber .= $acceptedForZ[array_rand($acceptedForZ)];
                    break;
            }
        }
        return [
            'equipment_type_id' => $randomEquipmentType->id,
            'serial_number' => $serialNumber,
            'description' => $this->faker->sentence(),
        ];
    }
}

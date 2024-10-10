<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use App\Models\EquipmentType;
use App\Models\Equipment;

class SerialNumber implements ValidationRule
{
    public $equipment;
    public $equipmentType;

    public function __construct($equipment=null, int $equipmentTypeId=null) {
        $this->equipment = $equipment;
        if (isset($equipmentTypeId)) {
            $this->equipmentType = EquipmentType::find($equipmentTypeId);
        } else if (isset($this->equipment)) {
            $this->equipmentType = $this->equipment->equipmentType;
        }
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $equipmentType = null;

        if (isset($this->equipmentType)) {
            $equipmentType = $this->equipmentType;
        } else if (isset($this->equipment)) {
            $equipmentType = $this->equipment->equipment_type_id;
        }

        if (isset($equipmentType)) {
            $reDict = [
                'N' => '[0-9]',
                'A' => '[A-Z]',
                'a' => '[a-z]',
                'X' => '[A-Z0-9]',
                'Z' => '[-_@]'
            ];

            $pattern = '';
            $mask = $equipmentType->serial_number_mask;

            for ($i = 0; $i < strlen($mask); $i++) {
                $char = $mask[$i];
                if (isset($reDict[$char])) {
                    $pattern .= $reDict[$char];
                }
            }

            for ($i = 0; $i < strlen($mask); $i++) {
                $pattern .= $associative_array[$mask[$i]] ?? '';
            }
            
            if (!preg_match('/^' . $pattern . '$/', $value)) {
                $fail('Серийный номер не соответствует маске типа оборудования');
            }

        } else {
            $fail('Невозможно проверить серийный номер оборудования, поскольку указанного типа оборудования не существует');
        }
    }
}

<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use App\Models\Equipment;

class EquipmentsRule implements ValidationRule
{
    protected array $attributes;
    protected array $tuples;

    /**
     * Create a new rule instance.
     *
     * @param array $attributes Массив имен атрибутов, по которым будет проверяться уникальность.
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
        $this->tuples = [];
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $attrsString = implode(', ', $this->attributes);

        if (!is_array($value)) {
            $fail('Элемент не является массивом');
        }

        $tuple = [];
        $allTheAttributes = true;
        foreach ($this->attributes as $attr) {
            if (!isset($value[$attr])) {
                $fail('В элементе нет параметра: ' . $attr);
                $allTheAttributes = false;
                continue;
            }
            $tuple[$attr] = $value[$attr];
        }
        
        if ($allTheAttributes) {
            $query = Equipment::query();
            foreach($this->attributes as $attribute) {
                $query->where($attribute, $tuple[$attribute]);
            }
            if ($query->exists()) {
                $fail('Дубликат элемента и базы данных согласно списку атрибутов: ' . $attrsString);
            }
        }

        $tupleKey = serialize($tuple);

        if (in_array($tupleKey, $this->tuples)) {
            $fail('Дубликат элементов внутри исходного массива согласно списку атрибутов: ' . $attrsString);
        }
        $this->tuples[] = $tupleKey;
    }
}

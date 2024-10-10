<?php
namespace App\Services;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\EquipmentCollection;
use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use App\Http\Requests\EquipmentRequest;
use App\Validators\EquipmentValidator;
use \stdClass;

class EquipmentService
{
    /**
     * Возвращает список оборудования с пагинацией и поиском
     * 
     * @param  array  $data
     * 
     * @return \Illuminate\Pagination\LengthAwarePaginator | \App\Http\Resources\EquipmentCollection | \Illuminate\Http\Response
     */
    public function index(array $data)
    {
        $query = null;
        if (array_key_exists('q', $data) && $data['q'] != '') {
            $query = Equipment::search($data['q']);
        } else {
            $query = Equipment::query();
            
            foreach ($data as $key => $value) {
                if ($key != 'q' && $key != 'perPage') {
                    $query = $query->where(DB::raw("CONVERT({$key}, char)"), 'like', DB::raw("'%{$value}%'"));
                }
            }
        }
        
        $perPage = $data['perPage'] ?? config('pages_config.per_page');
        $query = $query->paginate($perPage);

        return new EquipmentCollection($query);
    }

    /**
     * Создает запись об оборудовании в бд
     * 
     * @param array $data
     * 
     * @return array
     */
    public function create(array $data)
    {
        $errors = $data['errors'];
        $success = $data['success'];
        foreach ($success as $key => $equipment) {
            $created = Equipment::create($equipment);
            if ($created) {
                unset($success->$key);
                $success->$key = new EquipmentResource($created);
            } else {
                $errors->$key = 'Failed to create equipment';
            }
        }
        return ['success' => (object) $success, 'errors' => (object) $errors];
    }

    /**
     * Обновляет запись об оборудовании в бд
     * 
     * @param  int  $id
     * @param  array  $data
     * 
     * @return \App\Http\Resources\EquipmentResource | null
     */
    public function update(Equipment $equipment, array $data)
    {
        $equipment->update($data);
        return new EquipmentResource($equipment);
    }
}
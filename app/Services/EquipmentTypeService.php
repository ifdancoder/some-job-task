<?php
namespace App\Services;

use DB;
use App\Models\EquipmentType;

use App\Http\Resources\EquipmentTypeResource;
use App\Http\Resources\EquipmentTypeCollection;

class EquipmentTypeService
{
    /**
     * Возвращает список типов оборудования
     * 
     * @param array $data
     * 
     * @return EquipmentTypeCollection
     */
    public function index(array $data)
    {
        $query = null;
        if (array_key_exists('q', $data) && $data['q'] != '') {
            $query = EquipmentType::search($data['q']);
        } else {
            $query = EquipmentType::query();

            foreach ($data as $key => $value) {
                if ($key != 'q' && $key != 'perPage') {
                    $query = $query->where(DB::raw("CONVERT({$key}, char)"), 'like', DB::raw("'%{$value}%'"));
                }
            }
        }

        $perPage = $data['perPage'] ?? config('pages_config.per_page');
        $query = $query->paginate($perPage);

        return new EquipmentTypeCollection($query);
    }

    /**
     * Возвращает список типов оборудования без пагинации и поиска
     * 
     * @return \Illuminate\Pagination\LengthAwarePaginator | \App\Http\Resources\EquipmentTypeCollection | \Illuminate\Http\Response
     */
    public function indexwp()
    {
        return new EquipmentTypeCollection(EquipmentType::all());
    }

    /**
     * Создает запись о типе оборудования в бд
     * 
     * @param array $data
     * 
     * @return \App\Http\Resources\EquipmentTypeResource | null
     */
    public function create(array $data)
    {
        $createdEquipmentType = EquipmentType::create($data);

        if (!$createdEquipmentType) {
            return null;
        }
        return new EquipmentTypeResource($createdEquipmentType);
    }

    /**
     * Обновляет запись о типе оборудования в бд
     * 
     * @param  int  $id
     * @param  array  $data
     * 
     * @return \App\Http\Resources\EquipmentTypeResource | null
     */
    public function update($id, array $data)
    {
        $equipmentType = EquipmentType::find($id);
        if (!$equipmentType) {
            return null;
        }
        $equipmentType->update($data);
        return new EquipmentTypeResource($equipmentType);
    }

    /**
     * Удаляет запись о типе оборудования из бд
     * 
     * @param  int  $id
     * 
     * @return \App\Http\Resources\EquipmentTypeResource | null
     */
    public function destroy(int $id)
    {
        $equipmentType = EquipmentType::find($id);

        if (!$equipmentType) {
            return null;
        }
        $createdEquipmentType = $equipmentType->delete();
        return $createdEquipmentType;
    }

    /**
     * Возвращает запись о типе оборудования
     * 
     * @param  int  $id
     * 
     * @return \App\Http\Resources\EquipmentTypeResource | null
     */
    public function show($id)
    {
        $equipmentType = EquipmentType::find($id);
        if (!$equipmentType) {
            return null;
        }
        return new EquipmentTypeResource($equipmentType);
    }
}
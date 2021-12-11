<?php

namespace kirillbdev\WCUkrShipping\DB\Repositories;

use kirillbdev\WCUkrShipping\DB\Dto\InsertWarehouseDto;
use kirillbdev\WCUSCore\Facades\DB;

if ( ! defined('ABSPATH')) {
    exit;
}

class WarehouseRepository
{
    /**
     * @param string $ref
     * @return \stdClass|null
     */
    public function getWarehouseByRef($ref)
    {
        return DB::table('wc_ukr_shipping_np_warehouses')
            ->where('ref', $ref)
            ->first();
    }

    public function searchByQuery($query, $cityRef, $page = 1, $limit = 30)
    {
        $q = DB::table('wc_ukr_shipping_np_warehouses')
            ->where('city_ref', $cityRef);

        if ($query) {
            $q->whereRaw('(description like %s or description_ru like %s)', [
                "%$query%",
                "%$query%"
            ]);
        }

        $q->orderBy('`number`');

        if ($page > 1) {
            $q->skip(($page - 1) * $limit);
        }

        return $q->limit($limit)->get();
    }

    /**
     * @param string $query
     * @param string $cityRef
     * @return int
     */
    public function countByQuery($query, $cityRef)
    {
        $q = DB::table('wc_ukr_shipping_np_warehouses')
            ->where('city_ref', $cityRef);

        if ($query) {
            $q->whereRaw('(description like %s or description_ru like %s)', [
                "%$query%",
                "%$query%"
            ]);
        }

        return $q->count('ref');
    }

    public function clearWarehouses()
    {
        DB::table('wc_ukr_shipping_np_warehouses')->truncate();
    }

    public function insertWarehouse(InsertWarehouseDto $dto)
    {
        DB::table('wc_ukr_shipping_np_warehouses')
            ->insert([
                'ref' => $dto->getRef(),
                'description' => $dto->getDescription(),
                'description_ru' => $dto->getDescriptionRu(),
                'city_ref' => $dto->getCityRef(),
                'number' => $dto->getNumber(),
                'warehouse_type' => $dto->getWarehouseType()
            ], [
                'number' => '%d',
                'warehouse_type' => '%d'
            ]);
    }

    public function deleteByRefs(array $refs)
    {
        if ( ! $refs) {
            return;
        }

        global $wpdb;

        $inputs = '';

        for ($i = 0; $i < count($refs); $i++) {
            $inputs .= ($i > 0 ? ',' : '') . '%s';
        }

        $wpdb->query(
            $wpdb->prepare("delete from wc_ukr_shipping_np_warehouses where ref in ($inputs)", $refs)
        );
    }
}
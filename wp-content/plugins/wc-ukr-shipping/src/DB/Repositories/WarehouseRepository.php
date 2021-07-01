<?php

namespace kirillbdev\WCUkrShipping\DB\Repositories;

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
}
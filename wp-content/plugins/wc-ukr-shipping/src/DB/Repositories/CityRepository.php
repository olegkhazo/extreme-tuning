<?php

namespace kirillbdev\WCUkrShipping\DB\Repositories;

use kirillbdev\WCUkrShipping\DB\Dto\InsertCityDto;
use kirillbdev\WCUSCore\Facades\DB;

if ( ! defined('ABSPATH')) {
    exit;
}

class CityRepository
{
    public function getCitiesByRefs($refs)
    {
        return DB::table('wc_ukr_shipping_np_cities')
            ->whereIn('ref', $refs)
            ->get();
    }

    public function searchCitiesByQuery($query)
    {
        return DB::table('wc_ukr_shipping_np_cities')
            ->whereLike('description', $query . '%')
            ->orWhereLike('description_ru', $query . '%')
            ->orderBy('description')
            ->get();
    }

    public function getCityByRef($ref)
    {
        return DB::table('wc_ukr_shipping_np_cities')
            ->where('ref', $ref)
            ->first();
    }

    public function clearCities()
    {
        DB::table('wc_ukr_shipping_np_cities')->truncate();
    }

    public function insertCity(InsertCityDto $dto)
    {
        DB::table('wc_ukr_shipping_np_cities')
            ->insert([
                'ref' => $dto->getRef(),
                'description' => $dto->getDescription(),
                'description_ru' => $dto->getDescriptionRu(),
                'area_ref' => $dto->getAreaRef()
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
            $wpdb->prepare("delete from wc_ukr_shipping_np_cities where ref in ($inputs)", $refs)
        );
    }
}
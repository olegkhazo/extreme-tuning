<?php

namespace kirillbdev\WCUkrShipping\DB\Repositories;

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
}
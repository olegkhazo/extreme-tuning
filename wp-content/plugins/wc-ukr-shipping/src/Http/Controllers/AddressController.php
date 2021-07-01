<?php

namespace kirillbdev\WCUkrShipping\Http\Controllers;

use kirillbdev\WCUkrShipping\DB\Repositories\CityRepository;
use kirillbdev\WCUkrShipping\DB\Repositories\WarehouseRepository;
use kirillbdev\WCUSCore\Http\Controller;
use kirillbdev\WCUSCore\Http\Request;

if ( ! defined('ABSPATH')) {
    exit;
}

class AddressController extends Controller
{
    /**
     * @var CityRepository
     */
    private $cityRepository;

    /**
     * @var WarehouseRepository
     */
    private $warehouseRepository;

    public function __construct(
        CityRepository $cityRepository,
        WarehouseRepository $warehouseRepository
    ) {
        $this->cityRepository = $cityRepository;
        $this->warehouseRepository = $warehouseRepository;
    }

    public function searchCities(Request $request)
    {
        if (  ! $request->get('query')) {
            return $this->jsonResponse([
                'success' => true,
                'data' => []
            ]);
        }

        return $this->jsonResponse([
            'success' => true,
            'data' => $this->mapAdresses(
                $this->cityRepository->searchCitiesByQuery($request->get('query')),
                $request->get('lang', '')
            )
        ]);
    }

    public function searchWarehouses(Request $request)
    {
        if ( ! $request->get('city_ref') || ! (int)$request->get('page')) {
            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'items' => [],
                    'more' => false
                ]
            ]);
        }

        $items = $this->mapAdresses(
            $this->warehouseRepository->searchByQuery(
                $request->get('query', ''),
                $request->get('city_ref'),
                (int)$request->get('page')
            ),
            $request->get('lang', '')
        );

        $offset = ((int)$request->get('page') - 1) * 30 + count($items);

        return $this->jsonResponse([
            'success' => true,
            'data' => [
                'items' => $items,
                'more' => $offset < $this->warehouseRepository->countByQuery(
                    $request->get('query', ''),
                    $request->get('city_ref')
                )
            ]
        ]);
    }

    private function mapAdresses($addresses, $locale)
    {
        return array_map(function ($item) use($locale) {
            return [
                'value' => $item['ref'],
                'name' => $locale === 'ru' ? $item['description_ru'] : $item['description']
            ];
        }, $addresses);
    }
}
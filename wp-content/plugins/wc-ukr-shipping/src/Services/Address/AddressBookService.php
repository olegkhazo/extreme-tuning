<?php

namespace kirillbdev\WCUkrShipping\Services\Address;

use kirillbdev\WCUkrShipping\Api\NovaPoshtaApi;
use kirillbdev\WCUkrShipping\DB\Dto\InsertAreaDto;
use kirillbdev\WCUkrShipping\DB\Dto\InsertCityDto;
use kirillbdev\WCUkrShipping\DB\Dto\InsertWarehouseDto;
use kirillbdev\WCUkrShipping\DB\Repositories\AreaRepository;
use kirillbdev\WCUkrShipping\DB\Repositories\CityRepository;
use kirillbdev\WCUkrShipping\DB\Repositories\WarehouseRepository;
use kirillbdev\WCUkrShipping\DB\Repositories\WarehouseSyncRepository;
use kirillbdev\WCUkrShipping\Exceptions\ApiErrorException;

if ( ! defined('ABSPATH')) {
    exit;
}

class AddressBookService
{
    /**
     * @var NovaPoshtaApi
     */
    private $api;

    /**
     * @var WarehouseSyncRepository
     */
    private $syncRepository;

    public function __construct(NovaPoshtaApi $api, WarehouseSyncRepository $syncRepository)
    {
        $this->api = $api;
        $this->syncRepository = $syncRepository;
    }

    /**
     * @throws ApiErrorException
     * @throws \kirillbdev\WCUkrShipping\Exceptions\ApiServiceException
     */
    public function loadAreas()
    {
        /** @var AreaRepository $areaRepository */
        $areaRepository = wcus_container()->make(AreaRepository::class);
        $result = $this->api->getAreas();

        if ($result['success']) {
            $areaRepository->clearAreas();

            foreach ($result['data'] as $area) {
                $areaRepository->insertArea(new InsertAreaDto($area['Ref'], $area['Description']));
            }
        }
        else {
            throw new ApiErrorException($result['errors']);
        }
    }

    public function loadCities(int $page): int
    {
        // Hide error because Nova Poshta can send duplicate refs from api
        global $wpdb;
        $wpdb->hide_errors();

        /** @var CityRepository $cityRepository */
        $cityRepository = wcus_container()->make(CityRepository::class);
        $result = $this->api->getCities($page);

        if ($result['success']) {
            $this->syncRepository->updateStage(WarehouseSyncRepository::STAGE_CITY, $page);

            if ($page === 1) {
                $cityRepository->clearCities();
            }

            $cityRepository->deleteByRefs(array_map(function ($item) {
                return $item['Ref'];
            }, $result['data']));

            foreach ($result['data'] as $city) {
                $cityRepository->insertCity(
                    new InsertCityDto($city['Ref'], $city['Description'], $city['DescriptionRu'], $city['Area'])
                );
            }

            return count($result['data']);
        }
        else {
            throw new ApiErrorException($result['errors']);
        }
    }

    public function loadWarehouses(int $page): int
    {
        // Hide error because Nova Poshta can send duplicate refs from api
        global $wpdb;
        $wpdb->hide_errors();

        /** @var WarehouseRepository $warehouseRepository */
        $warehouseRepository = wcus_container()->make(WarehouseRepository::class);
        $result = $this->api->getWarehouses($page);

        if ($result['success']) {
            $this->syncRepository->updateStage(WarehouseSyncRepository::STAGE_WAREHOUSE, $page);

            if ($page === 1) {
                $warehouseRepository->clearWarehouses();
            }

            $warehouseRepository->deleteByRefs(array_map(function ($item) {
                return $item['Ref'];
            }, $result['data']));

            foreach ($result['data'] as $warehouse) {
                $warehouseRepository->insertWarehouse(
                    new InsertWarehouseDto(
                        $warehouse['Ref'],
                        $warehouse['Description'],
                        $warehouse['DescriptionRu'],
                        $warehouse['CityRef'],
                        (int)$warehouse['Number'],
                        $this->getWarehouseType($warehouse)
                    )
                );
            }

            if (count($result['data']) === 0) {
                $this->syncRepository->setCompleteSync();
            }

            return count($result['data']);
        }
        else {
            throw new ApiErrorException($result['errors']);
        }
    }

    private function getWarehouseType(array $warehouse): int
    {
        if ($warehouse['TypeOfWarehouse'] === '9a68df70-0267-42a8-bb5c-37f427e36ee4') {
            return WCUS_WAREHOUSE_TYPE_CARGO;
        }

        if (strpos($warehouse['Description'], 'Поштомат') !== false) {
            return WCUS_WAREHOUSE_TYPE_POSHTOMAT;
        }

        return WCUS_WAREHOUSE_TYPE_REGULAR;
    }
}
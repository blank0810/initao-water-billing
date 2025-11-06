<?php

namespace App\Services\Address;

use App\Models\Province;
use App\Models\Town;
use App\Models\Barangay;
use App\Models\Purok;
use App\Models\AccountType;
use App\Models\WaterRate;
use App\Models\ChargeItem;
use App\Models\Status;

class AddressService
{
    /**
     * Get all active provinces
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProvinces()
    {
        return Province::where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->orderBy('prov_desc')
            ->get(['prov_id', 'prov_desc']);
    }

    /**
     * Get all active towns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTowns()
    {
        return Town::where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->orderBy('t_desc')
            ->get(['t_id', 't_desc']);
    }

    /**
     * Get towns by province
     *
     * @param int $provinceId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTownsByProvince(int $provinceId)
    {
        return Town::where('prov_id', $provinceId)
            ->where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->orderBy('t_desc')
            ->get(['t_id', 't_desc']);
    }

    /**
     * Get all active barangays
     * Note: Barangay table doesn't have town_id, assumes single municipality (Initao)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBarangays()
    {
        return Barangay::where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->orderBy('b_desc')
            ->get(['b_id', 'b_desc']);
    }

    /**
     * Get all active puroks
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPuroks()
    {
        return Purok::where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->orderBy('p_desc')
            ->get(['p_id', 'p_desc']);
    }

    /**
     * Get puroks by barangay
     *
     * @param int $barangayId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPuroksByBarangay(int $barangayId)
    {
        return Purok::where('b_id', $barangayId)
            ->where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->orderBy('p_desc')
            ->get(['p_id', 'p_desc']);
    }

    /**
     * Get all active account types
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAccountTypes()
    {
        return AccountType::where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->orderBy('at_desc')
            ->get(['at_id', 'at_desc']);
    }

    /**
     * Get all active water rates
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWaterRates()
    {
        return WaterRate::where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->orderBy('rate_desc')
            ->get(['wr_id', 'rate_desc', 'rate']);
    }

    /**
     * Get application charge items (one-time charges for new applications)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getApplicationCharges()
    {
        return ChargeItem::where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->where('charge_type', 'one_time')
            ->orderBy('name')
            ->get(['charge_item_id', 'name', 'code', 'description', 'default_amount']);
    }
}

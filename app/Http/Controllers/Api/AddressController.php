<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Address\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    protected AddressService $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * Get all provinces
     */
    public function getProvinces(): JsonResponse
    {
        $provinces = $this->addressService->getProvinces();

        return response()->json($provinces);
    }

    /**
     * Get towns (all or by province)
     */
    public function getTowns(Request $request): JsonResponse
    {
        $request->validate([
            'province_id' => 'nullable|integer|exists:province,prov_id',
        ]);

        if ($request->has('province_id') && $request->province_id) {
            $towns = $this->addressService->getTownsByProvince($request->province_id);
        } else {
            $towns = $this->addressService->getAllTowns();
        }

        return response()->json($towns);
    }

    /**
     * Get all barangays
     * Note: Not filtered by town since barangay table doesn't have town_id
     */
    public function getBarangays(): JsonResponse
    {
        $barangays = $this->addressService->getBarangays();

        return response()->json($barangays);
    }

    /**
     * Get puroks (all or by barangay)
     */
    public function getPuroks(Request $request): JsonResponse
    {
        $request->validate([
            'barangay_id' => 'nullable|integer|exists:barangay,b_id',
        ]);

        if ($request->has('barangay_id') && $request->barangay_id) {
            $puroks = $this->addressService->getPuroksByBarangay($request->barangay_id);

            // Fallback to all puroks if none are linked to this barangay yet
            // This handles the case where b_id linkage hasn't been established
            if ($puroks->isEmpty()) {
                $puroks = $this->addressService->getAllPuroks();
            }
        } else {
            $puroks = $this->addressService->getAllPuroks();
        }

        return response()->json($puroks);
    }

    /**
     * Get all account types
     */
    public function getAccountTypes(): JsonResponse
    {
        $accountTypes = $this->addressService->getAccountTypes();

        return response()->json($accountTypes);
    }

    /**
     * Get all water rates
     */
    public function getWaterRates(): JsonResponse
    {
        $waterRates = $this->addressService->getWaterRates();

        return response()->json($waterRates);
    }

    /**
     * Get application charge items
     */
    public function getApplicationCharges(): JsonResponse
    {
        $charges = $this->addressService->getApplicationCharges();

        return response()->json($charges);
    }
}

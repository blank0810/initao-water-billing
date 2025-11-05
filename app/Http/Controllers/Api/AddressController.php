<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Address\AddressService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    protected AddressService $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * Get all provinces
     *
     * @return JsonResponse
     */
    public function getProvinces(): JsonResponse
    {
        $provinces = $this->addressService->getProvinces();
        return response()->json($provinces);
    }

    /**
     * Get towns by province
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTowns(Request $request): JsonResponse
    {
        $request->validate([
            'province_id' => 'required|integer|exists:province,prov_id'
        ]);

        $towns = $this->addressService->getTownsByProvince($request->province_id);
        return response()->json($towns);
    }

    /**
     * Get all barangays
     * Note: Not filtered by town since barangay table doesn't have town_id
     *
     * @return JsonResponse
     */
    public function getBarangays(): JsonResponse
    {
        $barangays = $this->addressService->getBarangays();
        return response()->json($barangays);
    }

    /**
     * Get puroks by barangay
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPuroks(Request $request): JsonResponse
    {
        $request->validate([
            'barangay_id' => 'required|integer|exists:barangay,b_id'
        ]);

        $puroks = $this->addressService->getPuroksByBarangay($request->barangay_id);
        return response()->json($puroks);
    }

    /**
     * Get all account types
     *
     * @return JsonResponse
     */
    public function getAccountTypes(): JsonResponse
    {
        $accountTypes = $this->addressService->getAccountTypes();
        return response()->json($accountTypes);
    }

    /**
     * Get all water rates
     *
     * @return JsonResponse
     */
    public function getWaterRates(): JsonResponse
    {
        $waterRates = $this->addressService->getWaterRates();
        return response()->json($waterRates);
    }

    /**
     * Get application charge items
     *
     * @return JsonResponse
     */
    public function getApplicationCharges(): JsonResponse
    {
        $charges = $this->addressService->getApplicationCharges();
        return response()->json($charges);
    }
}

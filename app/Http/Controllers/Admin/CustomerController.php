<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CustomerHelper;
use App\Models\Status;
use App\Rules\Uppercase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Validation rules for customer creation
     */
    protected function validationRules(): array
    {
        return [
            // Customer fields
            'cust_last_name' => ['required', 'string', 'max:50', new Uppercase],
            'cust_first_name' => ['required', 'string', 'max:50', new Uppercase],
            'cust_middle_name' => ['nullable', 'string', 'max:50', new Uppercase],
            'land_mark' => ['nullable', 'string', 'max:100', new Uppercase],
            'c_type' => ['nullable', 'string', 'max:50', new Uppercase],
            // Status is set automatically to PENDING

            // Address fields
            'p_id' => 'nullable|integer',
            'b_id' => 'nullable|integer',
            't_id' => 'nullable|integer',
            'prov_id' => 'nullable|integer',
            'address_stat_id' => 'nullable|integer',
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        // Set PENDING status for new customers
        $validated['stat_id'] = Status::getIdByDescription(Status::PENDING);

        // Generate resolution number
        $validated['resolution_no'] = CustomerHelper::generateCustomerResolutionNumber(
            $validated['cust_first_name'],
            $validated['cust_last_name']
        );

        return $this->createCustomer($validated);
    }

    /**
     * Create customer record (synchronously)
     */
    private function createCustomer(array $validated)
    {
        try {
            return DB::transaction(function () use ($validated) {
                $address = CustomerHelper::createConsumerAddress($validated);
                $customer = CustomerHelper::createCustomer($validated, $address->ca_id);

                return response()->json([
                    'message' => 'Customer created successfully',
                    'data' => $customer->load('address')
                ], 200);
            });
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

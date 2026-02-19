<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Services\Search\CustomerSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerSearchController extends Controller
{
    public function __construct(
        private CustomerSearchService $searchService
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $results = $this->searchService->search($query);

        return response()->json($results);
    }
}

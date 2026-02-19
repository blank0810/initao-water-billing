<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Models\User;
use App\Services\DocumentSignatory\DocumentSignatoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentSignatoryController extends Controller
{
    public function __construct(
        private DocumentSignatoryService $signatoryService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        if ($request->wantsJson()) {
            $signatories = $this->signatoryService->getAllSignatories();

            return response()->json(['success' => true, 'data' => $signatories]);
        }

        session(['active_menu' => 'config-system-signatories']);

        return view('pages.admin.config.document-signatories.index');
    }

    public function update(Request $request, string $positionKey): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'position_title' => ['required', 'string', 'max:100'],
        ]);

        $signatory = $this->signatoryService->updateSignatory($positionKey, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Signatory updated successfully.',
            'data' => $signatory,
        ]);
    }

    public function getActiveUsers(): JsonResponse
    {
        $users = User::where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->select('id', 'name', 'signature_path')
            ->orderBy('name')
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'has_signature' => (bool) $u->signature_path,
            ]);

        return response()->json(['success' => true, 'data' => $users]);
    }

    public function getSignatoryDataForJs(Request $request): JsonResponse
    {
        $keys = $request->input('keys', []);
        $data = $this->signatoryService->resolveSignatureDataForJs($keys);

        return response()->json(['success' => true, 'data' => $data]);
    }
}

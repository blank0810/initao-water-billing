<?php

namespace App\Services\DocumentSignatory;

use App\Models\DocumentSignatory;
use Illuminate\Support\Collection;

class DocumentSignatoryService
{
    /**
     * Get all signatory positions with their assigned users.
     */
    public function getAllSignatories(): Collection
    {
        return DocumentSignatory::with('user')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Update a signatory position's assigned user and/or title.
     */
    public function updateSignatory(string $positionKey, array $data): DocumentSignatory
    {
        $signatory = DocumentSignatory::where('position_key', $positionKey)->firstOrFail();

        $signatory->update([
            'user_id' => $data['user_id'] ?? $signatory->user_id,
            'position_title' => $data['position_title'] ?? $signatory->position_title,
        ]);

        return $signatory->load('user');
    }

    /**
     * Resolve signature data for a given position key.
     */
    public function resolveSignatureData(string $positionKey): array
    {
        $signatory = DocumentSignatory::where('position_key', $positionKey)->first();

        if (! $signatory) {
            return [
                'name' => '',
                'title' => '',
                'signature_url' => null,
                'has_signature' => false,
            ];
        }

        $user = $signatory->resolveUser();

        return [
            'name' => $user?->name ?? '',
            'title' => $signatory->position_title,
            'signature_url' => $user?->signature_url,
            'has_signature' => (bool) $user?->signature_path,
        ];
    }

    /**
     * Get signature data with base64-encoded image for JS-generated documents.
     */
    public function resolveSignatureDataForJs(array $positionKeys): array
    {
        $result = [];

        foreach ($positionKeys as $key) {
            $data = $this->resolveSignatureData($key);

            if ($data['has_signature']) {
                $signatory = DocumentSignatory::where('position_key', $key)->first();
                $user = $signatory?->resolveUser();

                if ($user?->signature_path) {
                    $fullPath = public_path($user->signature_path);
                    if (file_exists($fullPath)) {
                        $mimeType = mime_content_type($fullPath);
                        $data['signature_base64'] = 'data:'.$mimeType.';base64,'.base64_encode(file_get_contents($fullPath));
                    }
                }
            }

            $result[$key] = $data;
        }

        return $result;
    }
}

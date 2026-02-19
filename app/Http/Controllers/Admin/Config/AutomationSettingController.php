<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\Admin\Config\AutomationSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutomationSettingController extends Controller
{
    public function __construct(private AutomationSettingService $service) {}

    public function index()
    {
        $settings = $this->service->getAutomationSettings();

        return view('pages.admin.config.automation-settings.index', compact('settings'));
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'in:'.implode(',', [
                SystemSetting::AUTO_CREATE_PERIOD,
                SystemSetting::AUTO_APPLY_PENALTIES,
                SystemSetting::AUTO_CLOSE_READING_SCHEDULE,
            ])],
            'value' => ['required', 'in:0,1'],
        ]);

        $result = $this->service->updateSetting($validated['key'], $validated['value']);

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}

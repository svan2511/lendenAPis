<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    protected $dashService;

    public function __construct(DashboardService $dashService)
    {
        $this->dashService = $dashService;
    }

      public function getData(Request $request)
    {
        try {
            
            $data = $this->dashService->getData();
            
        return ApiResponse::success('Getting data successfully!' , "data" ,$data);
        } catch (\Exception $e) {
            Log::error('getting data failed', [
                'error' => $e->getMessage()
            ]);
        return ApiResponse::error('Failed to reterive data. Please try again.', 500);
        }
    }

    
}
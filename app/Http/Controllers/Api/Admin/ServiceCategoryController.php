<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServiceCategoryController extends Controller
{
    public function index(): Response|Application|ResponseFactory
    {
        return $this->response(ServiceCategory::query()->with('services')->get());
    }

    public function store(Request $request): Response|Application|ResponseFactory
    {
        $request->validate([
            'name' => ['required', 'string'],
            'name_tj' => ['sometimes', 'string'],
        ]);

        return $this->response(ServiceCategory::query()->firstOrCreate($request->all()));
    }

    public function update(Request $request, ServiceCategory $serviceCategory): Response|Application|ResponseFactory
    {
        $request->validate([
            'name' => ['sometimes', 'string'],
            'name_tj' => ['sometimes', 'string']
        ]);

        $serviceCategory->update($request->all());

        return $this->response($serviceCategory);
    }

    public function destroy(ServiceCategory $serviceCategory): Response|Application|ResponseFactory
    {
        if ($serviceCategory->services()->exists()) {
            return $this->responseError("Thid service category has services. You can't delete");
        }

        $result = $serviceCategory->delete();

        if (!$result) {
            return $this->responseError('Something went wrong!');
        }

        return $this->response();
    }
}

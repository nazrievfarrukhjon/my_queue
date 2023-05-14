<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCenter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ServiceCenterController extends Controller
{
    public function index(Request $request): Response|Application|ResponseFactory
    {
        $search = $request->input('search');

        $serviceCenters = ServiceCenter::query();

        if ($search) {
            $serviceCenters->where('name', 'like', "%$search");
        }

        return $this->responsePaginate($serviceCenters->paginate());
    }

    public function store(Request $request): Response|Application|ResponseFactory
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string'
        ]);

        $request->merge(['slug' => Str::slug($request->name)]);

        return $this->response(ServiceCenter::query()->firstOrCreate($request->all()));
    }

    public function update(Request $request, ServiceCenter $serviceCenter): Response|Application|ResponseFactory
    {
        $request->validate([
            'name' => 'sometimes|string',
            'address' => 'sometimes|string'
        ]);

        $serviceCenter->update($request->all());

        return $this->response($serviceCenter);
    }

    public function destroy(ServiceCenter $serviceCenter): Response|Application|ResponseFactory
    {
        $result = $serviceCenter->delete();

        if (!$result) {
            return $this->responseError('Something went wrong!');
        }

        return $this->response();
    }

    public function list(): Response|Application|ResponseFactory
    {
        return $this->response(
            ServiceCenter::query()->get()
        );
    }
}

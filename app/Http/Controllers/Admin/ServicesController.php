<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Service\DestroyService;
use App\Http\Requests\Admin\Service\IndexService;
use App\Http\Requests\Admin\Service\StoreService;
use App\Http\Requests\Admin\Service\UpdateService;
use App\Models\Service;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;

class ServicesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexService $request
     * @return array|Application|Factory|View|Response
     */
    public function index(IndexService $request): View|Factory|Response|array|Application
    {
        $data = Service::query()->get();

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.service.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|Response
     */
    public function create(): View|Factory|Response|Application
    {
        return view('admin.service.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreService $request
     * @return array|Application|RedirectResponse|Response|Redirector
     */
    public function store(StoreService $request): Response|array|Redirector|Application|RedirectResponse
    {
        $sanitized = $request->validated();

        Service::query()->create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/services'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/services');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Service $service
     * @return Application|Factory|View|Response
     */
    public function edit(Service $service): View|Factory|Response|Application
    {
        return view('admin.service.edit', [
            'service' => $service,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateService $request
     * @param Service $service
     * @return array|Application|RedirectResponse|Redirector
     */
    public function update(UpdateService $request, Service $service): array|Redirector|Application|RedirectResponse
    {
        $sanitized = $request->getSanitized();

        $service->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/services'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/services');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyService $request
     * @param Service $service
     * @return bool|RedirectResponse|Response
     * @throws Exception
     */
    public function destroy(DestroyService $request, Service $service): Response|bool|RedirectResponse
    {
        $service->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param DestroyService $request
     * @return Response
     */
    public function bulkDestroy(DestroyService $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Service::query()->whereIn('id', $bulkChunk)->delete();
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}

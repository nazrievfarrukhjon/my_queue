<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Client\DestroyClient;
use App\Http\Requests\Admin\Client\IndexClient;
use App\Http\Requests\Admin\Client\StoreClient;
use App\Http\Requests\Admin\Client\UpdateClient;
use App\Models\Client;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;

class ClientsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexClient $request
     * @return array|Application|Factory|View|Response
     */
    public function index(IndexClient $request): View|Factory|Response|array|Application
    {
        $data = Client::query()->get();

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.client.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|Response
     */
    public function create(): View|Factory|Response|Application
    {
        return view('admin.client.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreClient $request
     * @return array|Application|RedirectResponse|Response|Redirector
     */
    public function store(StoreClient $request): Response|array|Redirector|Application|RedirectResponse
    {
        $client = Client::create($request->all());

        if ($request->ajax()) {
            return ['redirect' => url('admin/clients/'.$client->id), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/clients');
    }

    /**
     * Display the specified resource.
     *
     * @param Client $client
     * @return Application|Factory|View
     */
    public function show(Client $client): Application|View|Factory
    {
        $client = Client::with([
            'tickets.category',
            'tickets.status',
            'tickets.user',
        ])->find($client->id);

        return view('admin.client.show', [
            'client' => $client,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Client $client
     * @return Application|Factory|View|Response
     */
    public function edit(Client $client): View|Factory|Response|Application
    {

        return view('admin.client.edit', [
            'client' => $client,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateClient $request
     * @param Client $client
     * @return array|Application|RedirectResponse|Response|Redirector
     */
    public function update(UpdateClient $request, Client $client): Response|array|Redirector|Application|RedirectResponse
    {
        $client->update($request->all());

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/clients'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/clients');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyClient $request
     * @param Client $client
     * @return bool|RedirectResponse|Response
     * @throws Exception
     */
    public function destroy(DestroyClient $request, Client $client): Response|bool|RedirectResponse
    {
        $client->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param DestroyClient $request
     * @return Response
     */
    public function bulkDestroy(DestroyClient $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Client::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Client\{DestroyClient, IndexClient, StoreClient, UpdateClient};
use App\Models\Client;
use Illuminate\Contracts\{Foundation\Application, Routing\ResponseFactory, View\Factory, View\View};
use Illuminate\Http\{RedirectResponse, Response};
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
        $search = $request->input('search');

        $clients = Client::query();

        if ($search) {
            $clients->where('phone', 'like', "%$search")
                ->orWhere('name', 'like', "%$search")
                ->orWhere('surname', 'like', "%$search")
                ->orWhere('second_name', 'like', "%$search");
        }

        return $this->responsePaginate($clients->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreClient $request
     * @return array|Application|RedirectResponse|Response|Redirector
     */
    public function store(StoreClient $request): Response|array|Redirector|Application|RedirectResponse
    {
        $client = Client::query()->create($request->all());

        return $this->response($client);
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

        return $this->response($client);
    }

    public function update(UpdateClient $request, Client $client): Response|Application|ResponseFactory
    {
        $client->update($request->all());

        return $this->response($client);
    }

    public function destroy(DestroyClient $request, Client $client): Response|Application|ResponseFactory
    {
        $client->delete();

        return $this->response();
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

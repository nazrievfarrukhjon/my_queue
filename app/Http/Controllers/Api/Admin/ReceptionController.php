<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Client;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReceptionController extends Controller
{
    /**
     * CabinetController constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return Response|Application|ResponseFactory
     */
    public function index(): Response|Application|ResponseFactory
    {
        $serviceCategories = ServiceCategory::query()
            ->whereHas('services')
            ->with('services')
            ->get();

        $services = Service::query()->whereNull('category_id')->get();

        return $this->response(['services' => $serviceCategories->mergeRecursive($services)]);
    }

    public function store(Request $request): Application|ResponseFactory|Response
    {
        $client = Client::query()->firstOrCreate(['phone' => $request->input('phone')]);

        $service = Service::query()->findOrFail($request->input('service_id'));

        if (
            Ticket::query()
                ->where('client_id', $client->id)
                ->where('created_at', '>', Carbon::now()->subMinutes(10)->toDateTimeString())
                ->exists()
        ) {
            return $this->responseError("Вы уже есть в очереди!", 400);
        }

        $ticket = Ticket::query()->create([
            'service_id' => $service->id,
            'created_at' => Carbon::now(),
            'status_id' => 1,
            'comment' => $request->comment,
            'number' => Ticket::getNumber($service),
            'client_id' => $client->id
        ]);

        return $this->response($ticket);
    }

    public function skipAll(): Response|Application|ResponseFactory
    {
        if (!auth()->user()->hasRole('SuperAdmin')) {
            return $this->responseError("У вас нет доступа!", 403);
        }

        $today = Carbon::now()->toDateString() . " 00:00:00";

        Ticket::query()->where('created_at', '>=', Carbon::parse($today))
            ->where('status_id', 1)
            ->update([
                'status_id' => 4
            ]);

        return $this->response();
    }
}

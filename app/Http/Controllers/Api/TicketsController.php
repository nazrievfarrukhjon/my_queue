<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketStoreFormRequest;
use App\Models\Client;
use App\Models\MonitorGroups;
use App\Models\Service;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
   public function create(TicketStoreFormRequest $request): JsonResponse
   {
       $client = Client::where('phone', $request->phone)->firstOrFail();
       $service = Service::find($request->service_id);

       $monitorGroup = MonitorGroups::where('name', $request->monitor_group_name)->firstOrFail();
       $monitorGroup->queue_number++;
       $monitorGroup->save();

       $ticket = new Ticket([
           'client_id' => $client->id,
           'service_id' => $service->id,
           'user_id' => $request->user_id,
           'monitor_group_id' => $monitorGroup->id,
           'number' => $monitorGroup->queue_number,
           "created_at" => $request->created_at
       ]);

       return response()->json([
           'ticket_created' => $ticket->save(),
           'queue_number'=> $monitorGroup->queue_number
       ] , 200);
   }

    public function update(int $id, Request $request): JsonResponse
    {
        $ticket = Ticket::whereId($id)->firstOrFail();

        return response()->json(
            [$ticket->update($request->all())],
            200);
    }

    public function delete(int $id, Request $request)
    {
        Ticket::whereId($id)->delete();
    }

    public function getAll(Request $request): JsonResponse
    {
        $tickets = Ticket::paginate(10);

        return response()->json([
            'tickets' => $tickets,
        ] , 200);
    }

    public function getById(int $id, Request $request): JsonResponse
    {
        return response()->json([
            Ticket::whereId($id)->firstOrFail()
        ] , 200);
    }

    public function getByStatusId(int $statusId, Request $request): JsonResponse
    {
        return response()->json([
            Ticket::whereStatusId($statusId)->firstOrFail()
        ] , 200);
    }

    public function getByUserId(int $userId, Request $request): JsonResponse
    {
        return response()->json([
            Ticket::whereUserId($userId)->firstOrFail()
        ] , 200);
    }

}

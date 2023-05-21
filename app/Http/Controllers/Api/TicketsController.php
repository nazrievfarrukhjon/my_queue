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
        $tickets = Ticket::query()
            ->with(['user', 'status']);

        if (isset($request->priority))
            $tickets->wherePriority($request->priority);

        if (isset($request->service_id))
            $tickets->whereInvitedAt($request->service_id);

        if (isset($request->status_id))
            $tickets->whereStatusId($request->status_id);

        if (isset($request->user_id))
            $tickets->whereuserId($request->user_id);

        if (isset($request->created_at))
            $tickets->whereInvitedAt($request->created_at);

        if (isset($request->invited_at))
            $tickets->whereInvitedAt($request->invited_at);

        if (isset($request->completed_at))
            $tickets->whereCompletedAt($request->completed_at);

        if (isset($request->client_id))
            $tickets->whereClientId($request->client_id);

        if (isset($request->monitor_group_id))
            $tickets->whereMonitorGroupId($request->monitor_group_id);


        return response()->json([
            'tickets' => $tickets->paginate(10),
        ] , 200);
    }

    public function getById(int $id): JsonResponse
    {
        return response()->json([
            Ticket::whereId($id)->firstOrFail()
        ] , 200);
    }

}

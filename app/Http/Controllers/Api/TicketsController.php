<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketStoreFormRequest;
use App\Models\Client;
use App\Models\MonitorGroups;
use App\Models\Service;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;

class TicketsController extends Controller
{

   public function create(TicketStoreFormRequest $request):JsonResponse {

       $client = Client::where('phone', $request->phone)->first();
       $service = Service::find($request->service_id);

       $monitorGroup = MonitorGroups::where('name', $request->monitor_group_name)->first();
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

       return response()->json(['ticket_created' => $ticket->save()], 200);
   }

}

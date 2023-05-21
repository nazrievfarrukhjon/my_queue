<?php

namespace App\Http\Controllers\Api;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonitorController extends Controller
{

    public function getInvitedTickets(): JsonResponse
    {
        $tickets = Ticket::query()
            ->with(['user', 'status'])
            ->where('status_id', Status::invited->value)
            ->whereNotNull('invited_at')
            ->get();

        return response()->json($tickets);
    }

    public function getByMonitorGroupId(Request $request): JsonResponse
    {
        $tickets = Ticket::query();

        if (isset($request->priority))
            $tickets->wherePriority($request->priority);

        if (isset($request->service_id))
            $tickets->whereServiceId($request->service_id);

        if (isset($request->status_id))
            $tickets->whereStatusId($request->status_id);

        if (isset($request->user_id))
            $tickets->whereUserId($request->user_id);

        if (isset($request->created_at))
            $tickets->whereCreatedAt($request->created_at);

        if (isset($request->invited_at))
            $tickets->whereInvitedAt($request->invited_at);

        if (isset($request->completed_at))
            $tickets->whereCompletedAt($request->completed_at);

        if (isset($request->client_id))
            $tickets->whereClientId($request->client_id);

        if (isset($request->monitor_group_id))
            $tickets->whereMonitorGroupId($request->monitor_group_id);


        return response()->json([
            'tickets' => $tickets
                ->limit(7)
                ->get(),
        ] , 200);
    }

}

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

    public function getByMonitorGroupId(Request $request)
    {
        $tickets = Ticket::query()
            ->where('monitor_group_id', $request->monitor_group_id)
            ->get();

        return response()->json($tickets);
    }

}

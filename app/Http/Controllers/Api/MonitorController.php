<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MonitorController extends Controller
{

    public function index(Request $request): Application|ResponseFactory|Response
    {
        $tickets = Ticket::query()
            ->with(['user', 'status'])
            ->where('status', 2)
            ->whereNotNull('invited_at')
            ->get();

        return $this->response($tickets);
    }

    public function getByMonitorId(Request $request)
    {
        $tickets = Ticket::query()
            ->where('monitor_id', $request->monitor_id)
            ->get();

        return $this->response($tickets);
    }

}

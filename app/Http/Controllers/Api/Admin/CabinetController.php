<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CabinetController extends Controller
{
    public function __construct()
    {
        $this->middleware(['perm:works']);
    }

    public function index(Request $request): Response|Application|ResponseFactory
    {
        $user = Auth::user();

        $today = Carbon::now()->toDateString() . " 00:00:00";

        $tickets = Ticket::with(['status', 'user', 'client', 'service'])
            ->where('created_at', '>=', Carbon::parse($today))
            ->whereIn('status_id', [1, 2])
            ->where('service_id', $user->getServicesId())
            ->orderByDesc('priority')
            ->get();

        $currentTicket = Ticket::where('created_at', '>=', Carbon::parse($today))
            ->where('status_id', 3)
            ->where('user_id', $user->id)
            ->with(['status', 'user', 'client', 'service'])
            ->get();

        $completedTickets = Ticket::where('created_at', '>=', Carbon::parse($today))
            ->where('status_id', 4)
            ->where('user_id', $user->id)
            ->with(['status', 'user', 'client', 'service'])
            ->orderBy('completed_at', 'desc')
            ->get();

        return $this->response(compact('tickets', 'currentTicket', 'completedTickets'));
    }

    public function services(): Response|Application|ResponseFactory
    {
        return $this->response(Service::all());
    }

    public function invite(): Application|ResponseFactory|Response
    {
        $user = Auth::user();

        if (Ticket::query()->where('status_id', 2)
            ->where('user_id', $user->id)
            ->exists()
        ) {
            return $this->responseError('У вас приглашенный клиент.');
        }

        $today = Carbon::now()->toDateString() . " 00:00:00";

        $ticket = Ticket::with(['status', 'user', 'client'])
            ->where('created_at', '>=', Carbon::parse($today))
            ->where('status_id', 1)
            ->where('service_id', $user->getServicesId())
            ->orderByDesc('priority')
            ->firstOrFail();

        if (!is_null($ticket)) {
            $ticket->status_id = 2;
            $ticket->user_id = $user->id;
            $ticket->save();

            return $this->response($ticket);
        }

        return $this->responseUnsuccess('Something went wrong!');
    }

    public function accept(): Application|ResponseFactory|Response
    {
        $user = Auth::user();

        if (Ticket::query()->where('status_id', 3)
            ->where('user_id', $user->id)
            ->exists()
        ) {
            return $this->responseError('У вас есть клиент для обслуживание.');
        }

        $ticket = Ticket::with(['status', 'user', 'client'])
            ->where('status_id', 2)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (!is_null($ticket)) {
            $ticket->status_id = 3;
            $ticket->user_id = $user->id;
            $ticket->invited_at = Carbon::now();
            $ticket->save();

            return $this->response($ticket);
        }

        return $this->responseUnsuccess('Something went wrong!');
    }

    public function done(Request $request): Response|Application|ResponseFactory
    {
        $ticket = Ticket::query()
            ->where('user_id', $request->user()->id)
            ->where('status_id', 3)
            ->firstOrFail();

        if (!is_null($ticket)) {
            $ticket->status_id = 4;
            $ticket->completed_at = Carbon::now();
            $ticket->save();

            return $this->response($ticket);
        }

        return $this->responseUnsuccess('Something went wrong!');
    }

    public function saveTicket(Request $request)
    {
        $ticket = Ticket::find($request->input('ticket_id'));
        $ticket->status_id = 3;
        $ticket->completed_at = Carbon::now();
        $ticket->service_id = $request->data['service_id'];
        $ticket->comment = $request->data['comment'];
        $ticket->save();

        $client = Client::findOrFail($request->data['id']);
        $client->phone = $request->data['phone'];
        $client->surname = $request->data['surname'];
        $client->name = $request->data['name'];
        $client->second_name = $request->data['second_name'];
        $client->tin = $request->data['tin'];
        $client->passport = $request->data['passport'];
        $client->address = $request->data['address'];
        $client->date_of_birth = $request->data['date_of_birth'];
        $client->save();

        return $this->response();
    }

}

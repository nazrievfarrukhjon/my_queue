<?php


namespace App\Http\Controllers\Admin;


use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use function PHPUnit\Framework\isNull;

class CabinetController extends Controller
{
    /**
     * CabinetController constructor.
     */
    public function __construct()
    {
        $this->middleware(['perm:works']);
    }

    /**
     * @param Request $request
     * @return array|Factory|View
     */
    public function index(Request $request): Factory|array|View
    {
        $user = Auth::user();

        $today = Carbon::now()->toDateString() . " 00:00:00";

        $category = $user->category()->first();

        $tickets = Ticket::with(['status', 'user', 'client'])
            ->where('created_at', '>=', Carbon::parse($today))
            ->whereIn('status_id', [Status::pending->value, Status::invited->value])
            ->where('user_id', $user->id)
            ->get();

        $ticket = $user->tickets()->where('created_at', '>=', Carbon::parse($today))
            ->where('status_id', Status::invited->value)
            ->with(['status', 'user', 'client'])
            ->first();

        $completedTickets = Ticket::where('created_at', '>=', Carbon::parse($today))
            ->where('status_id', Status::active->value)
            ->where('user_id', $user->id)
            ->with(['status', 'client'])
            ->orderBy('completed_at', 'desc')
            ->get();

        if (is_null($category)) {
            return view('404-admin-page', ['message' => 'Category not found!']);
        }

        $data = compact('today', 'user', 'category', 'ticket', 'tickets', 'completedTickets');

        if ($request->ajax()) {
            return $data;
        }

        //return $data;

        return view('admin.cabinet.index', $data);
    }

    public function services()
    {
        return Service::all();
    }

    /**
     * @return array
     */
    public function accept(): array
    {
        $user = Auth::user();
        $today = Carbon::now()->toDateString() . " 00:00:00";

        $tickets = Ticket::with(['status', 'user', 'client'])
            ->where('created_at', '>=', Carbon::parse($today))
            ->whereIn('status_id', [Status::pending->value])
            ->where('user_id', $user->id)
            ->get();

        if (count($tickets) > 0) {
            $currentTicket = $tickets->first();
            $currentTicket->status_id = Status::invited->value;
            $currentTicket->user_id = $user->id;
            $currentTicket->invited_at = Carbon::now();
            $currentTicket->save();
            $success = true;

            return compact('currentTicket', 'success');
        }

        $success = false;

        return compact('success');
    }

    public function done(Request $request)
    {
        $ticket = Ticket::find($request->input('ticketId'));
        $ticket->status_id = Status::active->value;
        $ticket->completed_at = Carbon::now();
        $ticket->save();

    }

    public function saveTicket(Request $request)
    {
        $ticket = Ticket::find($request->input('ticketId'));
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

    }

}

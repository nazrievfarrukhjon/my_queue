<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function index(Request $request): Application|ResponseFactory|Response
    {
        Carbon::setWeekStartsAt(Carbon::MONDAY);

        $y = $request->input('y', Carbon::now()->year);
        $m = $request->input('m', Carbon::now()->month);

        $dateStart = Carbon::createFromFormat('Y-m-d', "{$y}-{$m}-01");
        $dateEnd = Carbon::createFromFormat('Y-m-d', "{$y}-{$m}-32");

        $ticketsByDates = Ticket::where('created_at', '>=', $dateStart)
            ->where('created_at', '<=', $dateEnd)
            ->with('service')
            ->groupBy(['date'])
            ->orderBy('date', 'DESC')
            ->get([
                DB::raw('Date(created_at) as date'),
                DB::raw('COUNT(*) as "tickets"')
            ])->sortBy('date');

        $ticketsByDate = [];
        foreach ($ticketsByDates as $item) {
            $ticketsByDate[$item->date][] = $item;
        }

        $ticketsByService = Ticket::where('created_at', '>=', $dateStart)
            ->where('created_at', '<=', $dateEnd)
            ->with('service')
            ->groupBy(['service_id'])
            ->get([
                DB::raw('service_id'),
                DB::raw('COUNT(*) as "tickets"')
            ]);

        $alltotal = Ticket::where('created_at', '>=', $dateStart)
            ->where('created_at', '<=', $dateEnd)->count();

        $totalToday = Ticket::getTodays();
        $users = User::whereNotNull('service_center_id')->count();

        return $this->response(
            compact('totalToday', 'ticketsByService', 'users', 'alltotal', 'ticketsByDate', 'y', 'm')
        );
    }
}

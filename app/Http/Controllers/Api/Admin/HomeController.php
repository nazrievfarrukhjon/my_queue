<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request): Factory|View
    {
        Carbon::setWeekStartsAt(Carbon::MONDAY);

        $y = $request->input('y', Carbon::now()->year);
        $m = $request->input('m', Carbon::now()->month);

        $dateStart = Carbon::createFromFormat('Y-m-d', "{$y}-{$m}-01");
        $dateEnd = Carbon::createFromFormat('Y-m-d', "{$y}-{$m}-32");

        $ticketsByDates = Ticket::where('created_at', '>=', $dateStart)
            ->where('created_at', '<=', $dateEnd)
            ->with('category')
            ->groupBy(['date', 'category_id'])
            ->orderBy('date', 'DESC')
            ->get([
                DB::raw('Date(created_at) as date'),
                DB::raw('category_id'),
                DB::raw('COUNT(*) as "tickets"')
            ])->sortBy('date');

        $ticketsByDate = [];
        foreach ($ticketsByDates as $item) {
            $ticketsByDate[$item->date][] = $item;
        }

        $ticketsByCategory = Ticket::where('created_at', '>=', $dateStart)
            ->where('created_at', '<=', $dateEnd)
            ->with('category')
            ->groupBy(['category_id'])
            ->get([
                DB::raw('category_id'),
                DB::raw('COUNT(*) as "tickets"')
            ]);

        $alltotal = Ticket::where('created_at', '>=', $dateStart)
            ->where('created_at', '<=', $dateEnd)->count();

        $totalToday = Ticket::getTodays();
        $categories = ServiceCategory::count();
        $users = User::whereNotNull('category_id')->count();

        return view(
            'admin.index',
            compact('totalToday', 'ticketsByCategory', 'categories', 'users', 'alltotal', 'ticketsByDate', 'y', 'm')
        );
    }
}

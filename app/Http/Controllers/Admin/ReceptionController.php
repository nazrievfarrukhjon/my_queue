<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\Client;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReceptionController extends Controller
{
    /**
     * CabinetController constructor.
     */
    public function __construct()
    {
    }

    public function index(): Factory|View|Application
    {
        $categories = ServiceCategory::all();

        $text = '';

        return view('admin.reception.index', compact('categories', 'text'));
    }

    /**
     * @param Request $request
     * @return array
     */
    public function store(Request $request): array
    {
        Client::firstOrCreate(['phone' => $request->input('phone')]);

        $category = ServiceCategory::findOrFail($request->input('category_id'));

        $ticket = Ticket::create([
            'category_id' => $category->id,
            'created_at' => Carbon::now(),
            'status_id' => 1,
            'comment' => '',
            'number' => Ticket::getNumber($category),
        ]);

        return compact('ticket');
    }

    public function skipAll()
    {
        $today = Carbon::now()->toDateString() . " 00:00:00";

        $tickets = Ticket::where('created_at', '>=', Carbon::parse($today))
            ->where('status_id', 1)
            ->get();

        foreach ($tickets as $ticket) {
            $ticket->status_id = 4;
            $ticket->save();
        }
    }


}

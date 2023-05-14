<?php

namespace App\Http\Controllers\Monitor;


use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitorController extends Controller
{
    /**
     * @param Request $request
     * @param string $grad
     * @param int $size
     * @return array|Factory|View
     */
    public function index(Request $request, string $grad = "0", int $size = 14): Factory|array|View
    {
        $tickets = Ticket::query()
            ->with(['category', 'client', 'client', 'status'])
            ->get();

//        $today = Carbon::now()->toDateString() . " 00:00:00";
//
//        $categories = Category::with(['tickets' => function ($query) use ($today) {
//            $query->where('created_at', '>=', Carbon::parse($today))
//                ->whereIn('status_id', [1, 2])
//                ->with(['status', 'user']);
//        }, 'users'])->get();
//
//        $users = User::query()->select('users.*')->join('categories', 'users.category_id', '=', 'categories.id')
//            ->with(['tickets' => function ($query) use ($today) {
//                $query->where('created_at', '>=', Carbon::parse($today))
//                    ->whereIn('status_id', [1, 2])
//                    ->with(['status']);
//            }, 'category'])
////            ->where('users.id', '<>', 1)
//            ->get();
//
//        if ($request->ajax()) {
//            return compact('categories', 'users');
//        }
//
//        if ($size == '') $size = 14;
//        if ($size < 5) $size = 5;
//        if ($size > 44) $size = 44;
//
//        if ($grad != "90" && $grad != "-90") $grad = "";

        return view("monitor.index", compact('tickets'));
    }

    /**
     * @param Request $request
     * @return array|Factory|View
     */
    public function index90(Request $request): Factory|array|View
    {
        $today = Carbon::now()->toDateString() . " 00:00:00";

        $categories = ServiceCategory::with(['tickets' => function ($query) use ($today) {
            $query->where('created_at', '>=', Carbon::parse($today))
                ->whereIn('status_id', [1, 2])
                ->with(['status', 'user']);
        }, 'users'])->get();

        $users = User::select('users.*')->join('categories', 'users.category_id', '=', 'categories.id')
            ->with(['tickets' => function ($query) use ($today) {
                $query->where('created_at', '>=', Carbon::parse($today))
                    ->whereIn('status_id', [1, 2])
                    ->with(['status']);
            }, 'category'])
            ->where('users.id', '<>', 1)
            ->get();

        if ($request->ajax()) {
            return compact('categories', 'users');
        }

        return view('monitor.index90', compact('categories', 'users'));
    }

    /**
     * @param Request $request
     * @return array|Factory|View
     */
    public function indexMinus90(Request $request): Factory|array|View
    {
        $today = Carbon::now()->toDateString() . " 00:00:00";

        $categories = ServiceCategory::with(['tickets' => function ($query) use ($today) {
            $query->where('created_at', '>=', Carbon::parse($today))
                ->whereIn('status_id', [1, 2])
                ->with(['status', 'user']);
        }, 'users'])->get();

        $users = User::select('users.*')->join('categories', 'users.category_id', '=', 'categories.id')
            ->with(['tickets' => function ($query) use ($today) {
                $query->where('created_at', '>=', Carbon::parse($today))
                    ->whereIn('status_id', [1, 2])
                    ->with(['status']);
            }, 'category'])
            ->where('users.id', '<>', 1)
            ->get();

        if ($request->ajax()) {
            return compact('categories', 'users');
        }

        return view('monitor.index-90', compact('categories', 'users'));
    }
}

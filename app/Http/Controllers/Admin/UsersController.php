<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\DestroyUser;
use App\Http\Requests\Admin\User\IndexUser;
use App\Http\Requests\Admin\User\StoreUser;
use App\Http\Requests\Admin\User\UpdateUser;
use App\Models\ServiceCategory;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

class UsersController extends Controller
{

    /**
     * Guard used for admin user
     *
     * @var string
     */
    protected mixed $guard = 'admin';

    /**
     * UsersController constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->guard = config('admin-auth.defaults.guard');

        $this->middleware('perm:users.crud');
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexUser $request
     * @return array|Application|Factory|View
     */
    public function index(IndexUser $request): View|Factory|array|Application
    {
        $data = User::query()->with(['roles', 'categories'])->get();

        if ($request->ajax()) {
            return ['data' => $data, 'activation' => Config::get('admin-auth.activation_enabled')];
        }

        return view('admin.user.index', ['data' => $data, 'activation' => Config::get('admin-auth.activation_enabled')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return view('admin.user.create', [
            'activation' => Config::get('admin-auth.activation_enabled'),
            'roles' => Role::query()->where('guard_name', $this->guard)->get(),
            'categories' => ServiceCategory::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUser $request
     * @return array|Application|RedirectResponse|Response|Redirector
     */
    public function store(StoreUser $request): Response|array|Redirector|Application|RedirectResponse
    {
        // Sanitize input
        $sanitized = $request->getModifiedData();

        //return $sanitized;

        // Store the User
        $adminUser = User::query()->create($sanitized);

        // But we do have a roles, so we need to attach the roles to the adminUser
        $adminUser->roles()->sync(collect($request->input('roles', []))->map->id->toArray());

        if ($request->ajax()) {
            return ['redirect' => url('admin/admin-users'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/users');
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Application|Factory|View
     */
    public function show(Request $request, User $user): View|Factory|Application
    {
        Carbon::setWeekStartsAt(CarbonInterface::MONDAY);

        $y = $request->input('y', Carbon::now()->year);
        $m = $request->input('m', Carbon::now()->month);

        $dateStart = Carbon::createFromFormat('Y-m-d', "$y-$m-01");
        $dateEnd = Carbon::createFromFormat('Y-m-d', "$y-$m-32");

        $ticketsByDates = Ticket::query()->where('created_at', '>=', $dateStart)
            ->where('created_at', '<=', $dateEnd)
            ->where('user_id', $user->id)
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

        $ticketsByCategory = Ticket::query()->where('created_at', '>=', $dateStart)
            ->where('created_at', '<=', $dateEnd)
            ->where('user_id', $user->id)
            ->with('category')
            ->groupBy(['category_id'])
            ->get([
                DB::raw('category_id'),
                DB::raw('COUNT(*) as "tickets"')
            ]);

        $total = Ticket::query()->where('created_at', '>=', $dateStart)->where('user_id', $user->id)
            ->where('created_at', '<=', $dateEnd)->count();

        $totalToday = Ticket::getTodaysByUser($user);

        return view('admin.user.show', [
            'user' => $user,
            'y' => $y,
            'm' => $m,
            'total' => $total,
            'ticketsByCategory' => $ticketsByCategory,
            'ticketsByDate' => $ticketsByDate,
            'totalToday' => $totalToday,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $adminUser
     * @return Application|Factory|View
     */
    public function edit(User $adminUser): Application|Factory|View
    {
        $adminUser->load('roles');

        return view('admin.admin-user.edit', [
            'adminUser' => $adminUser,
            'activation' => Config::get('admin-auth.activation_enabled'),
            'roles' => Role::query()->where('guard_name', $this->guard)->get(),
            'categories' => ServiceCategory::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUser $request
     * @param User $adminUser
     * @return array|Application|RedirectResponse|Response|Redirector
     */
    public function update(UpdateUser $request, User $adminUser): Response|array|Redirector|Application|RedirectResponse
    {
        $sanitized = $request->getModifiedData();

        $adminUser->update($sanitized);

        if ($request->input('roles')) {
            $adminUser->roles()->sync(collect($request->input('roles', []))->map->id->toArray());
        }

        if ($request->ajax()) {
            return ['redirect' => url('admin/admin-users'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/admin-users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyUser $request
     * @param User $adminUser
     * @return bool|RedirectResponse|Response
     * @throws Exception
     */
    public function destroy(DestroyUser $request, User $adminUser): Response|bool|RedirectResponse
    {
        $adminUser->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

}

<?php

namespace App\Http\Controllers\Api\Admin;

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
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
     * @param IndexUser $request
     * @return Response|Application|ResponseFactory
     */
    public function index(IndexUser $request): Response|Application|ResponseFactory
    {
        $limit = $request->input('limit', 5);

        $data = User::query()
            ->with(['roles', 'services'])
            ->orderBy($request->input('order_by', 'id'), $request->input('order_direction', 'asc'));

        if ($request->input('search')) {
            $data->where('first_name', 'like', "%{$request->input('search')}%")
                ->orWhere('last_name', 'like', "%{$request->input('search')}%")
                ->orWhere('public_id', 'like', "%{$request->input('search')}%");
        }

        $data = $data->paginate($limit);

        return $this->responsePaginate($data);
    }

    /**
     * @param StoreUser $request
     * @return Response|Application|ResponseFactory
     */
    public function store(StoreUser $request): Response|Application|ResponseFactory
    {
        $request->merge(['password' => Hash::make($request->input('password'))]);

        $user = User::query()->create($request->all());

        event(new Registered($user));

        $user->roles()->sync($request->input('roles'));

        $user->services()->sync($request->input('services'));

        return $this->response($user->with(['roles', 'services'])->find($user->id));
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Response|Application|ResponseFactory
     */
    public function show(Request $request, User $user): Response|Application|ResponseFactory
    {
        Carbon::setWeekStartsAt(CarbonInterface::MONDAY);

        $y = $request->input('y', Carbon::now()->year);
        $m = $request->input('m', Carbon::now()->month);

        $dateStart = Carbon::createFromFormat('Y-m-d', "$y-$m-01");
        $dateEnd = Carbon::createFromFormat('Y-m-d', "$y-$m-32");

        $ticketsByDates = Ticket::query()->where('created_at', '>=', $dateStart)
            ->where('created_at', '<=', $dateEnd)
            ->where('user_id', $user->id)
            ->with('service')
            ->groupBy(['date', 'service_id'])
            ->orderBy('date', 'DESC')
            ->get([
                DB::raw('Date(created_at) as date'),
                DB::raw('service_id'),
                DB::raw('COUNT(*) as "tickets"')
            ])->sortBy('date');

        $ticketsByDate = [];
        foreach ($ticketsByDates as $item) {
            $ticketsByDate[$item->date][] = $item;
        }

        $ticketsByCategory = Ticket::query()->where('created_at', '>=', $dateStart)
            ->where('created_at', '<=', $dateEnd)
            ->where('user_id', $user->id)
            ->with('service')
            ->groupBy(['service_id'])
            ->get([
                DB::raw('service_id'),
                DB::raw('COUNT(*) as "tickets"')
            ]);

        $total = Ticket::query()->where('created_at', '>=', $dateStart)->where('user_id', $user->id)
            ->where('created_at', '<=', $dateEnd)->count();

        $totalToday = Ticket::getTodaysByUser($user);

        return $this->response([
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
     * @param UpdateUser $request
     * @param User $user
     * @return Response|Application|ResponseFactory
     */
    public function update(UpdateUser $request, User $user): Response|Application|ResponseFactory
    {
        $user->update($request->all());

        if ($user->id != auth()->user()->id) {
            $user->roles()->sync($request->input('roles', []));
        }

        $user->services()->sync($request->input('services', []));

        return $this->response($user->with(['roles', 'services'])->find($user->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyUser $request
     * @param User $user
     * @return bool|RedirectResponse|Response
     * @throws Exception
     */
    public function destroy(DestroyUser $request, User $user): Response|bool|RedirectResponse
    {
        $user->delete();

        return $this->response();
    }

}

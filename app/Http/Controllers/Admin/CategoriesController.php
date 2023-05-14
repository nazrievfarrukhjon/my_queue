<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\DestroyCategory;
use App\Http\Requests\Admin\Category\IndexCategory;
use App\Http\Requests\Admin\Category\StoreCategory;
use App\Http\Requests\Admin\Category\UpdateCategory;
use App\Models\ServiceCategory;
use App\Models\Ticket;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['perm:categories.crud']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexCategory $request
     * @return array|Application|Factory|View|Response
     */
    public function index(IndexCategory $request): View|Factory|Response|array|Application
    {
        $data = ServiceCategory::query()->get();

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.category.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        //$this->authorize('admin.category.create');

        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCategory $request
     * @return array|Application|RedirectResponse|Response|Redirector
     */
    public function store(StoreCategory $request): Response|array|Redirector|Application|RedirectResponse
    {
        // Sanitize input
        $sanitized = $request->validated();

        // Store the Category
        $category = ServiceCategory::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/categories'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/categories');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ServiceCategory $category
     * @return Application|Factory|View|Response
     */
    public function edit(ServiceCategory $category): View|Factory|Response|Application
    {
        //$this->authorize('admin.category.edit', $category);

        return view('admin.category.edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCategory $request
     * @param ServiceCategory $category
     * @return array|Application|RedirectResponse|Response|Redirector
     */
    public function update(UpdateCategory $request, ServiceCategory $category): Response|array|Redirector|Application|RedirectResponse
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Category
        $category->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/categories'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/categories');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCategory $request
     * @param ServiceCategory $category
     * @return bool|RedirectResponse|Response
     * @throws Exception
     */
    public function destroy(DestroyCategory $request, ServiceCategory $category): Response|bool|RedirectResponse
    {
        $users = User::where('category_id', $category->id)->get();

        foreach ($users as $user) {
            $user->category_id = null;
            $user->save();
        }

        Ticket::where('category_id', $category->id)->delete();

        $category->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param DestroyCategory $request
     * @return Response
     */
    public function bulkDestroy(DestroyCategory $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    ServiceCategory::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}

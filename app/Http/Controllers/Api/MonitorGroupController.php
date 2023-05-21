<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonitorGroups;
use Illuminate\Http\Request;

class MonitorGroupController extends Controller
{
    public function get()
    {
        return MonitorGroups::all();
    }

    public function create(Request $request)
    {
        return MonitorGroups::create([
            'name' => $request->name,
            'series' => $request->series
        ]);
    }

    public function update(int $id, Request $request)
    {
        $monitorGroup = MonitorGroups::whereId($id)->firstOrFail();
        $monitorGroup->name = $request->name;
        $monitorGroup->series = $request->series;

        return response()->json($monitorGroup->save());
    }

    public function delete(int $id)
    {
        MonitorGroups::whereId($id)->delete();
    }
}

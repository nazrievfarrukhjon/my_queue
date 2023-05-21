<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function get()
    {
        return Device::all();
    }

    public function register(Request $request)
    {
        return Device::create([
            'device_uuid' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
            'token' => password_hash(rand(100, 10000), PASSWORD_BCRYPT),
            'type' => $request->type,

        ]);
    }

    public function update(int $id, Request $request)
    {
        $device = Device::find($id);

        return $device->update([
            'token' => password_hash(rand(100, 10000), PASSWORD_BCRYPT),
            'type'=> $request->type,
        ]);
    }

    public function delete(int $id)
    {
        Device::whereId($id)->delete();
    }
}

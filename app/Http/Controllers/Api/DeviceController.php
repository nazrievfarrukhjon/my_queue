<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function get()
    {
        return Device::all();
    }

    public function create(Request $request)
    {
        return Device::create([
            'device_uuid' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
            'token' => password_hash(rand(100, 10000), PASSWORD_BCRYPT),
            'type' => $request->type,
        ]);
    }

    public function update(string $uuid, Request $request)
    {
        $device = Device::where('device_uuid', $uuid)->firstOrFail();

        $device->token = $this->getToken($device, $request);
        $device->type = $request->type;

        return response()->json($device->save());
    }

    public function delete(string $uuid)
    {
        Device::whereDeviceUuid($uuid)->delete();
    }

    //if token  fiels is not null it means token needed to be changed
    public function getToken($device, Request $request): ?string
    {
        $token = $device?->token;

        if (isset($request->token)) {
            $token = password_hash(rand(100, 10000), PASSWORD_BCRYPT);
        }

        return $token;
    }
}

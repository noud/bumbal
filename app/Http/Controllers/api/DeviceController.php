<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\api\ApiController;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
* Class DeviceController
* @package App\Http\Controllers\api
*/
class DeviceController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function paginate(Request $request)
    {
        $page = $request->get('page');
        $devices = Cache::get('devices_page_' . $page);
        if ($devices === null) {
            $devices = Device::paginate($perPage = 5, ['*'], 'page', $page);
            Cache::put('devices_page_' . $page, $devices, 60);
        }

        return response()->json([
            'status' => 'success',
            'devices' => $devices,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'required|integer',
        ]);

        $device = Device::create([
            'name' => $request->name,
            'employee_id' => $request->employee_id,
        ]);
        Cache::put('device_' . $device->id, $device, 60);

        return response()->json([
            'status' => 'success',
            'message' => 'Device created successfully',
            'device' => $device,
        ]);
    }

    public function show($id)
    {
        $device = Cache::get('device_' . $id);
        if ($device === null) {
            $device = Device::find($id);
            if (!$device) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Device does not exist.',
                ]);                          
            }
            Cache::put('device_' . $id, $device, 60);
        }
        return response()->json([
            'status' => 'success',
            'device' => $device,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'required|integer',
        ]);

        $device = Cache::get('device_' . $id);
        if ($device === null) {
            $device = Device::find($id);
            if (!$device) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Device does not exist.',
                ]);
            }
        }
        $device->name = $request->name;
        $device->employee_id = $request->employee_id;
        $device->save();
        Cache::put('device_' . $id, $device, 60);

        return response()->json([
            'status' => 'success',
            'message' => 'Device updated successfully',
            'device' => $device,
        ]);
    }

    public function destroy($id)
    {
        $device = Device::find($id);
        if (!$device) {
            return response()->json([
                'status' => 'error',
                'message' => 'Device does not exist.',
            ]);
        }
        $device->delete();
        Cache::forget('device_' . $id);

        return response()->json([
            'status' => 'success',
            'message' => 'Device deleted successfully',
            'device' => $device,
        ]);
    }
}

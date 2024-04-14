<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\api\ApiController;
use App\Models\Device;
use Illuminate\Http\Request;

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

    public function index()
    {
        $devices = Device::all();
        return response()->json([
            'status' => 'success',
            'devices' => $devices,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $device = Device::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Device created successfully',
            'device' => $device,
        ]);
    }

    public function show($id)
    {
        $device = Device::find($id);
        return response()->json([
            'status' => 'success',
            'device' => $device,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $device = Device::find($id);
        $device->name = $request->name;
        $device->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Device updated successfully',
            'device' => $device,
        ]);
    }

    public function destroy($id)
    {
        $device = Device::find($id);
        $device->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Device deleted successfully',
            'device' => $device,
        ]);
    }
}

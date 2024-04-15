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

    public function paginate(Request $request)
    {
        $page = $request->get('page');
        $devices = Device::paginate($perPage = 5, ['*'], 'page', $page);
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
            'employee_id' => 'required|integer',
        ]);

        $device = Device::find($id);
        $device->name = $request->name;
        $device->employee_id = $request->employee_id;
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

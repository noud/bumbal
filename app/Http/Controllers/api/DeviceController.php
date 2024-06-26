<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\api\ApiController;
use App\Models\Device;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

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
            Cache::forever('devices_page_' . $page, $devices);
        }

        return response()->json([
            'status' => 'success',
            'devices' => $devices,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'employee_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation fails',
                'error' => $validator->errors()->messages(),
            ], 422);       
        }

        // check if employee exists
        $employee = Cache::get('employee_' . $request->employee_id);
        if ($employee === null) {
            $employee = Employee::find($request->employee_id);
            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee does not exist',
                ], 422);  
            }                        
            Cache::forever('employee_' . $request->employee_id, $employee);
        }
     
        $device = Device::create([
            'name' => $request->name,
            'employee_id' => $request->employee_id,
        ]);
        Cache::forever('device_' . $device->id, $device);
        $this->forgetDevicesPageCaches();

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
                    'message' => 'Device does not exist',
                ], 422);                          
            }
            Cache::forever('device_' . $id, $device);
        }
        return response()->json([
            'status' => 'success',
            'device' => $device,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'employee_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation fails',
                'error' => $validator->errors()->messages(),
            ], 422);       
        }

        // check if employee exists
        $employee = Cache::get('employee_' . $request->employee_id);
        if ($employee === null) {
            $employee = Employee::find($request->employee_id);
            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee does not exist',
                ], 422);  
            }                        
            Cache::forever('employee_' . $request->employee_id, $employee);
        }
 
        $device = Cache::get('device_' . $id);
        if ($device === null) {
            $device = Device::find($id);
            if (!$device) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Device does not exist',
                ], 422);
            }
        }
        $device->name = $request->name;
        $device->employee_id = $request->employee_id;
        $device->save();
        Cache::forever('device_' . $id, $device);
        $this->forgetDevicesPageCaches();

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
                'message' => 'Device does not exist',
            ], 422);
        }
        $device->delete();
        Cache::forget('device_' . $id);
        $this->forgetDevicesPageCaches();

        return response()->json([
            'status' => 'success',
            'message' => 'Device deleted successfully',
            'device' => $device,
        ]);
    }

    private function forgetDevicesPageCaches()
    {
        $devices = Device::paginate($perPage = 5, ['*'], 'page', 1)->toArray();
        $pages = $devices['last_page'];
        for ($page = 1; $page <= $pages; $page++) {
            Cache::forget('devices_page_' . $page);
          }
    }
}

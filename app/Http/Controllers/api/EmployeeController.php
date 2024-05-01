<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\api\ApiController;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

/**
* Class EmployeeController
* @package App\Http\Controllers\api
*/
class EmployeeController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $employees = Cache::get('employees');
        if ($employees === null) {
            $employees = Employee::all();
            Cache::forever('employees', $employees);
        }

        return response()->json([
            'status' => 'success',
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation fails',
                'error' => $validator->errors()->messages(),
            ], 422);       
        }

        $employee = Employee::create([
            'name' => $request->name,
        ]);
        Cache::forever('employee_' . $employee->id, $employee);

        return response()->json([
            'status' => 'success',
            'message' => 'Employee created successfully',
            'employee' => $employee,
        ]);
    }

    public function show($id)
    {
        $employee = Cache::get('employee_' . $id);
        if ($employee === null) {
            $employee = Employee::find($id);
            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee does not exist',
                ], 422);  
            }                        
            Cache::forever('employee_' . $id, $employee);
        }

        return response()->json([
            'status' => 'success',
            'employee' => $employee,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation fails',
                'error' => $validator->errors()->messages(),
            ], 422);       
        }

        $employee = Cache::get('employee_' . $id);
        if ($employee === null) {
            $employee = Employee::find($id);
            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee does not exist',
                ], 422);  
            }                        
        }
        $employee->name = $request->name;
        $employee->save();
        Cache::forever('employee_' . $id, $employee);

        return response()->json([
            'status' => 'success',
            'message' => 'Employee updated successfully',
            'employee' => $employee,
        ]);
    }

    public function destroy($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee does not exist',
            ], 422);  
        }

        // check if employee still has devices
        if ($employee->devices()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee still has devices',
            ], 422);  
        }

        $employee->delete();
        Cache::forget('employee_' . $id);

        return response()->json([
            'status' => 'success',
            'message' => 'Employee deleted successfully',
            'employee' => $employee,
        ]);
    }
}

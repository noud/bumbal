<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\api\ApiController;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
            Cache::put('employees', $employees, 60);
        }

        return response()->json([
            'status' => 'success',
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $employee = Employee::create([
            'name' => $request->name,
        ]);
        Cache::put('employee_' . $employee->id, $employee, 60);

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
                    'status' => 'failure',
                    'message' => 'Employee does not exist.',
                ]);  
            }                        
            Cache::put('employee_' . $id, $employee, 60);
        }

        return response()->json([
            'status' => 'success',
            'employee' => $employee,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $employee = Cache::get('employee_' . $id);
        if ($employee === null) {
            $employee = Employee::find($id);
            if (!$employee) {
                return response()->json([
                    'status' => 'failure',
                    'message' => 'Employee does not exist.',
                ]);  
            }                        
        }
        $employee->name = $request->name;
        $employee->save();
        Cache::put('employee_' . $id, $employee, 60);

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
                'status' => 'failure',
                'message' => 'Employee does not exist.',
            ]);  
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

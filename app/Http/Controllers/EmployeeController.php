<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $uid = $request->get('uid', '');
        $search = $request->get('search', '');
        $perPage = $request->get('perPage', 10);

        if (!$uid) {
            $query = Employee::query();

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('uid', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            }

            $data = $query->orderByDesc('created_at')->paginate($perPage);

            return response()->json([
                'status' => 'success',
            ] + $data->toArray(), 200);
        }

        $data = Employee::where('uid', $uid)->first();

        if (!$data) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Employee not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'uid' => 'required|string|max:255|unique:employees,uid',
        ], [
            'name.required' => 'Name is required',
            'uid.required' => 'UID is required',
            'uid.unique' => 'UID must be unique',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'success' => false,
                'message' => $errors->all(),
                'errors' => [
                    'name' => $errors->first('name'),
                    'uid' => $errors->first('uid'),
                ]
            ], 422);
        }

        try {

            Employee::create($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Employee created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while creating the employee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'uid' => 'required|string|max:255|unique:employees,uid,' . $employee->id,
            ], [
                'name.required' => 'Name is required',
                'uid.required' => 'UID is required',
                'uid.unique' => 'UID must be unique',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json([
                    'success' => false,
                    'message' => $errors->all(),
                    'errors' => [
                        'name' => $errors->first('name'),
                        'uid' => $errors->first('uid'),
                    ]
                ], 422);
            }

            $employee->update([
                'name' => $request->name,
                'uid' => $request->uid
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Employee updated successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An Error Occured While Updating The Employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            $employee->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Employee Deleted Successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred while deleting the employee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

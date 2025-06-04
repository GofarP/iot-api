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
        $uid = $request->get('uid');

        if (!$uid) {
            $data = Employee::orderByDesc('created_at')->paginate(10);
            return response()->json([
                'status' => 'success',
                // 'data' => $data,  // jangan pakai ini kalau mau langsung data
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        //
    }
}

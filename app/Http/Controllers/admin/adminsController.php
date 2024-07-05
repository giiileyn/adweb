<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;


class adminsController extends Controller
{
    // public function index()
    // {
    //     $data = Admin::orderBy('id', 'DESC')->get();

    //     return response()->json($data);
    // }

    public function show()
    {
       
    }

    public function create()
    {
        //
    }

    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, $id)
{
    $admin = Admin::findOrFail($id);

    // Validate request data
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:admin,username,' . $admin->id,
        'email' => 'required|string|email|max:255|unique:admin,email,' . $admin->id,
        'old_password' => 'nullable|string|min:8',
        'new_password' => 'nullable|string|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Check if old password is provided
    if ($request->filled('old_password')) {
        // Verify old password
        if (!Hash::check($request->old_password, $admin->password)) {
            return response()->json(['error' => 'Old password is incorrect'], 400);
        }

        // Update password
        $admin->password = Hash::make($request->new_password);
    }

    // Update other fields
    $admin->name = $request->name;
    $admin->username = $request->username;
    $admin->email = $request->email;
    $admin->save();

    return response()->json(['admin' => $admin, 'message' => 'Admin updated successfully']);
    }
    

}


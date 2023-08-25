<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::select('id','name', 'email', 'password')->get();
        return response()->json($users);
    }

    public function insert(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email',
            'password' => 'required|string|min:5',
        ]);

        $userCount = User::count();
        $defaultRoleId = ($userCount === 0) ? 1 : 2; 

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $defaultRoleId, 
        ]);

        return response()->json(['message' => 'User registered successfully'], 200);
    }

    public function update(Request $request, $edit_info_id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email',
        'password' => 'required|string|min:5',
    ]);

    $user = User::find($edit_info_id);

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    $user->update([
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'password' => bcrypt($request->input('password')),
    ]);

    return response()->json(['message' => 'User updated successfully'], 200);
}

public function destroy(User $user)
{
    $user->delete();

    return response()->json(['message' => 'User deleted successfully'], 200);
}

}

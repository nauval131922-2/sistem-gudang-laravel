<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'role_id' => 'required|'. Rule::in(['1', '2', '3', '4'])
        ]);

        $request['password'] = bcrypt($request->password);
        $user = User::create($request->all());
    }
}

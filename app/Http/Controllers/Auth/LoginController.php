<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('user_name', 'password');
        $token = Auth::attempt($credentials);

        if ($token) {
            $user = Auth::user();

            return response()->json([
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ],
                'user' => $user,
            ]);
        } else {

            return response()->json([
                'error' => 'Invalid User Name and Password',
            ], 401);
        }
    }
}

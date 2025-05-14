<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function test(Request $request)
    {
        Log::info('test');
        return response()->json(['message' => 'Test route working!'], 200);
    }

    // Login method
    public function login(Request $request)
    {

        // Simulating login logic
        $credentials = $request->only('email', 'password');
        Log::info('credentials: ', $credentials);

        if (Auth::attempt($credentials)) { // $credentials['email'] === 'super@super.com' && $credentials['password'] === 'password'

            $user = Auth::user();
            //$permissions = $user->getAllPermissions()->pluck('id', 'name');
            //$roles = $user->getRoleNames()->pluck('id', 'name');
            //$teams = $user->getAllTeamNames();
            // Filter and simplify team data
            $teams = $user->teams->map(function ($team) {
                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'location' => $team->location,
                    'activities' => $team->type_activities->select('id', 'name'), // Only include activity names
                ];
            })->take(1);
            $roles = $user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->select('id', 'name'), // Only include activity names
                ];
            });

            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful!',
                'data' => [
                    'token' => $token, // You can generate real tokens here
                    //$user,
                    // 'teams' => $teams,

                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        "surname" => $user->surname,
                        "mobile_number" => $user->mobile_number,
                        'email' => $user->email,
                        "is_admin" => $user->is_admin,
                        "is_email_recipient" => $user->is_email_recipient,
                        "email_verified_at" => $user->email_verified_at,
                    ],
                    'teams' => $teams,
                    //'permissions' => $permissions,
                    'roles' => $roles,
                ]
            ], 200); // HTTP status code for success
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials.'
        ], 401); // HTTP status code for unauthorized

        // dd($request->all());
        // $credentials = $request->validate([
        //     'email' => 'required|email',
        //     'password' => 'required',
        // ]);

        // if (Auth::attempt($credentials)) {
        //     $user = Auth::user();
        //     $token = $user->createToken('API Token')->plainTextToken;

        //     return response()->json([
        //         'message' => 'Login successful',
        //         'token' => $token,
        //         'user' => $user,
        //     ], Response::HTTP_OK);
        // }

        // return response()->json([
        //     'message' => 'Invalid credentials',
        // ], Response::HTTP_UNAUTHORIZED);
    }

    // Logout method
    public function logout(Request $request)
    {
        // Revoke all tokens for the authenticated user
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ], Response::HTTP_OK);
    }
}

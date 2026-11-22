<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        try {
            
            $validated = $request->validated();

            $user = new User($validated);
            $user->password = Hash::make($validated['password']);
            $user->save();

            return (new UserResource($user))->response()->setStatusCode(201);

        } catch (\Throwable $th) {
            Log::error('Error Register' . $th->getMessage());

            return response()->json([
                'error' => $th->getMessage()
            ]);
        }

    }

    public function login(UserLoginRequest $request): UserResource
    {
        $data = $request->validated();

        $user = User::where('username', $data['username'])->first();

        if(!$user || !Hash::check($data['password'], $user->password)){
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'username or password wrong'
                    ]
                ]
            ], 401));
        }

        $user->token = Str::uuid()->toString();
        $user->save();

        return new UserResource($user);
    }

    public function get(Request $request): UserResource
    {
        $user = Auth::user();

        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request): UserResource
    {
        $user = Auth::user();

        $user->update($request->validated());
        
        return new UserResource($user);
    }

    public function logout(Request $request): JsonResponse
    {

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'errors' => 'Unauthenticated'
            ], 401);
        }

        $user->token = null;
        $user->save();
        
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}

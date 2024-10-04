<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fcm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\File;

class AuthController extends Controller
{

    public function indexUser()
    {
        $admin = Auth::user();

        if ($admin->role != 'admin') {
            return response([
                'status' => 401,
                'message' => "Only Admin Can Access"
            ], 401);
        } else {
            $user = User::latest()->get();
            return response()->json([
                'message' => 'Data User',
                'data' => $user,
            ], 200);
        }

    }

    public function changeStatusUser(Request $request, $nipOrUsername)
    {
        $admin = Auth::user();

        if ($admin->role != 'admin') {
            return response([
                'status' => 401,
                'message' => "Only Admin Can Access"
            ], 401);
        } else {
            $user = User::where('username_or_nip', $nipOrUsername)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'User Not Found',
                    'data' => $user,
                ], 404);
            } else {
                $user->status = $request->new_status;
                $user->save();
                return response()->json([
                    'message' => 'Data User',
                    'data' => $user,
                ], 200);
            }
        }
    }

    public function login(Request $request)
    {
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'username_or_nip' => 'required',
            'password' => 'required',
            // 'fcm_token' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()->all()], 400);
        }

        if (!$request->username_or_nip) {
            return response()->json([
                'success' => false,
                'message' => 'NIP/Username should not be empty',
            ], 404);
        } else if ($request->password == null) {
            if ($validate->fails()) {
                return response([
                    'status' => 401,
                    'message' => "Password is Empty"
                ], 401);
            }
        }

        $password = bcrypt($request->password);
        $user = User::where('username_or_nip', $request->username_or_nip)->first();

        if (!Auth::attempt($loginData)) {
            return response([
                'status' => 401,
                'message' => 'Wrong NIP or Username',
            ], 401);
        } else {
            if ($user->status != "Diblokir") {
                return response([
                    'role' => $user->role,
                    // 'token' => $token,
                    'user' => $user,
                    'message' => 'Login Successfully',
                ]);
                
            } else {
                
                // $auth = Auth::user();
                // $token = $user->createToken('auth_token')->plainTextToken;
                // $user = User::find($user->id);
                // $user->fcm_token = $request->fcm_token;
                // $user->save();
                return response([
                    'user' => $user,
                    'message' => 'Login Failed',
                ], 401);
                
            }
        }
    }

    /**
     * store
     *
     * @param Request $request
     * @return void
     */
    public function register(Request $request)
    {
        $registrationData = $request->all();
        //Validasi Formulir
        $validator = Validator::make($registrationData, [
            'nama' => 'required',
            'username_or_nip' => 'unique:users',
            // 'username' => 'unique:users',
            'role' => 'required',
            // 'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4',
            'status' => 'required',
        ], [
            'username_or_nip.unique' => 'NIP/Username sudah terdaftar',
            // 'username.unique' => 'Username sudah terdaftar'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->all()], 400);
        }
        $registrationData['password'] = bcrypt($request->password);
        if ($request->username_or_nip == null) {
            return response()->json([
                'success' => false,
                'message' => 'Not eligible to register',
            ], 404);
        } else {
            $userData = [
                'nama' => $request->nama,
                'username_or_nip' => $request->username_or_nip,
                // 'username' => $request->username,
                'role' => $request->role,
                'password' => $registrationData['password'],
                'status' => $request->status,
            ];

            $user = User::create($userData);

            return response()->json([
                'success' => true,
                'message' => 'Register success',
            ], 200);
        }

        // return response([
        //     'message' => 'Data added successfully',
        //     'paud' => $request->role,
        // ], 201);
    }

    /**
     * Logout a user (revoke the token).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $user = Auth::user();
        $userLogout = User::find($user->id);

        // $userLogout->fcm_token = null;
        $userLogout->save();

        // ~ Udah jalan emang kebaca error aja
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out',
        ], 200);
    }

    public function getUser()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'Customer':
                // $data = $user->customer->with('customer');
                // $data->customer_id = $data->id;
                $message = 'Profile Customer';
                $data = User::with('vet', 'petShop', 'customer')->find($user->id);
                break;

            case 'Vet':
                // $data = $user->vet;
                // $data->vet_id = $data->id;
                $message = ' Profile Vet';
                $data = User::with('vet', 'petShop', 'customer')->find($user->id);
                break;

            case 'Pet Shop':
                $message = ' Profile Pet Shop';
                $data = User::with('vet', 'petShop', 'customer')->find($user->id);
                break;

            default:
                $data = null;
                $message = 'Unknown role';
                break;
        }

        return response()->json([
            'data' => $data,
            'email' => $user->email,
            'message' => $message,
            'role' => $user->role,
        ], 200);
    }

    public function getUserToken($id)
    {
        $userToken = Fcm::where('id_user', $id)->latest()->get();

        if (!$userToken) {
            return response()->json([
                'data' => $userToken,
                'message' => "User not found",
            ], 400);
        }
        return response()->json([
            'data' => $userToken,
        ], 200);
    }
}

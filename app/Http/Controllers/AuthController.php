<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email|email',
            'password' => 'required|between:8,16|confirmed'    //|confirmed
        ], [
            'name.required' => 'Vui lòng nhập tên',
            'email.required' => 'Vui lòng nhập email',
            'email.unique' => 'Email đã được sử dụng',
            'email.email' => 'Email không đúng định dạng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.between' => 'Mật khẩu có độ dài từ 8 -> 16 ký tự.',
            'password.confirmed' => 'Mật khẩu không trùng nhau'
        ]);
        $user =  User::create([
            "name" => $request['name'],
            "email" => $request['email'],
            "password" => Hash::make($request['password']),
        ]);
        return response()->json([
            "User" => $user,
            "token" => $user->createToken('API TOKEN')->plainTextToken
        ]);
    }
    public function login(Request $request)
    {
        $validate = $request->validate([
            'email' => 'required|email',
            'password' => 'required|between:8,16'
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.between' => 'Mật khẩu có độ dài từ 8 -> 16 ký tự.',
        ]);
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $token = $request->user()->createToken('API TOKEN')->plainTextToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json([
                'errors' => ['password' => ['Sai mật khẩu hoặc tài khoản'], 'email' => ['Sai mật khẩu hoặc tài khoản']],
                'message' => 'Sai mật khẩu hoặc tài khoản',
            ], 400);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
    public function index(Request $request, $userId)
    {
        try {
            $categoriId = $request->input('categoriId');
            $query =  Event::join('users', 'users.id', '=', 'events.user_id')
                ->join('events_categoris', 'events_categoris.id', '=', 'events.categoris_id')
                ->select('events.start_time', 'events.id', 'events.color',  'events.end_time', 'events.name_event', 'events.notes', 'events_categoris.categoris')->where('events.user_id', $userId);
            if (isset($categoriId)) {
                $query->where('events.categoris_id', $categoriId);
            }
            $event = $query->get();
            return response()->json(['event' => $event]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve events',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function checkToken(Request $request)
    {
        $token = $request->input('token');
        if (Auth::guard('web')->check() || Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Token hợp lệ']);
        }
        return response()->json(['message' => 'Token không hợp lệ'], 401);
    }
}

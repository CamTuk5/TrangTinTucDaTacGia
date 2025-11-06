<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Đăng nhập
     * - Validate email/password
     * - Check mật khẩu
     * - Cấp Sanctum token kèm abilities theo role:
     *      admin => ["*"] (toàn quyền)
     *      user  => ["read"] (ví dụ)
     * - Trả JSON: token, token_type, abilities, user
     */
    public function login(Request $request)
    {
        // Bảo đảm trả JSON nếu lỗi validate
        $request->headers->set('Accept', 'application/json');

        $data = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required'],
            // Tuỳ chọn: tên thiết bị để phân biệt token
            'device_name' => ['sometimes','string','max:100'],
        ]);

        // Tìm user theo email (không phân biệt hoa thường)
        $user = User::whereRaw('LOWER(email) = ?', [mb_strtolower($data['email'])])->first();

        // Không tiết lộ tài khoản có tồn tại hay không → 422 thống nhất
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        // Gán abilities theo vai trò
        $abilities = ($user->role ?? 'user') === 'admin' ? ['*'] : ['read'];

        // Tên token (hiển thị trong personal_access_tokens)
        $tokenName = $data['device_name'] ?? 'api';
        $plainText = $user->createToken($tokenName, $abilities)->plainTextToken;

        return response()->json([
            'token'       => $plainText,
            'token_type'  => 'Bearer',
            'abilities'   => $abilities,
            'user'        => $user,
        ], 200);
    }

    /**
     * Lấy thông tin user đang đăng nhập
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Đăng xuất 1 thiết bị (xoá current access token)
     */
    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();
        return response()->json(['message' => 'Logged out']);
    }

    /**
     * (Tuỳ chọn) Đăng xuất toàn bộ thiết bị
     * - Thêm route POST /api/auth/logout-all nếu muốn dùng.
     */
    public function logoutAll(Request $request)
    {
        $request->user()?->tokens()->delete();
        return response()->json(['message' => 'Logged out from all devices']);
    }
}

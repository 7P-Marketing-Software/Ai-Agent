<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Auth\Services\AuthService;
use Modules\Auth\Services\WhatsAppService;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\ForgetUserRequest;
use Modules\Auth\Http\Requests\ResetUserRequest;
use Modules\Auth\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService, private WhatsAppService $whatsAppService) {}

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request);
        return $this->respondCreated($user, 'User registered successfully');
    }

    public function login(LoginRequest $request)
    {
        $user = $this->authService->login($request);
        if ($user) {
            return $this->respondOk($user, 'User logged in successfully');
        } else {
            return $this->respondError(null, 'Invalid credentials');
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        Log::info($user);
        if (!$user) {
            return $this->respondNotFound(null, 'User not found');
        }
        $this->authService->logout($user);
        return $this->respondOk(null, 'User logged out successfully');
    }

    public function resetPassword(ResetUserRequest $request)
    {
        $fields = $request->validated();

        $user = auth('sanctum')->user();

        $this->authService->resetPassword($user, $fields['password']);

        return $this->respondOk(null, 'Password reset successfully');
    }

    public function forgetPassword(ForgetUserRequest $request)
    {

        $user = User::where('phone', $request->validated()['phone'])->first();

        if ($this->sendWhatsAppOtp($user)) {

            return $this->respondOk(null, 'Please check your phone');
        } else {

            return $this->respondError(null, 'Something went wrong please try again later');
        }
    }

    public function checkPhoneOTPForgetPassword(Request $request)
    {

        $validated = $request->validate([
            'phone' => ['required', 'string', 'exists:users,phone'],
            'phoneOtp' => 'required|digits:5',
        ]);

        $user = User::where('phone', $validated['phone'])->first();

        $maxAttempts = 5;
        $lockDuration = 5;

        if ($user->otp_sent_at < now()->subMinutes($lockDuration)) {
            $user->update(['otp_attempts' => 0]);
        }

        if ($user->otp_attempts >= $maxAttempts) {
            return $this->respondError(null, 'Maximum OTP attempts exceeded. Please try again after 5 minutes.');
        }

        if ($user->otp_expires_at < now()) {
            return $this->respondError(null, 'OTP has expired. Please request a new one.');
        }

        if ($validated['phoneOtp'] != $user->otp) {
            $user->increment('otp_attempts');
            $user->update(['otp_sent_at' => now()]);
            return $this->respondError(null, 'Invalid OTP');
        }

        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
            'otp_verified_at' => now(),
        ]);
        $user->save();

        $data = [
            'token' => $this->authService->createForgetPasswordToken($user),
        ];

        return $this->respondOk($data, 'OTP verified successfully');
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|numeric|digits:11',
            'otp' => 'required|numeric|digits:5',
        ]);

        $user = User::where('phone', $validated['phone'])->first();

        if (!$user) {
            return $this->respondNotFound(null, 'Phone not found');
        }

        $maxAttempts = 5;
        $lockDuration = 5;

        if ($user->otp_sent_at < now()->subMinutes($lockDuration)) {
            $user->update(['otp_attempts' => 0]);
        }

        if ($user->otp_attempts >= $maxAttempts) {
            return $this->respondError(null, 'Maximum OTP attempts exceeded. Please try again after 5 minutes.');
        }

        if ($user->otp_expires_at < now()) {
            return $this->respondError(null, 'OTP has expired. Please request a new one.');
        }

        if ($validated['otp'] != $user->otp) {
            $user->increment('otp_attempts');
            $user->update(['otp_sent_at' => now()]);
            return $this->respondError(null, 'Invalid OTP');
        }

        $user->update(['otp_verified_at' => Carbon::now(), 'otp' => null]);

        return $this->respondOk($user, 'phone verified successfully.');
    }

    public function  resendOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|numeric',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return $this->respondError(null, 'Phone not found');
        }

        if ($this->sendWhatsAppOtp($user)) {
            return $this->respondOk(null, 'Please check your phone.');
        }

        return $this->respondError(null, 'Something went wrong please try again later');
    }

    public function sendWhatsAppOtp($user)
    {
        $otp = rand(10000, 99999);
        $country_code = 2;
        $this->whatsAppService->sendText($country_code . $user->phone, $otp);
        $user->update([
            'otp' => $otp,
            'otp_sent_at' => now(),
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts' => $user->otp_attempts + 1,
        ]);
        $user->save();
        return true;
    }
}

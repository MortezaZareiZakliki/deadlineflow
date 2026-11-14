<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;
use \App\Jobs\SendPasswordChangedEmailJob;

class AuthController extends Controller
{
    use ApiResponse;

    #[OA\Post(
        path: "/api/login",
        summary: "ورود کاربر",
        description: "دریافت توکن Sanctum با ایمیل و پسورد",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "demo@deadlineflow.ir"),
                    new OA\Property(property: "password", type: "string", example: "password"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "ورود موفق",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Login successful"),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "token", type: "string", example: "1|abcdefg..."),
                            new OA\Property(property: "user", type: "object"),
                        ]),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "ایمیل یا پسورد اشتباه"),
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $key = 'login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return $this->errorResponse("تعداد تلاش ها بیش از حد مجاز. لطفاً {$seconds} ثانیه دیگر تلاش کنید.", 429);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60);

            Log::warning("Failed login attempt", [
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            throw ValidationException::withMessages([
                'email' => ['اطلاعات ورود صحیح نیست.'],
            ]);
        }

        RateLimiter::clear($key);

        $newToken = $user->createToken('auth_token');
        $newToken->accessToken->forceFill([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ])->save();

        Log::info("User logged in successfully.", [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->successResponse([
            'token' => $newToken->plainTextToken,
            'user' => $user,
        ], 'ورود موفق');
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "خروج کاربر",
        tags: ["Auth"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "خروج موفق"),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse(null, 'خروج موفق');
    }

    #[OA\Put(
        path: "/api/profile/info",
        summary: "ویرایش اطلاعات شخصی (نام)",
        tags: ["Profile"],
        security: [["bearerAuth" => []]],
        responses: [new OA\Response(response: 200, description: "اطلاعات آپدیت شد")]
    )]
    public function updateInfo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $request->user()->update(['name' => $validated['name']]);
        $user = $request->user()->fresh();

        return $this->successResponse($user, 'اطلاعات شخصی با موفقیت آپدیت شد.');
    }

    #[OA\Put(
        path: "/api/profile/password",
        summary: "تغییر رمز عبور",
        tags: ["Profile"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "رمز عبور تغییر کرد"),
            new OA\Response(response: 422, description: "رمز عبور فعلی اشتباه است")
        ]
    )]
    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return $this->errorResponse('رمز عبور فعلی اشتباه است.', 422);
        }

        $user->update(['password' => Hash::make($validated['new_password'])]);
        SendPasswordChangedEmailJob::dispatch($user);

        return $this->successResponse(null, 'رمز عبور با موفقیت تغییر کرد.');
    }

    #[OA\Get(
        path: "/api/sessions",
        summary: "لیست نشست های فعال",
        tags: ["Sessions"],
        security: [["bearerAuth" => []]],
        responses: [new OA\Response(response: 200, description: "لیست نشست ها")]
    )]
    public function getSessions(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->forceFill(['last_used_at' => now()])->save();

        $agent = new \Jenssegers\Agent\Agent();

        $sessions = $request->user()->tokens->map(function ($token) use ($agent) {
            $agent->setUserAgent($token->user_agent);
            $browser = $agent->browser() ?: 'نامشخص';
            $platform = $agent->platform() ?: 'نامشخص';
            $device = 'کامپیوتر';

            if ($agent->isMobile()) $device = 'موبایل';
            elseif ($agent->isTablet()) $device = 'تبلت';
            elseif ($agent->isDesktop()) $device = 'کامپیوتر';

            return [
                'id' => $token->id,
                'ip_address' => $token->ip_address,
                'user_agent' => $token->user_agent,
                'browser' => $browser,
                'platform' => $platform,
                'device_type' => $device,
                'last_used_at' => $token->last_used_at
                    ? \Carbon\Carbon::parse($token->last_used_at)->diffForHumans()
                    : 'نامشخص',
                'created_at' => $token->created_at->format('Y-m-d H:i'),
                'is_current' => $token->id === request()->user()->currentAccessToken()->id,
            ];
        });

        return $this->successResponse($sessions, 'لیست نشست ها دریافت شد.');
    }

    #[OA\Delete(
        path: "/api/sessions/{id}",
        summary: "حذف یک نشست",
        tags: ["Sessions"],
        security: [["bearerAuth" => []]],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        responses: [new OA\Response(response: 200, description: "نشست حذف شد")]
    )]
    public function deleteSession(Request $request, $id): JsonResponse
    {
        $deleted = $request->user()->tokens()->where('id', $id)->delete();

        if ($deleted) {
            return $this->successResponse(null, 'نشست با موفقیت حذف شد.');
        }

        return $this->errorResponse('نشست یافت نشد.', 404);
    }

}

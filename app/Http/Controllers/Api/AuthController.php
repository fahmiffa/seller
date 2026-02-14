<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Rules\NumberWa;
use DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Register a new user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => ['nullable', 'string', 'max:20', new NumberWa],
            'password' => 'required|string|min:6|confirmed',
            'trial' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'saldo' => 50000,
            'limit' => 10000,
            'phone_number' => $request->phone_number,
            'password' => bcrypt($request->password),
            'trial' => $request->trial ?? 0,
        ]);

        $token = auth('api')->login($user);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil didaftarkan',
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ], 201);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json([
            'success' => true,
            'user' => auth('api')->user(),
            'limit' => auth('api')->user()->limit
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Update the authenticated User profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => 'nullable|string',
            'img' => 'nullable|image|max:2048',
            'old_password' => 'nullable|string|min:8',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->filled('password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password lama tidak sesuai',
                ], 422);
            }
            $user->password = Hash::make($request->password);
            $user->save();
        }

        $data = $request->only('name', 'phone_number', 'address');

        if ($request->hasFile('img')) {
            // Delete old image
            if ($user->img) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->img);
            }

            $file = $request->file('img');
            $filename = $file->hashName();
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($file);
            $image->scale(width: 500);
            $encoded = $image->toJpeg(quality: 70);
            \Illuminate\Support\Facades\Storage::disk('public')->put('users/' . $filename, $encoded);
            $data['img'] = 'users/' . $filename;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'user' => $user
        ]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user()
        ]);
    }

    public function forget(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'hp' => ['required', new NumberWa()],
            ],
            [
                'hp.required' => 'Nomor wajib diisi.',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }
        DB::beginTransaction();
        try {
            $user = User::where('phone_number', $request->hp)->first();
            if (! $user) {
                return response()->json([
                    'errors' => ['hp' => 'Nomor tidak valid'],
                ], 400);
            }

            $pass           = random_int(10000, 99999);
            $user->password = bcrypt($pass);
            $user->save();

            $to       = '62' . substr($user->phone_number, 1);
            $response = Http::post(env('URL_WA') . '/send', [
                'number'  => env('NUMBER_WA'),
                'to'      => $to,
                'message' => "Anda reset Berhasil Password\nPassword akun anda : *" . $pass . "*",
            ]);

            if ($response->status() != 200) {
                Log::error($response->json());
                return response()->json([
                    'errors' => ['hp' => 'Server Sibuk'],
                ], 400);
            } else {
                DB::commit();
                return response()->json([
                    'status' => true,
                ]);
            }
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['error' => $e], 500);
        }
    }
}

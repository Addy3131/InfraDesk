<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AccessToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class AuthenticateToken
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = str_replace('Bearer ', '', $header);
        $decoded = base64_decode($token);

        if (!$decoded) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        [$tokenId, $userId] = explode('|', $decoded);

        $accessToken = AccessToken::where('id', $tokenId)
            ->whereNull('revoked_at')
            ->first();

        if (!$accessToken || Carbon::parse($accessToken->expires_at)->isPast()) {
            return response()->json(['message' => 'Token expired'], 401);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // 🔥 THIS IS THE IMPORTANT PART
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        Auth::setUser($user);

        return $next($request);
    }
}

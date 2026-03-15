<?php

namespace App\Auth;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\AccessToken;
use App\Models\RefreshToken;
use Carbon\Carbon;

class TokenService
{
    public function generateAccessToken($user)
    {
        // Revoke existing tokens (single session logic)
        AccessToken::where('user_id', $user->id)->update([
            'revoked_at' => now()
        ]);

        $tokenId = (string) Str::uuid();

        AccessToken::create([
            'id' => $tokenId,
            'user_id' => $user->id,
            'expires_at' => now()->addMinutes(15)
        ]);

        return base64_encode($tokenId . '|' . $user->id);
    }

    public function generateRefreshToken($user)
    {
        // Revoke old refresh tokens
        RefreshToken::where('user_id', $user->id)->update([
            'revoked_at' => now()
        ]);

        $plainToken = Str::random(64);

        RefreshToken::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'token_hash' => Hash::make($plainToken),
            'expires_at' => now()->addDays(7)
        ]);

        return $plainToken;
    }

    public function refreshAccessToken($refreshTokenPlain)
    {
        $refreshTokens = RefreshToken::whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->get();

        $matched = null;

        foreach ($refreshTokens as $token) {
            if (Hash::check($refreshTokenPlain, $token->token_hash)) {
                $matched = $token;
                break;
            }
        }

        if (!$matched) {
            return null;
        }

        $user = $matched->user;

        // Single session → revoke everything
        AccessToken::where('user_id', $user->id)->update([
            'revoked_at' => now()
        ]);

        RefreshToken::where('user_id', $user->id)->update([
            'revoked_at' => now()
        ]);

        $newAccess = $this->generateAccessToken($user);
        $newRefresh = $this->generateRefreshToken($user);

        return [
            'access_token' => $newAccess,
            'refresh_token' => $newRefresh,
        ];
    }

}

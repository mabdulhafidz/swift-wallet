<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

final readonly class Login
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $user = User::where('email', $args['email'])->first();

        if (! $user || ! Hash::check($args['password'], $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }
}

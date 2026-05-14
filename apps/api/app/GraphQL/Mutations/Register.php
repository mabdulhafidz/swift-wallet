<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\User;

final readonly class Register
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $user = User::create([
            'name' => $args['name'],
            'email' => $args['email'],
            'password' => bcrypt($args['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }
}

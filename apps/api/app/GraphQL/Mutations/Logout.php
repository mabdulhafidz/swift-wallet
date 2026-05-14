<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

final readonly class Logout
{
    /** @param  array{}  $args */
    public function __invoke(null $root, array $args, array $context): array
    {
        /** @var \App\Models\User $user r*/
        $user = $context['user'];
        $user->currentAccessToken()->delete();

        return ['message' => 'Logged out successfully'];
    }
}

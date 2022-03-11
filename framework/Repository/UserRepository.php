<?php
declare(strict_types=1);

namespace Delos\Repository;

use Delos\Model\User;
use Illuminate\Database\Eloquent\Collection;

final class UserRepository implements RepositoryInterface
{
    /**
     * @return User[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): array|Collection
    {
        return User::all();
    }

    public function createUser(User $user): void
    {
        User::create(['username' => $user->username, 'email' => $user->email, 'password' => $user->password]);
    }
}
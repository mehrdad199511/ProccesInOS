<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Log;
use App\Models\User;

class UserRepository
{
    /**
     * Create new user
     *
     * @param array $user
     * @return object|bool
     */
    public function create(array $user)
    {
        try {

            return User::create($user);

        } catch (\PDOException $e) {
            Log::channel('api_database_exceptions')
                ->emergency(
                    'Occurrence point => User Repository (create method) ***** ' .
                    $e->getMessage()
                );

            return false;
        }
    }
}

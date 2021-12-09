<?php

namespace App\Repositories;

use App\Models\Log;

class LogRepository
{
    /**
     * Create new auth log
     *
     * @param array $log
     * @return void
     */
    public function create(array $log)
    {
        Log::create($log);
    }
}

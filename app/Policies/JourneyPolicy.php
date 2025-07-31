<?php

namespace App\Policies;

use App\Models\Journey;
use App\Models\Account;
use Illuminate\Auth\Access\Response;

class JourneyPolicy
{
    public function view(Account $account, Journey $journey): Response
    {
        
        return $account->id === $journey->tourist_id
        ? Response::allow()
        : Response::deny('You are not allowed to view this journey.');
    }

    public function update(Account $account, Journey $journey): Response
    {
        return $account->id === $journey->tourist_id 
        ? Response::allow()
        : Response::deny('You are not allowed to update this journey.');
    }

    public function delete(Account $account, Journey $journey): Response
    {
        return $account->id === $journey->tourist_id
        ? Response::allow()
        : Response::deny('You are not allowed to update this journey.');
    }
}

<?php

namespace App\Policies;

use App\Models\Food;
use App\Models\Account;

use Illuminate\Auth\Access\Response;

class FoodPolicy
{
   
    public function view(Account $account, Food $food): bool
    {
        return $food->restaurant->account_id === $account->id;
    }

    public function update(Account $account, Food $food): bool
    {
        return $food->restaurant->account_id === $account->id;
    }

    public function delete(Account $account, Food $food): bool
    {
        return $food->restaurant->account_id === $account->id;
    }
}

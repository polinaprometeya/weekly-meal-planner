<?php

namespace App\Policies;

use App\Models\MealPlan;
use App\Models\User;

class MealPlanPolicy
{
    public function view(User $user, MealPlan $mealPlan): bool
    {
        return $mealPlan->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MealPlan $mealPlan): bool
    {
        return $mealPlan->user_id === $user->id;
    }

    public function delete(User $user, MealPlan $mealPlan): bool
    {
        return $mealPlan->user_id === $user->id;
    }
}

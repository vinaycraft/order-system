<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Anyone can view categories
    }

    public function view(User $user, Category $category)
    {
        return true; // Anyone can view a category
    }

    public function create(User $user)
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Category $category)
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Category $category)
    {
        return $user->role === 'admin';
    }
}

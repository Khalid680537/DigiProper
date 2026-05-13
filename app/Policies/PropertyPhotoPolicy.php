<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\User;

class PropertyPhotoPolicy
{
    public function viewAny(User $user, Property $property): bool
    {
        return $property->created_by === $user->id;
    }

    public function view(User $user, PropertyPhoto $photo): bool
    {
        return $photo->property->created_by === $user->id;
    }

    public function create(User $user, Property $property): bool
    {
        return $property->created_by === $user->id;
    }

    public function update(User $user, PropertyPhoto $photo): bool
    {
        return $photo->property->created_by === $user->id;
    }

    public function delete(User $user, PropertyPhoto $photo): bool
    {
        return $photo->property->created_by === $user->id;
    }
}

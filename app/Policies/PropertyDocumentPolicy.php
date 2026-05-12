<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\PropertyDocument;
use App\Models\User;

class PropertyDocumentPolicy
{
    public function viewAny(User $user, Property $property): bool
    {
        return $property->created_by === $user->id;
    }

    public function view(User $user, PropertyDocument $document): bool
    {
        return $document->property->created_by === $user->id;
    }

    public function create(User $user, Property $property): bool
    {
        return $property->created_by === $user->id;
    }

    public function update(User $user, PropertyDocument $document): bool
    {
        return $document->property->created_by === $user->id;
    }

    public function delete(User $user, PropertyDocument $document): bool
    {
        return $document->property->created_by === $user->id;
    }
}

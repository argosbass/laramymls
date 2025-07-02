<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\PropertyLocations;
use App\Models\User;

class PropertyLocationsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any PropertyLocations');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PropertyLocations $propertylocations): bool
    {
        return $user->checkPermissionTo('view PropertyLocations');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create PropertyLocations');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PropertyLocations $propertylocations): bool
    {
        return $user->checkPermissionTo('update PropertyLocations');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PropertyLocations $propertylocations): bool
    {
        return $user->checkPermissionTo('delete PropertyLocations');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any PropertyLocations');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PropertyLocations $propertylocations): bool
    {
        return $user->checkPermissionTo('restore PropertyLocations');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any PropertyLocations');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, PropertyLocations $propertylocations): bool
    {
        return $user->checkPermissionTo('replicate PropertyLocations');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder PropertyLocations');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PropertyLocations $propertylocations): bool
    {
        return $user->checkPermissionTo('force-delete PropertyLocations');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any PropertyLocations');
    }
}

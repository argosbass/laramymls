<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\PropertyType;
use App\Models\User;

class PropertyTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any PropertyType');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PropertyType $propertytype): bool
    {
        return $user->checkPermissionTo('view PropertyType');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create PropertyType');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PropertyType $propertytype): bool
    {
        return $user->checkPermissionTo('update PropertyType');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PropertyType $propertytype): bool
    {
        return $user->checkPermissionTo('delete PropertyType');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any PropertyType');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PropertyType $propertytype): bool
    {
        return $user->checkPermissionTo('restore PropertyType');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any PropertyType');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, PropertyType $propertytype): bool
    {
        return $user->checkPermissionTo('replicate PropertyType');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder PropertyType');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PropertyType $propertytype): bool
    {
        return $user->checkPermissionTo('force-delete PropertyType');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any PropertyType');
    }
}

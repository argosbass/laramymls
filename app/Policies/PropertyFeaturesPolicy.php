<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\PropertyFeatures;
use App\Models\User;

class PropertyFeaturesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any PropertyFeatures');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PropertyFeatures $propertyfeatures): bool
    {
        return $user->checkPermissionTo('view PropertyFeatures');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create PropertyFeatures');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PropertyFeatures $propertyfeatures): bool
    {
        return $user->checkPermissionTo('update PropertyFeatures');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PropertyFeatures $propertyfeatures): bool
    {
        return $user->checkPermissionTo('delete PropertyFeatures');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any PropertyFeatures');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PropertyFeatures $propertyfeatures): bool
    {
        return $user->checkPermissionTo('restore PropertyFeatures');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any PropertyFeatures');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, PropertyFeatures $propertyfeatures): bool
    {
        return $user->checkPermissionTo('replicate PropertyFeatures');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder PropertyFeatures');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PropertyFeatures $propertyfeatures): bool
    {
        return $user->checkPermissionTo('force-delete PropertyFeatures');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any PropertyFeatures');
    }
}

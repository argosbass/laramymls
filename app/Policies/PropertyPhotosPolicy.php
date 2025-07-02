<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\PropertyPhotos;
use App\Models\User;

class PropertyPhotosPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any PropertyPhotos');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PropertyPhotos $propertyphotos): bool
    {
        return $user->checkPermissionTo('view PropertyPhotos');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create PropertyPhotos');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PropertyPhotos $propertyphotos): bool
    {
        return $user->checkPermissionTo('update PropertyPhotos');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PropertyPhotos $propertyphotos): bool
    {
        return $user->checkPermissionTo('delete PropertyPhotos');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any PropertyPhotos');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PropertyPhotos $propertyphotos): bool
    {
        return $user->checkPermissionTo('restore PropertyPhotos');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any PropertyPhotos');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, PropertyPhotos $propertyphotos): bool
    {
        return $user->checkPermissionTo('replicate PropertyPhotos');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder PropertyPhotos');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PropertyPhotos $propertyphotos): bool
    {
        return $user->checkPermissionTo('force-delete PropertyPhotos');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any PropertyPhotos');
    }
}

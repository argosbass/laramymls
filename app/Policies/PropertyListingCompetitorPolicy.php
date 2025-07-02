<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\PropertyListingCompetitor;
use App\Models\User;

class PropertyListingCompetitorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any PropertyListingCompetitor');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PropertyListingCompetitor $propertylistingcompetitor): bool
    {
        return $user->checkPermissionTo('view PropertyListingCompetitor');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create PropertyListingCompetitor');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PropertyListingCompetitor $propertylistingcompetitor): bool
    {
        return $user->checkPermissionTo('update PropertyListingCompetitor');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PropertyListingCompetitor $propertylistingcompetitor): bool
    {
        return $user->checkPermissionTo('delete PropertyListingCompetitor');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any PropertyListingCompetitor');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PropertyListingCompetitor $propertylistingcompetitor): bool
    {
        return $user->checkPermissionTo('restore PropertyListingCompetitor');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any PropertyListingCompetitor');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, PropertyListingCompetitor $propertylistingcompetitor): bool
    {
        return $user->checkPermissionTo('replicate PropertyListingCompetitor');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder PropertyListingCompetitor');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PropertyListingCompetitor $propertylistingcompetitor): bool
    {
        return $user->checkPermissionTo('force-delete PropertyListingCompetitor');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any PropertyListingCompetitor');
    }
}

<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\ListingCompetitor;
use App\Models\User;

class ListingCompetitorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any ListingCompetitor');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ListingCompetitor $listingcompetitor): bool
    {
        return $user->checkPermissionTo('view ListingCompetitor');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create ListingCompetitor');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ListingCompetitor $listingcompetitor): bool
    {
        return $user->checkPermissionTo('update ListingCompetitor');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ListingCompetitor $listingcompetitor): bool
    {
        return $user->checkPermissionTo('delete ListingCompetitor');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any ListingCompetitor');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ListingCompetitor $listingcompetitor): bool
    {
        return $user->checkPermissionTo('restore ListingCompetitor');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any ListingCompetitor');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, ListingCompetitor $listingcompetitor): bool
    {
        return $user->checkPermissionTo('replicate ListingCompetitor');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder ListingCompetitor');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ListingCompetitor $listingcompetitor): bool
    {
        return $user->checkPermissionTo('force-delete ListingCompetitor');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any ListingCompetitor');
    }
}

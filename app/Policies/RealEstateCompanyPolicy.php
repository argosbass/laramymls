<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\RealEstateCompany;
use App\Models\User;

class RealEstateCompanyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any RealEstateCompany');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RealEstateCompany $realestatecompany): bool
    {
        return $user->checkPermissionTo('view RealEstateCompany');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create RealEstateCompany');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RealEstateCompany $realestatecompany): bool
    {
        return $user->checkPermissionTo('update RealEstateCompany');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RealEstateCompany $realestatecompany): bool
    {
        return $user->checkPermissionTo('delete RealEstateCompany');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any RealEstateCompany');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RealEstateCompany $realestatecompany): bool
    {
        return $user->checkPermissionTo('restore RealEstateCompany');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any RealEstateCompany');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, RealEstateCompany $realestatecompany): bool
    {
        return $user->checkPermissionTo('replicate RealEstateCompany');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder RealEstateCompany');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RealEstateCompany $realestatecompany): bool
    {
        return $user->checkPermissionTo('force-delete RealEstateCompany');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any RealEstateCompany');
    }
}

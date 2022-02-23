<?php
namespace App\Models;

use Spatie\Permission\Models\Role as RoleModel;

class Role extends RoleModel {
    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date) {
        return (string) $date;
    }
}

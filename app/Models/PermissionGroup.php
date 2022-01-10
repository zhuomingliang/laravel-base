<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model {
    public function permission() {
        return $this->hasMany(Permission::class, 'pg_id');
    }
}

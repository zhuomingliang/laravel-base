<?php

namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission {
    public function group() {
        return $this->belongsTo(PermissionGroup::class, 'pg_id');
    }

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

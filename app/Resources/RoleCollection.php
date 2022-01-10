<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleCollection extends ResourceCollection {
    public function toArray($request) {
        return [
            'data' => $this->collection
        ];
    }
}

<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrUpdateRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $rules = [
            'cname' => 'required:max:32',
            'name' => 'required|max:255',
            'guard_name' => 'required|max:255',
            'pg_id' => 'required|numeric',
            'sequence' => 'numeric'
        ];

        return $rules;
    }
}

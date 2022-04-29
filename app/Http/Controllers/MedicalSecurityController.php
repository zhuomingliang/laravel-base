<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalSecurity;

/*
 * 医疗保障
 */
class MedicalSecurityController extends Controller {
    //获取
    public function getIndex(Request $request) {
        return MedicalSecurity::where($request->only(['date', 'status']))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            MedicalSecurity::insert($request->only([
                'date', 'doctor', 'doctor_phone', 'doctor_address', 'nurse', 'nurse_phone',
                'nurse_address', 'nucleic_acid_testing_address', 'isolation_address', 'status'
            ]));
        } catch (\Exception $e) {
            return $this->conflict($e->getMessage());
        }
        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            MedicalSecurity::where('id', (int)$request->get('id', 0))->update($request->only([
                 'date', 'doctor', 'doctor_phone', 'doctor_address', 'nurse', 'nurse_phone',
                'nurse_address', 'nucleic_acid_testing_address', 'isolation_address', 'status'
            ]));
        } catch (\Exception $e) {
            return $this->conflict($e->getMessage());
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (MedicalSecurity::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            }
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $model = MedicalSecurity::findOrFail((int) $request->get('id'));

        try {
            $model->update($request->only(['status']));
        } catch (\Exception $e) {
        }

        return $this->noContent();
    }
}

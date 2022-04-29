<?php

namespace App\Http\Controllers;

use App\Models\EpidemicPreventionInstructions;
use Illuminate\Http\Request;

/*
 * 防疫须知
 */
class EpidemicPreventionInstructionsController extends Controller {
    //获取
    public function getIndex(Request $request) {
        return EpidemicPreventionInstructions::latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    // public function PostIndex(Request $request) {
    //     try {
    //         EpidemicPreventionInstructions::insert($request->only([
    //             'content'
    //         ]));
    //     } catch (\Exception $e) {
    //         return $this->conflict('已存在该数据');
    //     }
    //     return $this->created();
    // }

    //修改
    public function PutIndex(Request $request) {
        try {
            EpidemicPreventionInstructions::where('id', (int)$request->get('id', 0))->update($request->only([
                'content'
            ]));
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }

        return $this->noContent();
    }
}

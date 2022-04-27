<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileInformation;

/*
 * 文件信息
 * */

class FileInformationController extends Controller {
    //获取
    public function getIndex(Request $request) {
        return FileInformation::where($request->only(['file_name', 'status']))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([
                'file_name', 'file_path', 'status'
            ]);

            if (isset($data['file_path'][0])) {
                $data['file_path'] = $data['file_path'][0];
            } else {
                $data['file_path'] = '';
            }

            FileInformation::insert($data);
        } catch (\Exception $e) {
            return $this->conflict($e->getMessage());
        }
        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            $data = $request->only([
                'file_name', 'file_path', 'status'
            ]);

            if (isset($data['file_path'][0])) {
                $data['file_path'] = $data['file_path'][0];
            } else {
                $data['video'] = '';
            }

            FileInformation::where('id', (int)$request->get('id', 0))->update($data);
        } catch (\Exception $e) {
            return $this->conflict($e->getMessage());
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (FileInformation::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            }
        } catch (\Exception $e) {
            return $this->conflict($e->getMessage());
        }

        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $FileInformation = FileInformation::findOrFail((int) $request->get('id'));

        try {
            $FileInformation->update($request->only(['status']));
        } catch (\Exception $e) {
            return $this->conflict($e->getMessage());
        }

        return $this->noContent();
    }
}

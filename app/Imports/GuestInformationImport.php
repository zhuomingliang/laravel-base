<?php

namespace App\Imports;

use App\Models\GuestInformation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class GuestInformationImport implements ToModel, WithBatchInserts
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (empty($row[0]) || $row[0] == '姓名') {
            return null;
        }
        return new GuestInformation([
            'full_name'     => $row[0],
            'phone'    => $row[1],
            'home_decoration_expo_id' => 1,
            'from'  => '后台导入',
            'status'=>true,
        ]);
    }

    //批量导入1000条
    public function batchSize(): int
    {
        return 1000;
    }
    //以1000条数据基准切割数据
    public function chunkSize(): int
    {
        return 1000;
    }

}

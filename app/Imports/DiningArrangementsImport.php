<?php

namespace App\Imports;

use App\Models\DiningArrangements;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class DiningArrangementsImport implements ToModel, WithBatchInserts
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (empty($row[0]) || $row[0] == '日期') {
            return null;
        }
        return new DiningArrangements([
            'date'              => $row[0],
            'breakfast_place'   => $row[1],
            'lunch_place'       => $row[2],
            'dinner_place'      => $row[3],
            'home_decoration_expo_id' => 1,
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

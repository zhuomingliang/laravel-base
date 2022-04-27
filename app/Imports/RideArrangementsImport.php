<?php

namespace App\Imports;

use App\Models\RideArrangements;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class RideArrangementsImport implements ToModel, WithBatchInserts
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (empty($row[0]) || $row[0] == '车次') {
            return null;
        }
        return new RideArrangements([
            'auto_no'                 => $row[0],
            'license_plate_number'    => $row[1],
            'driver'                  => $row[2],
            'driver_phone'            => $row[3],
            'commentator'             => $row[4],
            'commentator_phone'       => $row[5],
            'attendants'              => $row[6],
            'attendants_phone'        => $row[7],
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

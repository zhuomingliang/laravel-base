<?php

namespace App\Imports;

use App\Models\SpeechActivities;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class SpeechActivitiesImport implements ToModel, WithBatchInserts, WithStartRow
{

    protected $start = 2;//第几行开始导入
    protected $size = 1000;//分块大小
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (empty($row[0]) || $row[0] == '主题') {
            return null;
        }

        return new SpeechActivities([
            'title'         => $row[0],
            'date'          => $row[1],
            'time_start'    => gmdate("H:i", $row[2] * 86400),
            'time_end'      => gmdate("H:i", $row[3] * 86400),
            'place'         => $row[4],
            'host'          => $row[5],
            'guest'         => $row[6],
            'home_decoration_expo_id' => 1,
            'status'=>true,
        ]);
    }

    public function startRow(): int
    {
        return $this->start;
    }
    //批量导入1000条
    public function batchSize(): int
    {
        return $this->size;
    }
    //以1000条数据基准切割数据
    public function chunkSize(): int
    {
        return $this->size;
    }


}

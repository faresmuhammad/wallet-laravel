<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class RecordsImport implements ToModel
{

    public function model(array $row)
    {
        ds($row);
/*        if ($row[0] !== 'Title') {
            die();
        }*/
    }
}

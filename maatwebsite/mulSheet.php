<?php
/*
详细用法详见文档：https://docs.laravel-excel.com/3.1/getting-started/
安装Laravel-Excel


composer require maatwebsite/excel

config/app.php
'providers' => [

//Package Service Providers...

Maatwebsite\Excel\ExcelServiceProvider::class,
]

'aliases' => [
...
'Excel' => Maatwebsite\Excel\Facades\Excel::class,
]
发布配置
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"

使用：
导入：

新建导出文件：

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMapping;

class AddUserImport implements WithMapping
{

    public function map($row): array
    {
        if (!in_array("时间", $row)) {
            $row[19] = !is_float($row[19]) ? $row[19] : date("Y-m-d", ($row[19] - 25569) * 24 * 3600);
        }

        return $row;
    }
}

调用：
$data = Excel::toArray(new AddUserImport(), $fileName);

导出：
新建文件：
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class OperativeExport implements ShouldAutoSize, WithMultipleSheets, WithStrictNullComparison
{
    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }


    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->data as $key=>$value) {
            $sheets[$key] = new OperativeDataExport($key,$value);
        }

        return $sheets;
    }


}


## 多sheet的话，必须有sheets的方法：



<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;

class OperativeDataExport implements FromCollection, WithStrictNullComparison, WithTitle
{
    public $data;
    public $title;

    public function __construct($title,array $data)
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function title(): string
    {
        return $this->title;
    }

}

调用：
$file = ['数据1' => $export, '数据2' => $export2];
//一个数组是一个sheet,key表示的是sheet的名字

return Excel::download(new OperativeExport($file), $name . 'biaoge.xlsx')

*/
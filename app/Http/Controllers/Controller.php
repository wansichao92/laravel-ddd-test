<?php

namespace App\Http\Controllers;

use App\Supports\BaseExport;
use App\Supports\ExportBaseResponseDto;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Spatie\DataTransferObject\DataTransferObject;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function success($dataTransferObject, int $code = 0, string $message = 'success')
    {
        $layer = [
            'code' => $code,
            'message' => $message,
        ];
        if($dataTransferObject instanceof DataTransferObject){
            $layer['data'] = $dataTransferObject->all();
        }
        return response()->json($layer);
    }

    public function exportExcel($name, $data, $mergeColumnByFirst = [], $setStyle = [])
    {
        $name = 'excel'.DIRECTORY_SEPARATOR.$name.date('Y-m-d-H-i-s').'.xlsx';
        $result = \Maatwebsite\Excel\Facades\Excel::store(new BaseExport($data,$mergeColumnByFirst,$setStyle), $name, 'public');

        $data = new ExportBaseResponseDto();
        if($result){
            $data->url = config('app.url').'/storage/'.$name;
        }
        return $result ? $this->success($data) : $this->success($data,-1,'导出失败');
    }
}

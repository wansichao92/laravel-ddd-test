<?php

namespace App\Supports;

use Maatwebsite\Excel\Concerns\FromCollection;    // Export collection
use Maatwebsite\Excel\Concerns\WithEvents;     // Automatically register event listeners
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;    // Export 0 is displayed as it is, not null
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BaseExport implements FromCollection, WithEvents,WithStyles
{
    public $data;
    public $arr = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
    public $column = [];
    public $setStyle = [];

    public function __construct(array $data,$column,$setStyle)
    {
        $this->data     = $data;
        $this->column = $column;
        $this->setStyle = $setStyle;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->data);
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [1 => ['font' => ['bold' => true,'size'=> 12],'alignment' => [
            'wraptext'=>true,
            'vertical' => 'center',
            'horizontal' => 'center'
        ],'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'rotation' => 0, //渐变角度
            'startColor' => [
                'rgb' => 'FEFF00' //初始颜色
            ],
            'endColor' => [
                'argb' => 'FEFF00',
            ],
        ],]];
        return $styles;
    }

    public function registerEvents(): array
    {
        $data = $this->data;
        $arr = array_flip($this->arr);
        $column = $this->column;
        $setStyle = $this->setStyle;
        return [
            AfterSheet::class => function(AfterSheet $event) use ($data,$arr,$column,$setStyle) {
                $mergeData = '';
                $merge = [];
                $mergeCells = [];
                $count = count($data);
                foreach ($data as $key=>$item){
                    $event->sheet->getDelegate()->getRowDimension($key)->setRowHeight(25);
                    if($key==0){
                        continue;
                    }
                    if($column){
                        if($item[$arr[$column[0]]] == $mergeData && $mergeData){

                            $merge[] = $key;
                        }else {
                            $mergeData = $item[$arr[$column[0]]];
                            if ($merge) {
                                foreach ($column as $c) {
                                    $mergeCells[] = $c . \Arr::first($merge) . ':' . $c . (\Arr::last($merge) + 1);
                                }
                                $merge = [];
                            }
                        }
                    }
                }
                if(!empty($merge)){
                    foreach ($column as $c){
                        $mergeCells[] = $c.\Arr::first($merge).':'.$c.(\Arr::last($merge)+1);

                    }
                }
                if($mergeCells){
                    $event->sheet->getDelegate()->setMergeCells($mergeCells);
                }

                if($setStyle){
                    $style = [
                        'font' => [
                            'name' => '宋体',
                            'bold' => false,
                            'italic' => false,
                            'strikethrough' => false,
                            'color' => [
                                'rgb' => '000000',
                            ],
                        ],
                        'alignment' => [
                            'wraptext'=>true,
                            'vertical' => 'center',
                            'horizontal' => 'center'
                        ],
                        'borders' => [
                            'allBorders' =>[
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                            ]
                        ],

                    ];
                    foreach ($setStyle as $columnN=>$item){
                        if(isset($item['width'])){
                            $event->sheet->getDelegate()->getColumnDimension($columnN)->setWidth($item['width']);
                        }
                        $event->sheet->getDelegate()->getStyle($columnN.'2:'.$columnN.$count)->getAlignment()->setWrapText(true);
                        $event->sheet->getDelegate()->getStyle($columnN.'2:'.$columnN.$count)->applyFromArray($style);
                    }
                }
            },
        ];
    }

}

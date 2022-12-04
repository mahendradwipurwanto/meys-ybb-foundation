<?php defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Excel
{
    protected $_ci;
  
    public function __construct()
    {
        log_message('Debug', 'PHPSpreadSheet class is loaded.');
        $this->_ci = &get_instance();
        $this->_ci->load->database();
    }

    public function export_participants($data = [], $status = 'All Status')
    {
        $spreadsheet = new Spreadsheet;
        
        // set active sheet
        $sheet = $spreadsheet->setActiveSheetIndex(0);

        // Buat sebuah variabel untuk menampung pengaturan style dari title tabel
        $style_title = [
            'font' => ['bold' => true, 'color' => array('rgb' => '000000')], // Set font nya jadi bold
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
        ];

        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true, 'color' => array('rgb' => 'ffffff')], // Set font nya jadi bold
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
                'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'ff377dff')
            ]
        ];
        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
                'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];

        // set header title
        $sheet->setCellValue('A1', "MEYS PARTICIPANTS DATA ".date('Y')." - {$status}"); // Set kolom A1
        $sheet->mergeCells('A1:D1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1:D1')->applyFromArray($style_title);
        
        // set table header
        $sheet->setCellValue('A3', 'No')
            ->setCellValue('B3', 'Name')
            ->setCellValue('C3', 'Email')
            ->setCellValue('D3', 'Institution');

        // set column style
        $sheet->getStyle('A3')->applyFromArray($style_col);
        $sheet->getStyle('B3')->applyFromArray($style_col);
        $sheet->getStyle('C3')->applyFromArray($style_col);
        $sheet->getStyle('D3')->applyFromArray($style_col);
        
        $row  = 4;
        $no     = 1;
        if(!empty($data)){
            foreach ($data as $key => $val) {
                $sheet->setCellValue('A' . $row, $no)
                    ->setCellValue('B' . $row, $val->name)
                    ->setCellValue('C' . $row, $val->email)
                    ->setCellValue('D' . $row, $val->institution_workplace);

                // set row style
                $sheet->getStyle('A'.$row)->applyFromArray($style_row);
                $sheet->getStyle('B'.$row)->applyFromArray($style_row);
                $sheet->getStyle('C'.$row)->applyFromArray($style_row);
                $sheet->getStyle('D'.$row)->applyFromArray($style_row);

                // set auto width
                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);
                $sheet->getColumnDimension('D')->setAutoSize(true);

                // set row height
                $sheet->getRowDimension($row)->setRowHeight(20);

                $row++;
                $no++;
            }

            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Export Participants Data '.date("dMY").' - '.$status.'.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');

        }
    }
}

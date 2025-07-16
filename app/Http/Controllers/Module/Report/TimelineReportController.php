<?php

namespace App\Http\Controllers\Module\Report;

use App\Helpers\DataAccessHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class TimelineReportController extends Controller
{
    public function index()
    {
        return view('module.report.timeline.index');
    }

    public function downloadFile($fileName)
    {
        $fileName = base64_decode($fileName);
        $path = public_path()."/document/report/".$fileName;
        return response()->download($path);//->deleteFileAfterSend(true);
    }

    public function getData(Request $request, $mode)
    {
        $query = DB::table('Timeline_Report')
                ->select('*')
                ->where(function($query) use ($request){
                    $query->whereDate('Timeline_Start', '>=', $request->startdate)
                            ->orWhereDate('Timeline_End', '<=', $request->enddate);
                });

        /** SEARCH BY STATUS */
        switch ($request->status) {
            case 'all':
                $query = $query;
                break;
            case 'finish':
                $query = $query->where('Timeline_Status', 'DONE');
                break;
            case 'late':
                $query = $query->where('Timeline_Status', 'LATE');
                break;
            case 'on_progress':
                $query = $query->where('Timeline_Status', 'ON PROGRESS');
                break;
            case 'up_comming':
                $query = $query->where('Timeline_Status', 'UP COMMING');
                break;
            default:
                break;
        }

        /** SEARCH BY FILTER */
        switch ($request->filter) {
            case 'project':
                $query = $query->where('Project_Name', 'like', '%'.$request->search.'%');
                break;
            case 'pc':
                $query = $query->where('PC', 'like', '%'.$request->search.'%');
                break;
            case 'karyawan':
                $query = $query->where('Karyawan', 'like', '%'.$request->search.'%');
                break;
            default:
                # code...
                break;
        }
            
        $data['report'] = $query->orderBy('Project_Name')->orderBy('Timeline_Start')->get();
        $data['column'] = DB::getSchemaBuilder()->getColumnListing("Timeline_Report");

        if($mode != 'preview'){
            return self::generateReport($data,$request,$mode);
        } else {
            return response()->json([
                'status' => [
                    'msg' => 'OK',
                    'code' => JsonResponse::HTTP_OK,
                ],
                'data' => $data['report'],
                'column' => $data['column']
            ], JsonResponse::HTTP_OK);
        }
    }

    public function generateReport($data, $request, $mode)
    {
        // INITIATE SPREADSHEET
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

        /** SET EXCEL TITLE */
        $last_column = Coordinate::stringFromColumnIndex(count($data['column'])+1);
        $rangeTitle = 'A1:'.$last_column.'3';
        $activeWorksheet->mergeCells($rangeTitle);
        $activeWorksheet->setCellValue('A1', "Laporan Timeline");
        $activeWorksheet->getStyle($rangeTitle)
                        ->getFont()
                        ->setSize('18')
                        ->setBold(true);
        $activeWorksheet->getStyle($rangeTitle)
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $activeWorksheet->getRowDimension('4')->setRowHeight(30);
        
        $rangePeriode = 'A4:'.$last_column.'4';
        $activeWorksheet->mergeCells($rangePeriode);
        $activeWorksheet->setCellValue('A4', "Periode Timeline : $request->startdate - $request->enddate");
        $activeWorksheet->getStyle($rangePeriode)
                        ->getFont()
                        ->setSize('12')
                        ->setBold(true);
        $activeWorksheet->getStyle($rangePeriode)
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT);

        /** SET HEADER */
        $activeWorksheet->setCellValue('A5', "No");
        $activeWorksheet->getColumnDimension('A')->setWidth(5);

        //Background Color 
        $activeWorksheet
        ->getStyle('A5')
        ->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('0073ca');

        // Font Size & Color
        $activeWorksheet
        ->getStyle('A5')
        ->getFont()
        ->setSize('14')
        ->getColor()
        ->setARGB('FFFFFF');

        // Allignment
        $activeWorksheet
        ->getStyle('A5')
        ->getAlignment()
        ->setVertical(Alignment::VERTICAL_CENTER)
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $startColumn = 'B';
        for ($i=0; $i < count($data['column']); $i++) { 
            // set Field Value
            $activeWorksheet->setCellValue($startColumn.'5', str_replace('_', ' ', $data['column'][$i]));
            $activeWorksheet->getColumnDimension($startColumn)->setWidth(25);

            /** SET STYLING HEADER */
            //Background Color 
            $activeWorksheet
                ->getStyle($startColumn.'5')
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('0073ca');

            // Allignment
            $activeWorksheet
                ->getStyle($startColumn.'5')
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Font Size & Color
            $activeWorksheet
                ->getStyle($startColumn.'5')
                ->getFont()
                ->setSize('14')
                ->getColor()
                ->setARGB('FFFFFF');

            // Row Height
            $activeWorksheet->getRowDimension('5')->setRowHeight(30);

            $startColumn++;
        }

        /** ./END SET HEADER */

        /** SET VALUE */
        $startValueRow = 6;
        foreach ($data['report'] as $key => $value) {
            // Set Value
            $activeWorksheet->setCellValue('A'.$startValueRow, $key+1);
            
            $startValueCol = 'B';
            $valArr = json_decode(json_encode($value), true);
            for ($i=0; $i < count($data['column']); $i++) { 
                if($data['column'][$i] == 'Value'){
                    $colValue = DataAccessHelpers::formatValueMoney($valArr[$data['column'][$i]]);
                } else {
                    $colValue = $valArr[$data['column'][$i]];
                }

                $activeWorksheet->setCellValue($startValueCol.$startValueRow, $colValue);
                $activeWorksheet->getStyle($startValueCol.$startValueRow)
                                ->getNumberFormat()
                                ->setFormatCode(NumberFormat::FORMAT_TEXT);

                $activeWorksheet->getStyle($startValueCol.$startValueRow)
                                ->getAlignment()
                                ->setVertical(Alignment::HORIZONTAL_LEFT);

                $startValueCol++;
            }

            $startValueRow++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = "Laporan Timeline - ".$request->startdate.' - '.$request->enddate;
        $path = public_path()."/document/report/".$fileName;

        if($mode == 'excel'){
            $writer->save($path.'.xlsx');;
            $file = $fileName.'.xlsx';
        } else {
            $pdfWriter = IOFactory::createWriter($spreadsheet, 'Mpdf');
            $pdfWriter->save($path.'.pdf');;
            $file = $fileName.'.pdf';
        }

        return response()->json([
            'status' => [
                'msg' => 'OK',
                'code' => JsonResponse::HTTP_OK,
            ],
            'file' => $file,
        ], JsonResponse::HTTP_OK);
    }
}

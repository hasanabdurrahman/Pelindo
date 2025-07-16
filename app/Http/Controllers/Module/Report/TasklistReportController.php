<?php

namespace App\Http\Controllers\Module\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction\TaskList;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TasklistReportController extends Controller
{
    public function index(Request $request)
    {
        return view('module.report.tasklist.index');
    }


    public function preview(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $status = $request->input('status');
        $filter = $request->input('filter');
        $search = $request->input('search');
        // Check if both startDate and endDate are provided
        if (!empty($startDate) && !empty($endDate)) {
            // Validate that endDate is not earlier than startDate
            if (strtotime($endDate) < strtotime($startDate)) {
                // Handle the error, e.g., by sending a response or setting an error message.
                return response()->json(['error' => 'The endDate cannot be earlier than the startDate'], 400);
            }
        }
        // $tasks = TaskList::select(
        //     't_tasklist.*',
        //     'm_project.name AS project_name',
        //     'm_employee.name AS karyawan_name',
        //     't_timelineA.detail AS timelineA',
        //     't_timelineA.startdate AS startdate',
        //     'm_roles.name AS roles_name',
        //     't_timelineA.enddate AS enddate',
        //     DB::raw("CASE WHEN t_tasklist.approve = 0 THEN 'On Progress' ELSE 'Done' END as status")
        // )
        //     ->join('m_project', 't_tasklist.project_id', '=', 'm_project.id')
        //     ->join('m_employee', 't_tasklist.karyawan_id', '=', 'm_employee.id')
        //     ->join('m_roles', 'm_employee.roles_id', '=', 'm_roles.id')
        //     ->join('t_timelineA', 't_tasklist.timelineA_id', '=', 't_timelineA.id');
            // ->when($status, function ($query) use ($status) {
            //     return $query->where(function ($subquery) use ($status) {
            //         if ($status === 'Success') {
            //             $subquery->where('t_tasklist.approve', 1);
            //         } elseif ($status === 'On Progress') {
            //             $subquery->where('t_tasklist.approve', 0);
            //         }
            //     });
            // })
            // ->when($startDate, function ($query, $startDate) {
            //     return $query->where('t_timelineA.startdate', '>=', $startDate);
            // })
            // ->when($endDate, function ($query, $endDate) {
            //     return $query->where('t_timelineA.enddate', '<=', $endDate);
            // })
            // ->when($filter, function ($query) use ($filter, $search) {
            //     if ($filter === 'employee') {
            //         $query->where('m_employee.name', 'like', "%$search%");
            //     } elseif ($filter === 'position') {
            //         $query->where('m_roles.name', 'like', "%$search%");
            //     } elseif ($filter === 'project') {
            //         $query->where('m_project.name', 'like', "%$search%");
            //     } elseif ($filter === 'task') {
            //         $query->where('t_tasklist.description', 'like', "%$search%");
            //     }
            // })
            // ->latest()
            // ->limit(20)
            // ->get();

        
        $tasks = DB::table('Tasklist_Report')
                    ->select('*')
                    ->where(function($query) use ($startDate, $endDate){
                        $query->whereDate('startdate', '>=', $startDate)
                                ->orWhereDate('enddate', '<=', $endDate);
                    });
        if($request->status != 'All'){
            $tasks = $tasks->where('status', $request->status);
        }

        /** SEARCH BY FILTER */
        switch ($request->filter) {
            case 'employee':
                $tasks = $tasks->where('karyawan_name', 'like', '%'.$request->search.'%');
                break;
            case 'position':
                $tasks = $tasks->where('roles_name', 'like', '%'.$request->search.'%');
                break;
            case 'project':
                $tasks = $tasks->where('project_name', 'like', '%'.$request->search.'%');
                break;
            case 'task':
                $tasks = $tasks->where('timelineA', 'like', '%'.$request->search.'%');
                break;
            default:
                $tasks = $tasks;
                break;
        }

        $tasks = $tasks->latest()->limit(20)->get();
        return response()->json($tasks);
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $status = $request->input('status');
        $filter = $request->input('filter');
        $search = $request->input('search');
        $mode = $request->input('mode');
        // Check if both startDate and endDate are provided
        if (!empty($startDate) && !empty($endDate)) {
            // Validate that endDate is not earlier than startDate
            if (strtotime($endDate) < strtotime($startDate)) {
                // Handle the error, e.g., by sending a response or setting an error message.
                return response()->json(['error' => 'The endDate cannot be earlier than the startDate'], 400);
            }
        }
        $tasks = TaskList::select(
            't_tasklist.*',
            'm_project.name AS project_name',
            'm_employee.name AS karyawan_name',
            't_timelineA.detail AS timelineA',
            't_timelineA.startdate AS startdate',
            'm_roles.name AS roles_name',
            't_timelineA.enddate AS enddate',
            DB::raw("CASE WHEN t_tasklist.approve = 0 THEN 'On Progress' ELSE 'Success' END as status")
        )
            ->join('m_project', 't_tasklist.project_id', '=', 'm_project.id')
            ->join('m_employee', 't_tasklist.karyawan_id', '=', 'm_employee.id')
            ->join('m_roles', 'm_employee.roles_id', '=', 'm_roles.id')
            ->join('t_timelineA', 't_tasklist.timelineA_id', '=', 't_timelineA.id')
            ->when($status, function ($query) use ($status) {
                return $query->where(function ($subquery) use ($status) {
                    if ($status === 'Success') {
                        $subquery->where('t_tasklist.approve', 1);
                    } elseif ($status === 'On Progress') {
                        $subquery->where('t_tasklist.approve', 0);
                    }
                });
            })
            ->when($startDate, function ($query, $startDate) {
                return $query->where('t_timelineA.startdate', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->where('t_timelineA.enddate', '<=', $endDate);
            })
            ->when($filter, function ($query) use ($filter, $search) {
                if ($filter === 'employee') {
                    $query->where('m_employee.name', 'like', "%$search%");
                } elseif ($filter === 'position') {
                    $query->where('m_roles.name', 'like', "%$search%");
                } elseif ($filter === 'project') {
                    $query->where('m_project.name', 'like', "%$search%");
                } elseif ($filter === 'task') {
                    $query->where('t_tasklist.description', 'like', "%$search%");
                }
            })
            ->latest()
            ->limit(20)
            ->get();


        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

        // SET EXCEL TITLE
        $lastColumn = Coordinate::stringFromColumnIndex(10); // Adjust this based on the number of columns
        $rangeTitle = 'A1:' . $lastColumn . '1'; // Changed from A1:A3 to A1
        $activeWorksheet->mergeCells($rangeTitle);
        $activeWorksheet->setCellValue('A1', "Laporan Tasklist");
        $activeWorksheet->getStyle($rangeTitle)
            ->getFont()
            ->setSize(18)
            ->setBold(true);
        $activeWorksheet->getStyle($rangeTitle)
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rangePeriode = 'A2:' . $lastColumn . '2'; // Changed from A4:A4 to A2
        $activeWorksheet->mergeCells($rangePeriode);
        $activeWorksheet->setCellValue('A2', "Periode Timeline: $startDate - $endDate");
        $activeWorksheet->getStyle($rangePeriode)
            ->getFont()
            ->setSize(12)
            ->setBold(true);
        $activeWorksheet->getStyle($rangePeriode)
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // SET HEADER
        $headerLabels = [
            'No',
            'Transaction Number',
            'Employee Name',
            'Progress',
            'Description',
            'Project Name',
            'Task Name',
            'Start Date',
            'End Date',
            'Status',
        ];
        $headerStyle = [
            'font' => ['size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0073ca']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $activeWorksheet->getStyle('A3:' . $lastColumn . '3')->applyFromArray($headerStyle);
        $activeWorksheet->getColumnDimension('A')->setWidth(10);

        for ($col = 'A'; $col <= $lastColumn; $col++) {
            $activeWorksheet->getColumnDimension($col)->setWidth(30);
        }

        $activeWorksheet->fromArray([$headerLabels], null, 'A3');
        $activeWorksheet->getStyle('A3:' . $lastColumn . '3')->applyFromArray($headerStyle);

        // Add data from the query to the Excel file and apply borders
        $row = 4; // Start from row 4
        $rowNumber = 1;
        foreach ($tasks as $task) {
            $activeWorksheet->setCellValue('A' . $row, $rowNumber);
            $activeWorksheet->setCellValue('B' . $row, $task->transactionnumber);
            $activeWorksheet->setCellValue('C' . $row, $task->karyawan_name);
            $activeWorksheet->setCellValue('D' . $row, $task->progress);
            $activeWorksheet->setCellValue('E' . $row, $task->description);
            $activeWorksheet->setCellValue('F' . $row, $task->project_name);
            $activeWorksheet->setCellValue('G' . $row, $task->timelineA);
            $activeWorksheet->setCellValue('H' . $row, $task->startdate);
            $activeWorksheet->setCellValue('I' . $row, $task->enddate);
            $activeWorksheet->setCellValue('J' . $row, $task->status);

            $activeWorksheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray($headerStyle);

            $row++;
            $rowNumber++;
        }


        if ($mode == 'excel') {
            // Create a writer object
            $writer = new Xlsx($spreadsheet);

            // Set response headers
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename=Laporan Timeline - $startDate - $endDate.xlsx");
            header('Cache-Control: max-age=0');

            // Save the file to the output buffer
            $writer->save('php://output');

            // Send the output buffer to the browser
            ob_end_flush();

            exit();
        }
        if ($mode == 'pdf') {
            // Create a writer object for PDF (Mpdf)
            $pdfWriter = IOFactory::createWriter($spreadsheet, 'Mpdf');

            // Set response headers for PDF
            header('Content-Type: application/pdf');
            header("Content-Disposition: attachment;filename=Laporan Timeline - $startDate - $endDate.pdf");
            header('Cache-Control: max-age=0');

            // Output the PDF directly to the browser
            $pdfWriter->save('php://output');

            // Send the output buffer to the browser
            ob_end_flush();

            exit();
        }
    }
}

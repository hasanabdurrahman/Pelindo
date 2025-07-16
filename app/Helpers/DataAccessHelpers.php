<?php

namespace App\Helpers;

use Carbon\Carbon;
use CurlHandle;
use Illuminate\Support\Facades\DB;

class DataAccessHelpers
{

    public static function convertArrayToNumber($value)
    {

        try {


            $value = str_replace(',', '', $value);

            // Mengubah string menjadi float/angka
            $value = floatval($value);



            return $value;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public static function convertToNumber($value)
    {

        try {


            $value = str_replace(',', '', $value);

            // Mengubah string menjadi float/angka
            $value = floatval($value);


            return $value;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public static function generateTransactionNumber($project_id)
    {
        $projectCode = DB::table('m_project')
            ->select('code')
            ->where('id', '=', $project_id)
            ->first();


        $currentYear = Carbon::now()->translatedFormat('Y');

        $totalProject = DB::table('t_timeline')->select('*')->whereYear('created_at', $currentYear)->orderBy('created_at', 'DESC')->get();
        $number = '0000';

        if ($totalProject == null) {
            $number = '0001';
        } else {
            $lastId = explode('/', $totalProject[0]->transactionnumber);
            $number = (int) $lastId[count($lastId) - 1] + 1;
            $number = sprintf('%04d', $number);
        }

        // } else if (strlen($totalProject) < 4) {
        //     // $number = substr($number, 0, $totalProject).$totalProject;
        //     $number = sprintf("%04d", (int)$totalProject + 1);
        // } else {
        //     $number = $totalProject;
        // }

        $code = explode('/', $projectCode->code)[0];
        $transNumber = "TL/$code/$currentYear/$number";

        return $transNumber;
    }

    public static function generateAdTransNumber($project_id, $transNumber)
    {
        // $projectCode = DB::table('m_project')
        //                 ->select('code')
        //                 ->where('id','=',$project_id)
        //                 ->first();
        $timeline = DB::table('t_timeline')
            ->select('transactionnumber')
            ->where('project_id', $project_id)
            ->first();

        $totalAd = DB::table('t_additional')->select('*')->where('transactionnumber', $transNumber)->where('project_id', $project_id)->count();
        $number = '0000';

        if ($totalAd == 0) {
            $number = '0001';
        } else if (strlen($totalAd) < 4) {
            $number = sprintf("%04d", (int)$totalAd + 1);
        } else {
            $number = $totalAd;
        }

        $adTransNumber = "AD/$timeline->transactionnumber/$number";
        return $adTransNumber;
    }

    public static function generateRequestTeamTransNumber()
    {
        $currentYear = Carbon::now()->translatedFormat('Y');
        $totalRequest = DB::table('trx_requestteam')->select('*')->whereYear('transactiondate', $currentYear)->count();

        if ($totalRequest == 0) {
            $number = '0001';
        } else if (strlen($totalRequest) < 4) {
            $number = sprintf("%04d", (int)$totalRequest + 1);
        } else {
            $number = $totalRequest;
        }

        $transNumber = "REQ/$currentYear/$number";
        return $transNumber;
    }
    public static function generateRequestTicketTransNumber()
    {
        $currentYear = Carbon::now()->translatedFormat('Y');
        $totalRequest = DB::table('trx_requestticket')->select('*')->whereYear('transactiondate', $currentYear)->count();

        if ($totalRequest == 0) {
            $number = '0001';
        } else if (strlen($totalRequest) < 4) {
            $number = sprintf("%04d", (int)$totalRequest + 1);
        } else {
            $number = $totalRequest;
        }

        $transNumber = "REQ/$currentYear/$number";
        return $transNumber;
    }

    public static function generateTerminTransactionNumber($project_id)
    {
        $projectCode = DB::table('m_project')
            ->select('code')
            ->where('id', '=', $project_id)
            ->first();

        $currentYear = Carbon::now()->translatedFormat('Y');

        $totalTermin = DB::table('t_termin')->select('*')->whereYear('created_at', $currentYear)->count();
        $number = '0000';

        if ($totalTermin == 0) {
            $number = '0001';
        } else if (strlen($totalTermin) < 4) {
            // $number = substr($number, 0, $totalTermin).$totalTermin;
            $number = sprintf("%04d", (int)$totalTermin);
        } else {
            $number = $totalTermin;
        }

        $code = explode('/', $projectCode->code)[0];
        $transNumber = "TRM/$code/$currentYear/$number";

        return $transNumber;
    }

    public static function weeks($month, $year)
    {
        $num_of_days = date("t", mktime(0, 0, 0, $month, 1, $year));
        $lastday = date("t", mktime(0, 0, 0, $month, 1, $year));
        $no_of_weeks = 0;
        $count_weeks = 0;

        while ($no_of_weeks < $lastday) {
            $no_of_weeks += 7;
            $count_weeks++;
        }

        return (int)floor($lastday / 7);
        // return $count_weeks;
    }

    public static function formatValueMoney($number)
    {
        $val = number_format($number, 2, ".", ",");
        $setVal = 'Rp. ' . $val;

        return $setVal;
    }

    public static function is_base64($s)
    {
        return (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s);
    }
}

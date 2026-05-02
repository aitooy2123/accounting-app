<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\Prints;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CmsHelper
{
    use HasFactory;

    //=====================================================================================================
    // หมวด แปลงค่า
    //=====================================================================================================

    // ใช้แล้วของมูลวันที่ เช่น 2021-01-11 08:04:01 เป็นวันที่ภาษาไทย
    // ตัวอย่างการใช้งาน {{ DateThai($val) }} จะแสดงเป็น 11 มกราคม 2564
    public static function DateThai($strDate)
    {
        if ($strDate == '0000-00-00' || $strDate == '' || $strDate == null) {
            return '-';
        }

        $strYear = date("Y", strtotime($strDate)) + 543;
        $strYear1 = date("y", strtotime($strDate)) + 43;
        $strYear2 = date("Y", strtotime($strDate)) ;
        $strMonth = date("n", strtotime($strDate));
        $strMonth2 = date("m", strtotime($strDate));
        $strDay = date("j", strtotime($strDate));
        $strHour = date("H", strtotime($strDate));
        $strMinute = date("i", strtotime($strDate));
        $strSeconds = date("s", strtotime($strDate));
        $strMonthCut = array("", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
        $strMonthCut2 = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
        $strMonthThai = $strMonthCut[$strMonth];
        $strMonthThai2 = $strMonthCut2[$strMonth];
        // return "$strDay $strMonthThai $strYear, $strHour:$strMinute";

        return array(
            // "Ymd" => $strYear2 . '-' . $strMonth . '-' . $strDay,
            "dmY" => $strDay . ' ' . $strMonthThai . ' ' . $strYear,
            "dmy" => $strDay . ' ' . $strMonthThai . ' ' . $strYear1,
            "dMY" => $strDay . ' ' . $strMonthThai2 . ' ' . $strYear,
            "dMYt" => $strDay . ' เดือน ' . $strMonthThai2 . ' พ.ศ. ' . $strYear,
            "dmYHi" => $strDay . ' ' . $strMonthThai . ' ' . $strYear . ' เวลา ' . $strHour . ':' . $strMinute,
            "dMYHi" => $strDay . ' ' . $strMonthThai2 . ' ' . $strYear . ' เวลา ' . $strHour . ':' . $strMinute,
            "dMYHin" => $strDay . ' ' . $strMonthThai2 . ' ' . $strYear . ' เวลา ' . $strHour . ':' . $strMinute . ' น.',
            "Hi" => $strHour . ':' . $strMinute,
            "Date" => $strYear2 . '-' . $strMonth2 . '-' . $strDay,
            "d" => $strDay,
            "m" => $strMonthThai,
            "M" => $strMonthThai2,
            "Y" => $strYear,
        );
    }

    // แปลงเลขเดือนเป็นชื่อเดือน
    // เรียกใช้งาน {{ Cms::MonthThai($val) }}
    public static function MonthThai($val)
    {
        $arr = [
            '1' => 'มกราคม',
            '2' => 'กุมภาพันธ์',
            '3' => 'มีนาคม',
            '4' => 'เมษายน',
            '5' => 'พฤษภาคม',
            '6' => 'มิถุนายน',
            '7' => 'กรกฎาคม',
            '8' => 'สิงหาคม',
            '9' => 'กันยายน',
            '10' => 'ตุลาคม',
            '11' => 'พฤศจิกายน',
            '12' => 'ธันวาคม',
        ];
        return $arr[$val] ?? false;
    }

    // แปลงเครื่องหมาย 01/02/2022 -> 2022-02-01 (dash)
    // แปลงเครื่องหมาย 2022-02-01 -> 01/02/2022 (slash)
    // เรียกใช้งาน {{ Cms::DateFormat($val)['dash'] }}
    public static function DateChangeFormat($val)
    {
        if (empty($val)) {
            return false;
        }

        [$day, $month, $year] = explode("/", $val);
        $year -= 543;

        return [
            'dash' => "$year-$month-$day",
            'slash' => "$day/$month/$year",
        ];
    }

    // แปลงเดือนไทยเป็นเลข
    // เรียกใช้งาน Cms::Date_Month2Num[$val]
    public static function Date_Month2Num($val)
    {
        if (empty($val)) {
            return false;
        }

        $array = [
            'มกราคม' => '01',
            'กุมภาพันธ์' => '02',
            'มีนาคม' => '03',
            'เมษายน' => '04',
            'พฤษภาคม' => '05',
            'มิถุนายน' => '06',
            'กรกฎาคม' => '07',
            'สิงหาคม' => '08',
            'กันยายน' => '09',
            'ตุลาคม' => '10',
            'พฤศจิกายน' => '11',
            'ธันวาคม' => '12',
        ];
        $bc_year = explode(" ", $val);
        $day = $bc_year['0'];
        $month = $array[$bc_year['1']];
        $year = $bc_year['2'] - 543;
        return $year . '-' . $month . '-' . $day;
    }

    // แปลงเลขอราบิคเป็นไทย
    // เรียกใช้งาน Cms::Numth[$val]
    public static function Numth($val)
    {
        $temp = str_replace("0", "๐", $val);
        $temp = str_replace("1", "๑", $temp);
        $temp = str_replace("2", "๒", $temp);
        $temp = str_replace("3", "๓", $temp);
        $temp = str_replace("4", "๔", $temp);
        $temp = str_replace("5", "๕", $temp);
        $temp = str_replace("6", "๖", $temp);
        $temp = str_replace("7", "๗", $temp);
        $temp = str_replace("8", "๘", $temp);
        $temp = str_replace("9", "๙", $temp);
        return $temp;
    }

    // กำหนดรูปแบบโทรศัพท์ : {{ Cms::TextFormat($value,'__-____-____') }}
    // กำหนดรูปแบบบัตรประชาชน : {{ Cms::TextFormat($value) }}
    public static function TextFormat($text = '', $pattern = '', $ex = '')
    {
        $cid = ($text == '') ? '0000000000000' : $text;
        $pattern = ($pattern == '') ? '_-____-_____-__-_' : $pattern;
        $p = explode('-', $pattern);
        $ex = ($ex == '') ? '-' : $ex;
        $first = 0;
        $last = 0;
        for ($i = 0; $i <= count($p) - 1; $i++) {
            $first = $first + $last;
            $last = strlen($p[$i]);
            $returnText[$i] = substr($cid, $first, $last);
        }
        return implode($ex, $returnText);
    }














    // -------------------------------------------------------

    public static function CountCustomer()
    {
        $count = Customer::count();
        return number_format($count) ?? 0;
    }

   
}


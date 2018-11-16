<?php

namespace App\Http\Controllers\Events;

use App\Models\Event\AttendanceListPdf;
use App\Models\Event\AttendanceListPricePdf;
use App\Models\Event\GuestListPdf;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventPrintController extends Controller
{
    public function attendanceList($id, $type=null) {


        if (strcmp($type, 'guest_list') == 0) {

            $guest_list_class = new GuestListPdf();
            $guest_list_class->generatePdfGuestList($id);

        }elseif (strcmp($type, 'attendance_price') == 0){

            $attendanceListPdf = new AttendanceListPricePdf();
            $attendanceListPdf->generatePdfEventAttendance($id, 'I');

        }else{

            $attendanceListPdf = new AttendanceListPdf();
            $attendanceListPdf->generatePdfEventAttendance($id, 'I');

        }



    }




}

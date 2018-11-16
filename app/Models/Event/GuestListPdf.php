<?php

namespace App\Models\Event;

use App\Models\Club;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use TCPDF;

class GuestListPdf extends Model
{

    public function generatePdfGuestList($id, $output="I")
    {

        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

        $event = Event::find($id);
        if(!$event) return;

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);

        $pdf->SetTitle('title');

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //set auto page breaks
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('freesans', '', 8, '', 'true');

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        $pdf->AddPage();

        $pdf->SetLineWidth(0.3);
        $pdf->Line(15, 34, 280, 34);

        $pdf->Image(
            public_path('images/app/B4B_logo_alpha_invoice.png'), 20, 18, 0, 14, 'PNG', '', '', false, 150
        );

        $pdf->SetFont('freesans', '', 15, '', 'true');
        $txt_title = 'Zoznam hostí ';
        $pdf->SetXY(40, 20);
//        $pdf->Cell(55, 10, $txt_title , 0, 0, 'L');
        $txt_title =  'Zoznam hostí - '. $event->title . ', '. $event->club->short_title . ', ' . Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from )->format('d.n.Y');
        $pdf->Cell(200, 10, $txt_title , 0, 1, 'L');

        $pdf->setY(40);

        //$pdf->SetXY(15, 70);

        $attendance['member'] = null;
        $attendance['guest'] = null;
        $attendance['member_apology'] = null;

        $event_class = new Event();
        $attendance_raw = $event_class->getEventAttendanceList($id);

        $atendance_activity = $event_class->getEventActivity($id);

        $club_class = new Club();
        //$club_users = $club_class->getUsersFromClub($event->club_id);
        $club_users = $club_class->getUsersFromClubWithCompany($event->club_id);

//
//        if ($club_users){
//            foreach ($club_users as $k => $v){
//
//
//                dump($v);
//
//
//            }
//        }
//
//        dd();


        // rozdelime na clenov klubu a hosti
        if ($attendance_raw){
            foreach ($attendance_raw as $k => $a){

                $key = array_search( $a->user_id, array_column($club_users->toArray(), 'id'));

                if ($a->status_id == 2 ) {

                    if (in_array($a->user_id, $club_users->pluck('id')->toArray())) {
                        $attendance['member'][] = [ 'user' => $a, 'membership' =>  $club_users[$key] ];
                    } else {
                        $attendance['guest'][] = [ 'user' => $a, 'membership' =>  null ];
                    }
                }

                if ($a->status_id == 3 ) {
                    if (in_array($a->user_id, $club_users->pluck('id')->toArray())) {
                        //$attendance['member_apology'][$k] = $a;
                        $attendance['member_apology'][] = [ 'user' => $a, 'membership' => null ];

                    }
                }

            }
        }


        //dd($attendance);

        $tbl = "";

        $pdf->SetFont('freesans', 'B', 10, '', true);

        if ($attendance['member']){

            $tbl = ' <table border="0.5" cellpadding="4px" cellspacing="0" align="center">';
            $tbl .= '<thead>';
            $tbl .= '    <tr style="padding: 20px; background-color: #eeeeee">';
            $tbl .= '        <th width="30%" align="left">Členovia klubu</th>';
            $tbl .= '        <th width="25%" align="left">Odbor</th>';
            $tbl .= '        <th width="45%" align="left">Priestor pre vaše poznámky</th>';
            $tbl .= '    </tr>';
            $tbl .= '</thead>';
            $tbl .= '<tbody>';

            foreach ($attendance['member'] as $a){
                $tbl .= '    <tr style="padding: 5px; ">';
                $tbl .= '        <td width="30%"  align="left">'. $a['user']->name . ' ' . $a['user']->surname . '<br>'. $a['membership']->company_name . '</td>';
                $tbl .= '        <td width="25%" align="left" style="padding: 20px;">' . $a['user']->industry . '<br>' . $a['user']->internet . '</td>';
                $tbl .= '        <td width="45%"  ></td>';
                $tbl .= '    </tr>';
            }
            $tbl .= '</tbody>';
            $tbl .= '</table>';

            $pdf->writeHTML($tbl, true, false, false, false, '');

        }


        $pdf->SetFont('freesans', '', 10, '', 'true');


        if ($attendance['guest']){

            $tbl = ' <table border="0.5" cellpadding="4px" cellspacing="0" align="center">';
            $tbl .= '<thead>';
            $tbl .= '    <tr style="padding: 20px; background-color: #eeeeee">';
            $tbl .= '        <th width="30%" align="left">Hostia</th>';
            $tbl .= '        <th width="25%" align="left">Odbor</th>';
            $tbl .= '        <th width="45%" align="left">Priestor pre vaše poznámky</th>';
            $tbl .= '    </tr>';
            $tbl .= '</thead>';
            $tbl .= '<tbody>';

            foreach ($attendance['guest'] as $a){
                $tbl .= '    <tr style="padding: 5px; ">';
                $tbl .= '        <td width="30%" align="left">'. $a['user']->name . ' ' . $a['user']->surname . '<br>' . $a['user']->company . '</td>';
                $tbl .= '        <td width="25%" align="left">'. $a['user']->industry . '<br>'. $a['user']->internet . '</td>';
                $tbl .= '        <td width="45%"></td>';
                $tbl .= '    </tr>';
            }

            for ($i = 0; $i <= 1; $i++) {
                $tbl .= '    <tr style="padding: 5px; ">';
                $tbl .= '        <td width="30%" align="left"></td>';
                $tbl .= '        <td height="40" width="25%" align="left" style="font-size: smaller; padding: 20px; "></td>';
                $tbl .= '        <td width="45%"></td>';
                $tbl .= '    </tr>';
            }

            $tbl .= '</tbody>';
            $tbl .= '</table>';

            $pdf->writeHTML($tbl, true, false, false, false, '');
        }


        if (count($atendance_activity) > 0){
            // Info o franchise
            $pdf->SetFont('dejavusans', '', 11, '', 'true');
            $string = "";
            foreach ($atendance_activity as $a){
                $string .= $a->activity . ': ' . $a->name . ' ' . $a->surname . '   ';
            }
            $pdf->Cell(200, 6, $string, 0, 1, 'L');
        }


        $pdf->SetFont('dejavusans', '', 8, '', 'true');
        // Info o franchise
        $pdf->Cell(100, 6, __('email.email_company_franchise'), 0, 2, 'L');


        //ak je zadany vystup S vratime string
        if(strcmp($output, 'S') == 0) {
            return $pdf->Output('Event_b4b_'. $event->id .'.pdf', 'S');
        }
        $pdf->Output('Event_b4b_'. $event->id .'.pdf', 'I');
    }


}

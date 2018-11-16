<?php

namespace App\Models\Event;

use App\Models\Club;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use TCPDF;


class AttendanceListPdf extends Model
{


    public function generatePdfEventAttendance($id, $output="I")
    {

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        $event = Event::find($id);
        if(!$event) return;

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetTitle('title');

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //set auto page breaks
        $pdf->SetAutoPageBreak(true, 10);
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('freesans', '', 8, '', 'true');

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        $pdf->AddPage();

        $pdf->SetLineWidth(0.3);
        $pdf->Line(15, 34, 195, 34);

        $pdf->Image(
            public_path('images/app/B4B_logo_alpha_invoice.png'), 20, 18, 0, 14, 'PNG', '', '', false, 150
        );

        $pdf->SetFont('freesans', '', 15, '', 'true');
        $txt_title = 'Prezenčná listina ';
        $pdf->SetXY(40, 20);
        $pdf->Cell(40, 10, $txt_title , 0, 0, 'L');

        $pdf->SetFont('freesans', '', 14, '', 'true');
        $txt_title =  $event->title . ', ' . $event->club->short_title . ', ' . Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from )->format('d.n.Y');
        $pdf->Cell(97, 10, $txt_title , 0, 2, 'L');

        $txt_title = 'Každý účastník zúčastnením sa na akcii Business for Breakfast, zároveň udeľuje spoločnosti Business for Breakfast Slovakia s.r.o., so sídlom: Tomášikova 17, Bratislava 821 09, IČO: 46 831 932, zapísanej v Obchodnom registri Okresného súdu Bratislava I, Oddiel: Sro, Vložka č.: 94263/B, ako organizátorovi akcie, bezodplatný súhlas (privolenie) podľa § 12 ods. 1 Občianskeho zákonníka na použitie jeho obrazového alebo zvukového záznamu ako prejavu osobnej povahy v súvislosti s akciou. 
Každý účastník zúčastnením sa na akcii Business for Breakfast udeľuje spoločnosti Business for Breakfast Slovakia s.r.o., so sídlom: Tomášikova 17, Bratislava 821 09, IČO: 46 831 932, zapísanej v Obchodnom registri Okresného súdu Bratislava I, Oddiel: Sro, Vložka č.: 94263/B, ako organizátorovi akcie, súhlas na spracovanie svojich osobných údajov v rozsahu meno, priezvisko, pracovná funkcia, telefón a email, (prípadne iné údaje uverejnené na vizitke účastníka) v zmysle zákona 122/2013 Z.z. o ochrane osobných údajov na účely evidencie v databáze a marketingovej komunikácie. Súhlas sa udeľuje na dobu neurčitú, pričom je možné ho kedykoľvek písomne odvolať.';

        $pdf->SetXY(15, 37);
        $pdf->SetFont('freesans', '', 7, '', 'false');
        $pdf->MultiCell(180, 5, $txt_title, 0, 'L', 0, 2, '' ,'', true);

        $pdf->SetXY(15, 70);

        $attendance['member'] = null;
        $attendance['guest'] = null;
        $attendance['member_apology'] = null;

        $event_class = new Event();
        $attendance_raw = $event_class->getEventAttendanceList($id);
        $atendance_activity = $event_class->getEventActivity($id);


        $club_class = new Club();
        $club_users = $club_class->getUsersFromClub($event->club_id);

        // rozdelime na clenov klubu a hosti
        if ($attendance_raw){
            foreach ($attendance_raw as $a){
                if ($a->status_id == 2 ) {
                    if (in_array($a->user_id, $club_users->pluck('id')->toArray())) {
                        $attendance['member'][] = $a;
                    } else {
                        $attendance['guest'][] = $a;
                    }
                }

                if ($a->status_id == 3 ) {
                    if (in_array($a->user_id, $club_users->pluck('id')->toArray())) {
                        $attendance['member_apology'][] = $a;
                    }
                }

            }
        }

        $tbl = "";
        $sum = 0;

        $pdf->SetFont('freesans', '', 10, '', 'true');

        if ($attendance['member']){

            $tbl = ' <table border="0.5" cellpadding="4px" cellspacing="0" align="center">';
            $tbl .= '<thead>';
            $tbl .= '    <tr style="padding: 20px; background-color: #eeeeee">';
            $tbl .= '        <th width="4%" align="left">#</th>';
            $tbl .= '        <th width="25%" align="left">Členovia klubu</th>';
            $tbl .= '        <th width="20%">Firma</th>';
            $tbl .= '        <th width="20%">Odvetvie do:</th>';
            $tbl .= '        <th width="15%" >Poznámka</th>';
            $tbl .= '        <th width="16%" >Podpis</th>';
            $tbl .= '    </tr>';
            $tbl .= '</thead>';

            foreach ($attendance['member'] as $k => $a){

                $sum = $sum + 1;
                $tbl .= '    <tr style="padding: 5px; ">';
                $tbl .= '        <td width="4%" align="left"><small> '. (intval($k) + 1)  . '</small></td>';
                $tbl .= '        <td width="25%" align="left">'. $a->name . ' ' . $a->surname . '</td>';
                $tbl .= '        <td width="20%" align="left" style="font-size: smaller; padding: 20px;">'. $a->company . '</td>';
                $tbl .= '        <td width="20%" align="left" style="font-size: smaller; padding: 20px;">' . $a->industry . '</td>';
                $tbl .= '        <td width="15%"><small>'. $a->phone . '</small></td>';
                $tbl .= '        <td width="16%"></td>';
                $tbl .= '    </tr>';

            }

            $tbl .= '<tfoot>';
            $tbl .= '    <tr style="padding: 20px; background-color: #fff">';
            $tbl .= '        <th width="4%" align="left">#</th>';
            $tbl .= '        <th colspan="4" width="80%" align="left"></th>';
            $tbl .= '        <th width="16%" >Spolu: ' . $sum . '</th>';
            $tbl .= '    </tr>';
            $tbl .= '</tfoot>';

            $tbl .= '</table>';

            $pdf->writeHTML($tbl, true, false, false, false, '');

        }


        $pdf->SetFont('freesans', '', 10, '', 'true');

        $sum = 0;
        if ($attendance['member_apology']){

            $tbl = ' <table border="0.5" cellpadding="4px" cellspacing="0" align="center">';
            $tbl .= '<thead>';
            $tbl .= '    <tr style="padding: 20px; background-color: #eeeeee">';
            $tbl .= '        <th width="4%" align="left">#</th>';
            $tbl .= '        <th width="25%" align="left">Ospravedlnení členovia</th>';
            $tbl .= '        <th width="20%">Firma</th>';
            $tbl .= '        <th width="20%">Odvetvie do:</th>';
            $tbl .= '        <th width="15%" >Poznámka</th>';
            $tbl .= '        <th width="16%" >Podpis</th>';
            $tbl .= '    </tr>';
            $tbl .= '</thead>';

            foreach ($attendance['member_apology'] as $k =>  $a){

                $sum = $sum + 1;
                $tbl .= '    <tr style="padding: 5px; ">';
                $tbl .= '        <td width="4%" align="left"><small> '. (intval($k) + 1)  . '</small></td>';
                $tbl .= '        <td width="25%" align="left">'. $a->name . ' ' . $a->surname . '</td>';
                $tbl .= '        <td width="20%" align="left" style="font-size: smaller; padding: 20px;">'. $a->company . '</td>';
                $tbl .= '        <td width="20%" align="left" style="font-size: smaller; padding: 20px;">' . $a->industry . '</td>';
                $tbl .= '        <td width="15%"><small>'. $a->phone . '</small></td>';
                $tbl .= '        <td width="16%"></td>';
                $tbl .= '    </tr>';

            }

            $tbl .= '<tfoot>';
            $tbl .= '    <tr style="padding: 20px; background-color: #fff">';
            $tbl .= '        <th width="4%" align="left">#</th>';
            $tbl .= '        <th colspan="4" width="80%" align="left"></th>';
            $tbl .= '        <th width="16%" >Spolu: ' . $sum . '</th>';
            $tbl .= '    </tr>';
            $tbl .= '</tfoot>';

            $tbl .= '</table>';

            $pdf->writeHTML($tbl, true, false, false, false, '');

        }

        $sum= 0;
        if ($attendance['guest']){

            $tbl = ' <table border="0.5" cellpadding="4px" cellspacing="0" align="center">';
            $tbl .= '<thead>';
            $tbl .= '    <tr style="padding: 20px; background-color: #eeeeee">';
            $tbl .= '        <th width="4%" align="left">#</th>';
            $tbl .= '        <th width="25%" align="left">Hostia</th>';
            $tbl .= '        <th width="20%">Firma</th>';
            $tbl .= '        <th width="20%">Odvetvie do:</th>';
            $tbl .= '        <th width="15%" >Poznámka</th>';
            $tbl .= '        <th width="16%" >Podpis</th>';
            $tbl .= '    </tr>';
            $tbl .= '</thead>';

            foreach ($attendance['guest'] as $k => $a){

                $sum = $sum + 1;
                $tbl .= '    <tr style="padding: 5px; ">';
                $tbl .= '        <td width="4%" align="left"><small> '. (intval($k) + 1)  . '</small></td>';
                $tbl .= '        <td width="25%" align="left">'. $a->name . ' ' . $a->surname . '</td>';
                $tbl .= '        <td width="20%" align="left" style="font-size: smaller; padding: 20px;">'. $a->company . '</td>';
                $tbl .= '        <td width="20%" align="left" style="font-size: smaller; padding: 20px;">' . $a->industry . '</td>';
                $tbl .= '        <td width="15%"><small>'. $a->phone . '<br>' . $a->invited_user . '</small></td>';
                $tbl .= '        <td width="16%"></td>';
                $tbl .= '    </tr>';

            }

            for ($i = 0; $i <= 2; $i++) {
                $tbl .= '    <tr style="padding: 5px; ">';
                $tbl .= '        <td width="4%" align="left">' . ''  . '</td>';
                $tbl .= '        <td width="25%" align="left"></td>';
                $tbl .= '        <td width="20%" align="left" style="font-size: smaller; padding: 20px; "></td>';
                $tbl .= '        <td width="20%" align="left" style="font-size: smaller; padding: 20px;"></td>';
                $tbl .= '        <td width="15%"></td>';
                $tbl .= '        <td width="16%"></td>';
                $tbl .= '    </tr>';
            }

            $tbl .= '<tfoot>';
            $tbl .= '    <tr style="padding: 20px; background-color: #fff">';
            $tbl .= '        <th width="4%" align="left">#</th>';
            $tbl .= '        <th colspan="4" width="80%" align="left"></th>';
            $tbl .= '        <th width="16%" >Spolu: ' . $sum . '</th>';
            $tbl .= '    </tr>';
            $tbl .= '</tfoot>';

        $tbl .= '</table>';

        $pdf->writeHTML($tbl, true, false, false, false, '');

        }


        if (count($atendance_activity) > 0){
            // Info o franchise
            $pdf->SetFont('dejavusans', '', 10, '', 'true');
            $string = "";
            foreach ($atendance_activity as $a){
                $string .= $a->activity . ': ' . $a->name . ' ' . $a->surname . '   ';
            }

            $pdf->Cell(200, 6, $string, 0, 1, 'L');
        }

        // Info o franchise
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

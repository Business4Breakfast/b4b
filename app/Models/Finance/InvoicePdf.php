<?php

namespace App\Models\Finance;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use TCPDF;

class InvoicePdf extends Model
{

    public function generatePdfInvoice($id, $output="I")
    {
        $inv = Invoice::findOrFail($id);

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetAutoPageBreak(false, 0);
        $pdf->SetTitle('title');

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //set auto page breaks
        $pdf->SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('dejavusans', '', 8, '', true);

        // set default font subsetting mode
        $pdf->setFontSubsetting(false);
        $pdf->AddPage();


        $bx = $by = 15;
        $pdf->SetLineWidth(0.5);
        $pdf->PolyLine(
            [
                15, 15,
                195, 15,
                195, 280,
                15, 280,
                15, 15,
            ]
        );
        $pdf->SetLineWidth(0.3);
        $pdf->Line(15, 34, 195, 34);

        // separator pred textom
        $pdf->SetLineWidth(0.2);
        $pdf->Line(19, 118, 191, 118);
        // separator za textom
        $pdf->Line(19, 170, 191, 170);
        // separator mezdi dod | odb.
        $pdf->Line(95, 37, 95, 115);
        // separator sumy zvisla
        $pdf->Line(120, 170, 120, 180);
        //separator sumy vodorovna
        $pdf->Line(19, 180, 191, 180);
        //separator sumy vodorovna 2x k uhrade
        $pdf->Line(19, 190, 191, 190);
        // separator sumy zvisla
        $pdf->Line(120, 190, 120, 200);
        $pdf->Line(120, 200, 191, 200);

        // folding mark
        $pdf->Line(195, 96, 196, 96);
        $pdf->Line(197, 96, 198, 96);
        $pdf->Line(14, 96, 15, 96);

        $pdf->Image(
            public_path('images/app/B4B_logo_alpha_invoice.png'), 20, 18, 0, 14, 'PNG', '', '', false, 150
        );

        $x = 90; // max 180
        $y = 0;
        $pdf->SetFont('dejavusans', '', 15, '', 'true');
        $pdf->SetXY($x + $bx, $y + $by);

        $txt_title = ($inv->proforma == 9) ? 'Zálohová faktúra: ' : 'Faktúra - Daňový doklad: ';

        $pdf->Cell(85, 20, $txt_title . $inv->variable_symbol, 0, 2, 'R');
        $pdf->SetFont('dejavusans', '', 10, '', 'true');

        // Dodávateľ: / Odberateľ:
        $x = 5; // max 180
        $y = 25;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(90, 5, 'Dodávateľ', 0, 0, 'L');
        $x = 85;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->Cell(85, 5, 'Odberateľ', 0, 0, 'L');

        // Dodávateľ:
        $x = 5;
        $y = 30;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('freesans', 'b', 10, '', 'true');
        $pdf->Cell(90, 5, $inv->suplier_company, 0, 0, 'L');

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(90, 5, $inv->suplier_address_street, 0, 0, 'L');

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(90, 5, $inv->suplier_address_psc . ' ' . $inv->suplier_address_city, 0, 0, 'L');

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(90, 5, $inv->suplier_address_country, 0, 0, 'L');


        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(90, 5, 'IČO: '.$inv->suplier_ico, 0, 0, 'L');

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(90, 5, 'DIČ: '.$inv->suplier_dic, 0, 0, 'L');

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(90, 5, 'IČ DPH: '.$inv->suplier_ic_dph, 0, 0, 'L');

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 8, '', 'true');
        $pdf->MultiCell(
            75, 20, 'Registrácia: ' . config('invoice.accounts.default.registration'), 0, 'L', 0, 0, '', '', true
        );

        $y += 9;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', 'b', 9, '', 'true');
        $pdf->Cell(90, 5, 'Účet: '. config('invoice.accounts.default.account'), 0, 0, 'L');

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', 'b', 9, '', 'true');
        $pdf->Cell(90, 5, 'Banka: '. config('invoice.accounts.default.bank'), 0, 0, 'L');


        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', 'b', 9, '', 'true');
        $pdf->Cell(90, 5, 'IBAN: '. config('invoice.accounts.default.iban'), 0, 0, 'L');

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', 'b', 9, '', 'true');
        $pdf->Cell(90, 5, 'SWIFT: '. config('invoice.accounts.default.swift'), 0, 0, 'L');

        // Odberateľ:

        $x = 85;
        $y = 30;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('freesans', 'b', 10, '', 'true');
        $pdf->MultiCell(80, 10, $inv->company_title, 0, 'L', false, 2);

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(90, 5, $inv->address_street, 0, 0, 'L');

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(90, 5, $inv->address_psc . ' ' . $inv->address_city, 0, 0, 'L');

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(90, 5, $inv->address_country , 0, 0, 'L');


        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(90, 5, 'IČO: '. $inv->ico, 0, 0, 'L');

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(90, 5, 'DIČ: '. $inv->dic, 0, 0, 'L');

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(90, 5, 'IČ DPH: '. $inv->ic_dph, 0, 0, 'L');

        $y += 7;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', 'b', 9, '', 'true');
        $pdf->Cell(90, 5, 'Variabilný symbol: '. $inv->variable_symbol, 0, 0, 'L');

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(90, 5, 'Konštantný symbol: 308', 0, 0, 'L');

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(90, 5, 'Spôsob platby: PP ', 0, 0, 'L');

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(
            90, 5, 'Dátum vystavenia: '. Carbon::createFromFormat('Y-m-d H:i:s', $inv->date_create)->format('d.m.Y'), 0, 0,
            'L'
        );

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(
            90, 5, 'Dátum dodania: '. Carbon::createFromFormat('Y-m-d H:i:s', $inv->date_delivery)->format('d.m.Y'), 0, 0,
            'L'
        );

        $y += 5;
        $pdf->setXY($x + $bx, $y + $by);
        $pdf->SetFont('dejavusans', 'b', 9, '', 'true');
        $pdf->Cell(
            90, 5, 'Dátum splatnosti: '. Carbon::createFromFormat('Y-m-d H:i:s', $inv->date_pay_to)->format('d.m.Y'), 0, 0,
            'L'
        );

        // Popis
        $pdf->setXY(20, 120);
        $pdf->SetFont('dejavusans', '', 10, '', 'true');
        $pdf->MultiCell(
            170, 40, $inv->description, 0, 'L', 0, 0, '', '', true
        );

        //ak je proforma
        if($inv->proforma == 1) {
            // bez dph
            $pdf->setXY(20, 172);
            $pdf->SetFont('dejavusans', '', 10, '', 'true');
            $pdf->Cell(50, 6, 'Cena bez DPH : ' . number_format((float)$inv->price, 2, ',', ' ') . ' €', 0, 0, 'L');

            // dph
            $pdf->setXY(75, 172);
            $pdf->SetFont('dejavusans', '', 10, '', 'true');
            $pdf->Cell(50, 6, 'DPH ' . config('invoice.setting.vat') . '%: ' . number_format((float)$inv->price_dph, 2, ',', ' ') . ' €', 0, 0, 'L');

            // Suma
            $pdf->setXY(125, 172);
            $pdf->SetFont('dejavusans', '', 10, '', 'true');
            $pdf->Cell(50, 6, 'Cena s DPH ', 0, 0, 'L');

        }  else {
            // Suma
            $pdf->setXY(125, 172);
            $pdf->SetFont('dejavusans', '', 10, '', 'true');
            $pdf->Cell(50, 6, 'Zálohová čiastka ', 0, 0, 'L');
        }

        $pdf->setXY(150, 172);
        $pdf->SetFont('dejavusans', '', 10, '', 'true');
        $pdf->Cell(
            40, 6, number_format((float) $inv->price_w_dph, 2, ',', ' ').' €', 0, 0,
            'R'
        );

        //ak je ostra a bola uhradena proforma fakturou
        if($inv->proforma == 9) {
            // Uhrada proforma faktúrou
//            $pdf->setXY(20, 182);
//            $pdf->SetFont('dejavusans', '', 10, '', 'true');
//            $pdf->Cell(50, 6, 'Uhradená zálohová faktúra č: ' . $inv->paid_description, 0, 0, 'L');
//
//            $pdf->setXY(150, 182);
//            $pdf->SetFont('dejavusans', '', 10, '', 'true');
//            $pdf->Cell(
//                40, 6, number_format((float)$inv->price_w_dph, 2, ',', ' ') . ' €', 0, 0, 'R');
        }

        // Celkom k úhrade
        $pdf->setXY(125, 192);
        $pdf->SetFont('dejavusans', '', 10, '', 'true');
        $pdf->Cell(50, 6, 'Celkom k úhrade', 0, 0, 'L');

        if($inv->status == 5) {

            $pdf->setXY(150, 192);
            $pdf->SetFont('dejavusans', 'B', 12, '', 'true');
            $pdf->Cell(40, 6, number_format((float) 0, 2, ',', ' ').' €', 0, 0, 'R');

            if($inv->reference == 0){

                $pdf->setXY(150, 192);
                $pdf->SetFont('dejavusans', 'B', 12, '', 'true');
                $pdf->Cell(40, 6, number_format((float) $inv->price_w_dph, 2, ',', ' ') .' €', 0, 0, 'R');

            }else{

                $pdf->setXY(150, 192);
                $pdf->SetFont('dejavusans', 'B', 12, '', 'true');
                $pdf->Cell(40, 6, number_format((float) 0, 2, ',', ' ').' €', 0, 0, 'R');

            }

        } else {

            $pdf->setXY(150, 192);
            $pdf->SetFont('dejavusans', 'B', 12, '', 'true');
            $pdf->Cell(40, 6, number_format((float) $inv->price_w_dph, 2, ',', ' ') .' €', 0, 0, 'R');
        }

        //Peciatka B4B_peciatka_do_faktury.png
        $pdf->Image(
            public_path('images/app/B4B_peciatka_do_faktury.png'), 145, 245, 45, 0, 'PNG', '', '', false, 150
        );


        // Vystavil
        $user = User::findOrFail($inv->user_id_create);
        $pdf->setXY(20, 264);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(80, 6, 'Faktúru vystavil:' . $user->full_name, 0, 0, 'L');
        $pdf->setXY(20, 269);
        $pdf->SetFont('dejavusans', '', 9, '', 'true');
        $pdf->Cell(
            80, 6, config('invoice.accounts.default.tel') . ',  ' .config('invoice.accounts.default.email') . ',  ' . config('invoice.accounts.default.web') , 0, 0,
            'L'
        );

        // Info o franchise
        $pdf->setXY(20, 273);
        $pdf->SetFont('dejavusans', '', 8, '', 'true');
        $pdf->Cell(50, 6, __('email.email_company_franchise'), 0, 0, 'L');

        //ak je zadany vystup S vratime string
        if(strcmp($output, 'S') == 0) {
            return $pdf->Output('Invoice_b4b_'. $inv->variable_symbol .'.pdf', 'S');
        }

        return $pdf->Output('Invoice_b4b_'. $inv->variable_symbol .'.pdf', 'I');

    }

}

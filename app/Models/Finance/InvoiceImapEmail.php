<?php

namespace App\Models\Finance;

use App\Models\Notification\EmailNotification;
use Illuminate\Database\Eloquent\Model;

class InvoiceImapEmail extends Model
{


    // citanie vypisov z emailu
    public function readImapMail()
    {

        $deleted = 0;
        $moved = 0;

        $hostname = '{' . config('imap.accounts.default.host') . ':' . config('imap.accounts.default.port') . '/imap/' .
                          config('imap.accounts.default.encryption'). '}INBOX';

        $username = config('imap.accounts.default.username');
        $password = config('imap.accounts.default.password');


        $invoice = new Invoice();
        $email_notification = new EmailNotification();


        $mailbox = imap_open( $hostname, $username, $password ) or die('Cannot connect to IMAP: ' . imap_last_error());

        $check = imap_check ( $mailbox ) or die ( 'Check Mail Box Error: ' .  imap_last_error () );

        //echo "Number of messages : " . $check->Nmsgs . "\r\n<br />\r\n";

        $view  = @imap_fetch_overview ( $mailbox, '1:' . $check->Nmsgs, 0 ) or die ( 'Count Messages Error: ' .  imap_last_error () );

        $size  = ( sizeof ( $view ) - 1 );

        $res = [];

        /* loop the box newest to oldest */
        for ( $x = $size; $x >= 0; $x-- )
        {

            $message = imap_fetchbody($mailbox, $view[$x]->uid,1,FT_UID );

            $payment = $invoice->parseEmailTatrabankaStatement($view[$x]->from, $view[$x]->subject, $message);

            if($payment){


                if(array_key_exists('variable_symbol', $payment)){


                    //najdeme fakturu podla variabilneho sybnolu
                    $invoice_to_pay = $invoice->getIdInvoiceFromVariableSymbol($payment['variable_symbol']);

                    //ak je neuhradena
                    if( $invoice_to_pay && $invoice_to_pay->status != 5){

                        // zapiseme uhrady
                        $pay['invoice_id'] = $invoice_to_pay->id;
                        $pay['amount'] = floatval($payment['amount_payment']);
                        $pay['date_payment'] = $payment['date_payment'];
                        $pay['description_payment'] = $payment['subject'];

                        $last_id = $invoice->addNewPaymentFromProformaInvoice($pay);

                        //dump($last_id);

                        if($last_id['id']){

                            // uhradime faktúru na zaklade vlozenej platby
                            $invoice->payInvoiceCheck($invoice_to_pay->id, $payment['subject']);

                            // uhradime membership ak bola faktura za memberhip
                            // TODO nastavime uhradu clenského, ak je cela suma na rok ak 1/2 tak len do splatnosti druhej faktury
                            $invoice->payMembershipFromPayInvoice($invoice_to_pay->id);

                            // ak je proforma vygenerujeme ostru fakturu
                            $new_invoice = $invoice->createInvoiceFromProforma($invoice_to_pay->id);

                            //ak bola vygenerovana nova ostra faktura z proforma faktury
                            if ($new_invoice) {

                                // vytvorime uhrady z proforma faktury do uhrad faktúr
                                $new_invoice['description_payment'] = $invoice_to_pay['variable_symbol'];
                                $invoice->addNewPaymentFromProformaInvoice($new_invoice);

                                // uhradime novu ostru faktúru na zaklade vlozenej platby
                                $invoice->payInvoiceCheck($new_invoice['invoice_id'], $invoice_to_pay['variable_symbol']);

                                // odosleme ostru fakturu emailom a posleme serial ak je faktura za členské
                                $email_notification->sendInvoiceMembershipUsers($new_invoice['invoice_id']);

                            }

                            //presunieme email do archivnych
                            @imap_mail_move($mailbox,$view[$x]->uid,"INBOX.Archive", CP_UID);
                            @imap_expunge ( $mailbox );
                            $moved++;

                        }

                    }

                }

            } else {
                //zmazeme emaily ktore nezodpovedaju platbam v subjekte
                @imap_delete ( $mailbox, $view[$x]->uid, FT_UID );
                @imap_expunge ( $mailbox );
                $deleted++;
            }

        }

        imap_close ( $mailbox );
        //echo "Deleted: " . $deleted . " message(s)\r\n<br />\r\n";

        echo now() . 'moved: ' . $moved ."\n";

        return;

    }

}
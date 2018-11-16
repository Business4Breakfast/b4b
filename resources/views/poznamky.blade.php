
mamp server
problem so statrtovanim db

If you cannot kill them with Activity Monitor, in Terminal do
$ ps aux | grep mysqld
The 2nd column is the process id, and you can stop it with (if it is 1234)
$ kill 1234






// najdenie jedneho produktu
$key = array_search( 12, array_column($industry, 'id'));
dump($industry[$key]);

kluby a manager
SELECT cu1.*, u.name, u.surname
FROM club_users cu1 LEFT JOIN club_users cu2
ON (cu1.user_function_id = cu2.user_function_id AND cu1.id < cu2.id)
LEFT JOIN users as u ON cu1.user_id = u.id
WHERE cu2.id IS NULL AND cu1.club_id = 24


SELECT m1.*
FROM club_users m1 LEFT JOIN club_users m2
ON (m1.user_function_id = m2.user_function_id AND m1.id < m2.id)
WHERE m2.id IS NULL AND m1.club_id = 24

convert font
http://www.xml-convert.com/ttftopdf/getfiles/id/80377/uniqueid/15441630455a7384a90e043


UPDATE events as eu
LEFT JOIN clubs as c ON c.id = eu.club_id

SET eu.address_street =  c.address_street,  eu.address_psc = c.address_psc,   eu.address_city = c.address_city,
eu.discrict_id = c.district_id, eu.county_id = c.county_id

MAIL_DRIVER=smtp
MAIL_HOST=smtp.crooce.com
MAIL_PORT=587
MAIL_USERNAME=statement@y9.sk
MAIL_PASSWORD=6876gGgjhhjns
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=statement@y9.sk
MAIL_FROM_NAME=statement@y9.sk


//setup before functions
var typingTimer;                //timer identifier
var doneTypingInterval = 5000;  //time in ms (5 seconds)

//on keyup, start the countdown
$('#dic').keyup(function(){
clearTimeout(typingTimer);
if ($('#dic').val()) {
typingTimer = setTimeout(doneTyping, doneTypingInterval);
}
});

$("#btn_dic").click(function(){
toastr.success('dic');
});

//user is "finished typing," do something
function doneTyping () {
//do something
toastr.success('dic')
}



SELECT ri.*, concat(g.name, ' ', g.surname) as user_from, g.id as user_from_id,
concat(g1.name, ' ', g1.surname) as user_from, g1.id as user_from_id, c.id as club_id

FROM reference_import as ri

LEFT JOIN guests as g ON g.user_cackon_id = ri.user_from
LEFT JOIN guests as g1 ON g1.user_cackon_id = ri.user_to
LEFT JOIN clubs as c ON ri.club_cackon_id = c.club_cackon_id





INSERT INTO `reference_import` (`reference_id`, `event_id`, `reference_type`, `user_to`, `description`, `user_from`, `value_1`, `active`, `club_id`, `club_text`, `date`, `zb`, `vb`)

SELECT ru.*, t.klub, k.oznaceni, t.termin, t.ZB, t.vB
FROM `referencni_ustrizky` as ru
LEFT JOIN terminy as t ON  t.id = ru.datum
LEFT JOIN kluby as k ON t.klub = k.id
WHERE k.master = 123


//$res = Collection::make($res);
//$sms = new SmsGateway('BforB.sk');
//$status = $sms->sendMessage('+421918410663', 'Hello, Mr.Bais Pleeease dont forget for BEER at eight o\'clock  in LIMI  :)');
//dump($sms);
//dump($status);



//skript na testovanie cronu po 6 sekundach
// spustit v console a ctr c vypnut
while true; do php artisan schedule:run; sleep 60; done


Banka: Tatra banka, a.s. Dátum vystavenia:17.10.2017
SWIFT: TATRSKBX Dátum splatnosti:17.10.2017
IBAN: SK12 1100 0000 0029 2390 0970 Dátum dodania:17.10.2017
Číslo účtu: 2923900970/1100
Označenie dodávky Množstvo J.cena Cena %DPH DPH Celkom EUR
Fakturujeme Vám za marketingové služby
na mítingoch Business for Breakfast za 1 660.00 20.00% 132.00 792.00
obdobie od 06.10.2017 do 14.10.2018
UHRADENÁ ZÁLOHA Faktúra č. 20179060 792.00
CELKOM K ÚHRADE 0.00
Vystavil: Ing. Juraj Bai


$oClient = new Client([
'host'          => 'imap.crooce.com',
'port'          => 143,
'encryption'    => 'tls',
'validate_cert' => true,
'username'      => 'statement@y9.sk',
'password'      => '6876gGgjhhjns',
]);


//Connect to the IMAP Server
$oClient->connect();

//Get all Mailboxes
$aMailboxes = $oClient->getFolders();

//dump($aMailboxes);

//Loop through every Mailbox
/** @var \Webklex\IMAP\Folder $oMailbox */
foreach($aMailboxes as $oMailbox){

//Get all Messages of the current Mailbox
/** @var \Webklex\IMAP\Message $oMessage */
foreach($oMailbox->getMessages() as $oMessage){


echo $oMessage->subject.'<br />';
//echo 'Attachments: '. $oMessage->getAttachments()->count();
echo $oMessage->getHTMLBody(true);
dump($oMessage->getTextBody(true));

$from = $oMessage->getFrom()[0]->mail;
$subject = $oMessage->subject;
$text = $oMessage->getTextBody(true);

$this->parseEmailTatrabankaStatement($from, $subject, $text);


if($oMessage->getUid() == 4){

dump($oMessage->getDate());
//$oMessage->delete();
$this->parseEmailTatrabankaStatement($from, $subject, $text);
}


var users = {!! json_encode(  $users->pluck('email','id') ) !!};



//Move the current Message to 'INBOX.read'
//                if($oMessage->moveToFolder('INBOX.Read') == true){
//                    echo 'Message has ben moved';
//                }else{
//                    echo 'Message could not be moved';
//                }
}

$oClient->disconnect();
}

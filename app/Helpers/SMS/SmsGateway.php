<?php

namespace App\Helpers\SMS;

class SmsGateway extends SmsService
{

    private $integrationId;
    private $integrationKey;
    private $receiveNumber = "0000000000";


    private $from;


    public function __construct($from = "Directreal")
    {
        $this->integrationId = config('services.smslab.integration_id');
        $this->integrationKey = config('services.smslab.integration_key');

        $this->from = $from;

        parent::__construct();
    }


    /**
     * Generate signature for sms sending
     *
     * @param string $number
     *
     * @return string $signature
     */
    private function generateSignature($number)
    {
        $plain = $this->integrationKey.$number;
        $hash = md5($plain);
        $signature = substr($hash, 10, 11);

        return $signature;
    }


    /**
     * Check telephone number format
     *
     * @param string $number
     *
     * @return boolean
     */
    private function checkNumberFormat($number)
    {

        if (strlen($number) == 0) {
            return false;
        }

        $match = preg_match("/^[+]?[0-9]{12}|09{1}[0-9]{8}$/", $number);

        return $match > 0;
    }


    /**
     * Check sender name format
     *
     * @param string $sender
     *
     * @return boolean
     */
    private function checkSender($sender)
    {
        return preg_match("/^[0-9a-zA-Z\. -]{1,14}$/", $sender) > 0;
    }


    /**
     * Send sms message
     *
     * @param string $number Recipient telephone number
     * @param string $message
     *
     * @throws Exception
     * @return number
     */
    public function sendMessage($number, $message)
    {

        if (!$this->checkNumberFormat($number)) {
            throw new \Exception("Bad number format");
        }

//		if(!$this->checkSender($this->from)) {
//			throw new Exception("Bad sender format");
//		}

        if (strlen($message) == 0) {
            throw new \Exception("Message empty");
        }

        if (strlen($message) > 160) {
            throw new \Exception("Message too long");
        }

        $signature = $this->generateSignature($number);

        $smsId = $this->sendSms(
            $this->integrationId, $signature, $this->from, $number, $message, true
        );

        return $smsId;

    }


    /**
     * Get sms status data from provider
     *
     * @param integer $id Sms message id
     *
     * @throws \Exception
     */
    public function getSmsStatus($id)
    {
        if ($id < 0) {
            throw new \Exception("Invalid sms id");
        }

        $result = $this->smsStatus($id);

        return $result;

    }

    public function test()
    {

        $integrationIdProd = $this->integrationId;
        $integrationKeyProd = $this->integrationKey;

        $this->integrationId = "1-TB672G";
        $this->integrationKey = "5^Af-8Ss";
        $number = "421949608102";
        $finalSignature = "32592c5457d";

        $calculatedSignature = $this->generateSignature($number);

        echo("Test signature: $calculatedSignature ".(strcmp(
                $finalSignature, $calculatedSignature
            ) == 0 ? "OK" : "FAIL")."<br/>");


        $this->integrationId = $integrationIdProd;
        $this->integrationKey = $integrationKeyProd;


        try {
            $id = $this->sendMessage("0949608102", "Toto je test sms Directreal");
            echo("Test send message: ".$id." ".($id > 0 ? "OK" : "FAIL")."<br/>");
        } catch (\Exception $e) {
            echo("Error Code ".$e->getCode().": ".$e->getMessage());
        }

    }

}

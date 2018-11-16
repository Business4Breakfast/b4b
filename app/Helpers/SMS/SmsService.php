<?php

namespace App\Helpers\SMS;

use Illuminate\Support\Facades\Log;

class SmsService
{
    private $client = null;


    /**
     * SMS Service provider
     */
    public function __construct()
    {
        $options = [
            'encoding' => 'UTF-8',
            'trace' => 1,
        ];

        try {
            $this->client = new \SoapClient(config('services.smslab.wsdl'), $options);
        } catch (\Exception $e) {
            Log::error('SMS service SOAP exception: '.$e->getMessage());
        }
    }


    /**
     * Check if the service is valid service object, ie. initialization succeed
     *
     * @return boolean True if service is ready to use
     */
    protected function isValid()
    {
        return $this->client != null;
    }


    /**
     * Convert value to boolean
     *
     * @param boolean $value
     */
    private function convertToBoolean($value)
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        } else if (is_int($value) || is_float($value)) {
            return $value > 0 ? 1 : 0;
        } else if (is_string($value)) {
            return strcasecmp($value, 'true') == 0 ? 1 : 0;
        } else {
            return 0;
        }
    }


    /**
     * Try to send sms message to given number
     *
     * @param string  $integrationId  Integration id from service provider
     * @param string  $signature      Signature
     * @param string  $from           From number or string
     * @param string  $to             Recipient phone number
     * @param string  $msg            Message
     * @param boolean $deliveryReport True to obtain delivery status from carrier
     *
     * @return integer Sms id, 0 otherwise
     * @throws \Exception
     */
    protected function sendSms($integrationId, $signature, $from, $to, $msg, $deliveryReport)
    {

        if (!$this->isValid()) {
            throw new \Exception('Client is not valid');
        }

        $params["integrationId"] = $integrationId;
        $params["signature"] = $signature;
        $params["from"] = $from;
        $params["to"] = $to;
        $params["msg"] = $msg;
        $params["deliveryReport"] = $this->convertToBoolean($deliveryReport);

        Log::info('SMS service sending: '.print_r($params, true));

        try {
            $result = $this->client->smsSendOne($params);
        } catch (\Exception $e) {
            Log::error('SMS service sending error: '.$e->getMessage());
            throw new \Exception($e->getMessage());
        }

        if ($result == null) {
            throw new \Exception('Empty result');
        }

        Log::info('SMS service sending result: '.print_r($result, true));

        if (isset($result->return)) {
            $data = $result->return;

            $smsId = 0;
            if (isset($data[0])) {
                $smsId = $data[0];
            }

            if (isset($data[1])) {
                $errCode = $data[1];
            }

            if (isset($data[2])) {
                $errDescription = $data[2];
            }

            if (strcasecmp($errCode, 'ok') != 0) {
                if (is_string($errDescription) == false && is_array(
                        $errDescription
                    )) {
                    $errDescription = print_r($errDescription, true);
                } else {
                    $errDescription = (string) $errDescription;
                }

                $errCode = (int) $errCode;
                throw new \Exception($errDescription, $errCode);
            }

            return $smsId;
        }

        if ($result == null) {
            throw new \Exception('Invalid result');
        }

    }


    /**
     * Get sms status
     *
     * @param string $smsId
     *
     * @return array Array with keys "sendStatus, sendStatusDescription, sendDate,
     *               receiveStatus, receiveStatusDescription, receiveDate"
     * @throw Exception
     */
    protected function smsStatus($smsId)
    {

        if (!$this->isValid()) {
            throw new \Exception('Client is not valid');
        }

        $params['smsId'] = $smsId;

        //$this->log('Sms status : '.print_r($params, true));

        $result = null;
        try {
            $result = $this->client->smsStatusOne($params);
        } catch (\Exception $e) {
            Log::error('SMS service status exception: '.$e->getMessage());
        }

        if ($result == null) {
            throw new \Exception('SMS Check result is null!');
        }

        Log::info('Sms service status result: '.print_r($result, true));


        $sendStatus = '';
        $sendStatusDescription = '';
        $sendDate = '';
        $receiveStatus = '';
        $receiveStatusDescription = '';
        $receiveDate = '';

        if (isset($result['return'])) {
            $data = $result['return'];

            if (isset($data[0])) {
                $sendStatus = $data[0];
            }

            if (isset($data[1])) {
                $sendStatusDescription = iconv('ISO-8859-2', 'UTF-8', $data[1]);
            }

            if (isset($data[2])) {
                $sendDate = $data[2];
            }

            if (isset($data[3])) {
                $receiveStatus = $data[3];
            }

            if (isset($data[4])) {
                $receiveStatusDescription = iconv('ISO-8859-2', 'UTF-8', $data[4]);
            }

            if (isset($data[5])) {
                $receiveDate = $data[5];
            }
        } else {
            throw new \Exception('Empty result');
        }

        $statusData = null;
        $statusData['sendStatus'] = $sendStatus;
        $statusData['sendStatusDescription'] = $sendStatusDescription;
        $statusData['sendDate'] = $sendDate;
        $statusData['receiveStatus'] = $receiveStatus;
        $statusData['receiveStatusDescription'] = $receiveStatusDescription;
        $statusData['receiveDate'] = $receiveDate;

        return $statusData;

    }


    /**
     * Receive sms from service provider queue
     *
     * @param string $integrationId
     * @param string $signature
     * @param string $from
     * @param array Array with keys "number, message, date"
     *
     * @throw Exception
     */
    protected function receiveSms($integrationId, $signature, $from)
    {

        if (!$this->isValid()) {
            throw new \Exception('Client is not valid');
        }

        $params['integrationId'] = "$integrationId";
        $params['signature'] = "$signature";
        $params['from'] = "$from";

        Log::info('SMS service received message: '.print_r($params, true));
        try {
            $result = $this->client->smsReceiveOne($params);
        } catch (\Exception $e) {
            Log::errro("SMS service Receive message exception: ".$e->getMessage());
            throw new \Exception($e->getMessage());
        }

        if ($result == null) {
            throw new \Exception('Received message is null');
        }

        Log::info("Receive message result : ".print_r($result, true));


        $error = "";
        $errorDescription = "";
        $number = "";
        $message = "";
        $date = "";

        if (isset($result["return"])) {
            $data = $result["return"];

            if (isset($data[0])) {
                $error = $data[0];
            }
            if (isset($data[1])) {
                $errorDescription = $data[1];
            }
            if (isset($data[2])) {
                $number = $data[2];
            }
            if (isset($data[3])) {
                $message = $data[3];
            }
            if (isset($data[4])) {
                $date = $data[4];
            }
        } else {
            throw new \Exception("Empty result");
        }


        $smsData = null;
        if (strcasecmp($error, "ok") != 0) {
            throw new \Exception($errorDescription, $error);
        } else {
            $smsData["number"] = $number;
            $smsData["message"] = $message;
            $smsData["date"] = $date;
        }

        return $smsData;

    }

}

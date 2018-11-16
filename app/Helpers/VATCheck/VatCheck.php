<?php

namespace App\Helpers\VATCheck;

class VatCheck
{
    private $client;


    public function __construct()
    {
        $this->client = new \SoapClient(config('services.vatcheck.wsdl'));
    }


    public function checkVAT($vatNo)
    {

        $response = new \stdClass();
        $response->valid = false;

        try {
            $args = [
                'countryCode' => substr($vatNo, 0, 2),
                'vatNumber'   => substr($vatNo, 2),
            ];
            $response = $this->client->checkVat($args);

        } catch (\SoapFault $exception) {
            $faults = [
                'INVALID_INPUT'       => 'The provided CountryCode is invalid or the VAT number is empty',
                'SERVICE_UNAVAILABLE' => 'The SOAP service is unavailable, try again later',
                'MS_UNAVAILABLE'      => 'The Member State service is unavailable, try again later or with another Member State',
                'TIMEOUT'             => 'The Member State service could not be reached in time, try again later or with another Member State',
                'SERVER_BUSY'         => 'The service cannot process your request. Try again later.',
            ];
            $error = $faults[$exception->faultstring];
            if (!isset($error)) {
                $error = $exception->faultstring;

                return json_encode(['valid' => false, 'error' => $error]);
            }
        }

        if (!$response->valid) {
            $error = "Not a valid VAT number";

            return json_encode(['valid' => false, 'error' => $error]);
        }

        return json_encode($response);
    }
}

<?php

namespace CoinSimple;

class Business
{
    const NEW_INVOICE_URL = "https://app.coinsimple.com/api/v1/invoice";

    protected $businessId;
    protected $apiKey;

    public function __construct($bId, $apiKey)
    {
        $this->businessId = $bId;
        $this->apiKey = $apiKey;
    }

    public function sendInvoice($invoice)
    {
        $timestamp = time();

        $options = $invoice->data();
        $options['timestamp'] = $timestamp;
        $options['hash'] = hash_hmac("sha256", $this->apiKey, $timestamp);
        $options['business_id'] = $this->businessId;

        return $this->httpSendInvoice($options);
    }

    public function validateHash($hash, $timestamp)
    {
        return $hash == hash_hmac("sha256", $this->apiKey, $timestamp);
    }

    protected function httpSendInvoice($options)
    {
        $curl = curl_init(self::NEW_INVOICE_URL);
        $data = json_encode($options);

        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        );

        curl_setopt($curl, CURLOPT_PORT, 443);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($curl);

        if ($response == false || curl_getinfo($curl, CURLINFO_HTTP_CODE) >= 400) {
            return array("status" => "error", "error" => "Error creating Invoice");
        }

        return json_decode($response);
    }
}

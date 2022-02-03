<?php

namespace App\Services;

use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;


class TraInvoiceService
{
    /**
     * URL for the website
     *
     * @var string
     */
    private $tin;
    private $certKey;
    private $RCTNUM = 1;
    private $GC;
    private $publicKey;
    private $certBase;
    private $username;
    private $password;
    private $routingKey;
    private $regID;

    private $xml_doc = "<?xml version='1.0' encoding='UTF-8'?>";
    private $efdms_open = "<EFDMS>";
    private $efdms_close = "</EFDMS>";
    private $efdms_signatureOpen = "<EFDMSSIGNATURE>";
    private $efdms_signatureClose = "</EFDMSSIGNATURE>";


    /**
     * Constructor
     *
     * @return void
     * @throws Exception
     */
    public function __construct()
    {
        $this->url = config('services.TRA.registerTestURL');
        $this->tin = config('services.TRA.TIN');
        $this->certKey = config('services.TRA.CertKey');
        $this->GC = $this->RCTNUM;
        $this->username = "babaadfj8490urjt";
        $this->password = "cG1pe1eH9q^WOT5=";
        $this->routingKey = "vfdrct";
        $this->regID = "TZ010055721";

        // Extract Client Public and Private Digital Signatures
        $path = storage_path() . '/' . 'app/public/vfdPergamon.pfx';

        $cert_store = file_get_contents($path);
        $clientSignature = openssl_pkcs12_read($cert_store, $cert_info, 'Peg@m0n9');
        $privateKey = $cert_info['pkey'];
        $this->publicKey = openssl_get_privatekey($privateKey);
        $this->certBase = base64_encode('69 c5 af 9d 61 81 34 94 44 b6 2a 39 ed 99 1e 3f');
    }


    /**
     * @throws Exception
     */
    public function Register()
    {
        //check if the user data exists


        // Compute Signature with SHA1
        $payloadData = "<REGDATA><TIN>$this->tin</TIN><CERTKEY>$this->certKey</CERTKEY></REGDATA>";
        $payloadDataSignature = $this->signPayloadPlain($payloadData);
        $signedMessageRegistration = $this->xml_doc . $this->efdms_open . $payloadData . $this->efdms_signatureOpen . $payloadDataSignature . $this->efdms_signatureClose . $this->efdms_close;
        Log::info($signedMessageRegistration);
        //send out the Registration Request

        // Send Request To TRA for Registration
        $urlReceipt = 'https://virtual.tra.go.tz/efdmsRctApi/api/vfdRegReq';
        $headers = array(
            'Content-type: application/xml',
            'Cert-Serial: ' . $this->certBase,
            'Client: WEBAPI'
        );

        $registrationACK = $this->sendRequest($urlReceipt, $headers, $signedMessageRegistration);
        Log::info($registrationACK);
        return new SimpleXMLElement($registrationACK);

    }


    /**
     * @throws Exception
     */
    public function getToken()
    {

        // $traToken = Session::get('TRA_token');
        //if (empty($traToken)) {
        $username = $this->username;
        $password = $this->password;
        $urlReceipt = 'https://virtual.tra.go.tz/efdmsRctApi/vfdtoken';
        $headers = '';
        $authenticationData = "username=$username&password=$password&grant_type=password";
        $tokenACKData = $this->sendRequest($urlReceipt, $headers, $authenticationData);
        Log::info($tokenACKData);
        $token = $tokenACKData['access_token'];

        session(['TRA_token' => $token]);

        return $token;
    }


    /**
     * @throws Exception
     */
    public function postInvoice(Transaction $transaction): array
    {

        $receiptNO = "L9V2PU" . $transaction->id;
        $transactionDate = getTransactionDate($transaction->id);
        $transactionTime = getTransactionTime($transaction->id);


        $token = $this->getToken();

        $payloadData = "<RCT>
<DATE>$transactionDate</DATE>
<TIME>$transactionTime</TIME>
<TIN>110781512</TIN>
<REGID>TZ010055721</REGID>
<EFDSERIAL>10TZ100359</EFDSERIAL>
<CUSTIDTYPE>1</CUSTIDTYPE>
<CUSTID>111222333</CUSTID>
<CUSTNAME>RichardKazimoto</CUSTNAME>
<MOBILENUM>0713655545</MOBILENUM>
<RCTNUM>$transaction->id</RCTNUM>
<DC>$transaction->id</DC>
<GC>$transaction->id</GC>
<ZNUM>getZnum()</ZNUM>
<RCTVNUM>$receiptNO</RCTVNUM>
<ITEMS>
<ITEM>
<ID>1</ID>
<DESC>Sponsorship deal to TRAFC</DESC>
<QTY>1</QTY>
<TAXCODE>1</TAXCODE>
<AMT>20000.01</AMT>
</ITEM>
</ITEMS>
<TOTALS>
<TOTALTAXEXCL>$transaction->total_value</TOTALTAXEXCL>
<TOTALTAXINCL>38000.0</TOTALTAXINCL>
<DISCOUNT>0.00</DISCOUNT>
</TOTALS>
<PAYMENTS>
<PMTTYPE>CASH</PMTTYPE>
<PMTAMOUNT>$transaction->total_value</PMTAMOUNT>

</PAYMENTS>
<VATTOTALS>
<VATRATE>A</VATRATE>
<NETTAMOUNT>$transaction->total_value</NETTAMOUNT>
<TAXAMOUNT>0.00</TAXAMOUNT>
</VATTOTALS>
</RCT>
";

        $payloadDataSignatureReceipt = $this->signPayloadPlain($payloadData);

        $signedMessageReceipt = $this->xml_doc . $this->efdms_open . $payloadData . $this->efdms_signatureOpen . $payloadDataSignatureReceipt . $this->efdms_signatureClose . $this->efdms_close;


        Log::info($signedMessageReceipt);

        $urlReceipt = 'https://virtual.tra.go.tz/efdmsRctApi/api/efdmsRctInfo';

        $headers = array(
            'Content-type: application/xml',
            'Routing-Key: ' . $this->routingKey,
            'Cert-Serial: ' . $this->certBase,
            'Client: WEBAPI',
            'Authorization: bearer ' . $token
        );

        $receiptACK = $this->sendRequest($urlReceipt, $headers, $signedMessageReceipt);


        $xmlACKReceipt = new SimpleXMLElement($receiptACK);
        $ackCodeReceipt = $xmlACKReceipt->RCTACK->ACKCODE;
        $ackReceiptMessage = $xmlACKReceipt->RCTACK->ACKMSG;


        $response["code"] = $ackCodeReceipt;
        $response["message"] = $ackReceiptMessage;

        return $response;


    }

    /**
     * Compute signature with SHA-256
     * @param $payload_data
     * @return string
     */
    function signPayloadPlain($payload_data): string
    {
        openssl_sign($payload_data, $signature, $this->publicKey, OPENSSL_ALGO_SHA1);
        return base64_encode($signature);
    }


    /**
     * Send a request to the given URL with the given headers and body
     * Send Signed Request to TRA
     * @param string $urlReceipt
     * @param  $headers
     * @param  $signedData
     * @return mixed
     * @throws Exception
     */
    function sendRequest(string $urlReceipt, $headers, $signedData)
    {
        $curl = curl_init($urlReceipt);
        if ($headers != '') {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        } else {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

        }
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $signedData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $resultEfd = curl_exec($curl);

        if ($headers == '') {
            $resultEfd = json_decode($resultEfd, true);
        }
        if (curl_errno($curl)) {
            throw new Exception(curl_error($curl));
        }
        curl_close($curl);
        return $resultEfd;
    }


}

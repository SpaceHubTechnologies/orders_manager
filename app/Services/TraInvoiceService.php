<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;


class TraInvoiceService
{
    /**
     * URL for the website
     *
     * @var string
     */
    private $url;
    private $tin;
    private $certKey;
    private $RCTNUM = 1;
    private $GC;
    private $publicKey;
    private $certBase;

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

        // Extract Client Public and Private Digital Signatures
        $cert_store = file_get_contents('vfdClient.pfx');
        $clientSignature = openssl_pkcs12_read($cert_store, $cert_info, 'Password');
        $privateKey = $cert_info['pkey'];
        $this->publicKey = openssl_get_privatekey($privateKey);
        $this->certBase = base64_encode('15 70 1e 15 39 94 7e ab 46 1f 0c f1 33 bc ac c9');
    }


    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function Register(): SimpleXMLElement
    {
        //check if the user data exists


        // Compute Signature with SHA1
        $payloadData = "<REGDATA><TIN>$this->tin</TIN><CERTKEY>$this->certKey</CERTKEY></REGDATA>";
        $payloadDataSignature = $this->signPayloadPlain($payloadData, $this->publicKey);
        $signedMessageRegistration = $this->xml_doc . $this->efdms_open . $payloadData . $this->efdms_signatureOpen . $payloadDataSignature . $this->efdms_signatureClose . $this->efdms_close;

        //send out the Registration Request
        $client = new Client();
        $registrationACK = $client->request('POST', $this->url, [
            'headers' => [
                'Content-type: application/xml',
                'Cert-Serial: ' . $this->certBase,
                'Client: WEBAPI'
            ],
            'body' => $signedMessageRegistration
        ]);
        return new SimpleXMLElement($registrationACK);

    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function getToken()
    {
        //check if the token is expired or empty

        //check if the registration was a success

        //if expired  register again
        $xmlACKRegistration = $this->register();

        //if all false generate  the token
        $ackCode = $xmlACKRegistration->EFDMSRESP->ACKCODE;
        /* 0 = Response Code for Successful Registration */
        if ($ackCode == 0) {
            $username = $xmlACKRegistration->EFDMSRESP->USERNAME;
            $password = $xmlACKRegistration->EFDMSRESP->PASSWORD;
            $routingKey = $xmlACKRegistration->EFDMSRESP->ROUTINGKEY;
            $registrationID = $xmlACKRegistration->EFDMSRESP->REGID;
            $receiptCode = $xmlACKRegistration->EFDMSRESP->RECEIPTCODE;
            $UIN = $xmlACKRegistration->EFDMSRESP->UIN;
            $urlReceipt = 'https://virtual.tra.go.tz/efdmsRctApi/vfdtoken';
            $headers = '';
            $authenticationData = "username=$username&password=$password&grant_type=password";

            Log::info($authenticationData);

            $tokenACKData = $this->sendRequest($urlReceipt, $headers, $authenticationData);
            //save this token in the session

            $token = $tokenACKData['access_token'];

            //post the invoice
            $response = $this->postInvoice($receiptCode, $routingKey,);

        } else {
            $ackMsg = $xmlACKRegistration->EFDMSRESP->ACKMSG;
            return 'Error ' . $ackMsg;
        }
        return $response;
    }


    /**
     * @throws Exception|GuzzleException
     */
    public function postInvoice($receiptCode, $routingKey): array
    {


        $token = $this->getToken();
        $RCTVNUM = $receiptCode . $this->GC;
        $payloadData = "<RCT><DATE>2019- 09 -25</DATE><TIME>11:38:00</TIME><TIN>111111111</TIN><REGID>TZ090055567</REGID><EFDSERIAL>10TZ999999</EFDSERIAL><CUSTIDTYPE>1</CUSTIDTYPE><CUSTID>111222333</CUSTID><CUSTNAME>RichardKazimoto</CUSTNAME><MOBILENUM>0713655545</MOBILENUM><RCTNUM>1</RCTNUM><DC>1</DC><GC>1</GC><ZNUM>20190625</ZNUM><RCTVNUM>GU72D81</RCTVNUM><ITEMS><ITEM><ID>1</ID><DESC>Sponsorship deal to TRAFC</DESC><QTY>1</QTY><TAXCODE>1</TAXCODE><AMT>20000.01</AMT></ITEM></ITEMS><TOTALS><TOTALTAXEXCL>18000.00</TOTALTAXEXCL><TOTALTAXINCL>38000.0</TOTALTAXINCL><DISCOUNT>0.00</DISCOUNT></TOTALS><PAYMENTS><PMTTYPE>CASH</PMTTYPE><PMTAMOUNT>50000.00</PMTAMOUNT><PMTTYPE>CHEQUE</PMTTYPE><PMTAMOUNT>100000.00</PMTAMOUNT><PMTTYPE>CCARD</PMTTYPE><PMTAMOUNT>68000.00</PMTAMOUNT><PMTTYPE>EMONEY</PMTTYPE><PMTAMOUNT>0.00</PMTAMOUNT></PAYMENTS><VATTOTALS><VATRATE>A</VATRATE><NETTAMOUNT>100000.00</NETTAMOUNT><TAXAMOUNT>16500.00</TAXAMOUNT><VATRATE>B</VATRATE><NETTAMOUNT>100000.00</NETTAMOUNT><TAXAMOUNT>0.00</TAXAMOUNT><VATRATE>C</VATRATE><NETTAMOUNT>100000.00</NETTAMOUNT><TAXAMOUNT>0.00</TAXAMOUNT></VATTOTALS></RCT>";

        $payloadDataSignatureReceipt = $this->signPayloadPlain($payloadData, $this->publicKey);
        $signedMessageReceipt = $this->efdms_open . $this->efdms_open . $payloadData . $this->efdms_signatureOpen . $payloadDataSignatureReceipt . $this->efdms_signatureClose . $this->efdms_close;

        $urlReceipt = 'https://virtual.tra.go.tz/efdmsRctApi/api/efdmsRctInfo';

        $headers = array(
            'Content-type: application/xml',
            'Routing-Key: ' . $routingKey,
            'Cert-Serial: ' . $this->certBase,
            'Client: WEBAPI',
            'Authorization: Bearer ' . $token
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
     * @param $publicKey
     * @return string
     */
    function signPayloadPlain($payload_data, $publicKey): string
    {
        openssl_sign($payload_data, $signature, $publicKey, OPENSSL_ALGO_SHA1);
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

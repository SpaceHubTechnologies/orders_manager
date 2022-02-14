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
    private $efd_serial;

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
        $this->GC = 02;
        $this->username = "babaahaf8490uanx";
        $this->password = "Via(S3Ej0h3bjog[";
        $this->routingKey = "vfdrct";
        $this->regID = "TZ0100554863";
        $this->efd_serial="10TZ100705";

        // Extract Client Public and Private Digital Signatures
        $path = storage_path() . '/' . 'app/public/VFD_PERGAMON_10TZ100705.pfx';

        $cert_store = file_get_contents($path);
        $clientSignature = openssl_pkcs12_read($cert_store, $cert_info, 'P3gAmon');
        $privateKey = $cert_info['pkey'];
        $this->publicKey = openssl_get_privatekey($privateKey);
        $this->certBase = base64_encode('51 37 8D 93 AC BF 82 86 49 94 AB 8D EB 75 93 26');
    }


    /**
     * @throws Exception
     */
    public function Register()
    {

        $payloadData = "<REGDATA><TIN>$this->tin</TIN><CERTKEY>$this->certKey</CERTKEY></REGDATA>";
        $payloadDataSignature = $this->signPayloadPlain($payloadData);
        $signedMessageRegistration = $this->xml_doc . $this->efdms_open . $payloadData . $this->efdms_signatureOpen . $payloadDataSignature . $this->efdms_signatureClose . $this->efdms_close;
        //send out the Registration Request

        // Send Request To TRA for Registration
        $urlReceipt = 'https://virtual.tra.go.tz/efdmsRctApi/api/vfdRegReq';
        $headers = array(
            'Content-type: application/xml',
            'Cert-Serial: ' . $this->certBase,
            'Client: WEBAPI'
        );

        $registrationACK = $this->sendRequest($urlReceipt, $headers, $signedMessageRegistration);
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

        $token = $tokenACKData['access_token'];

        session(['TRA_token' => $token]);

        return $token;
    }


    /**
     * @throws Exception
     */
    public function postInvoice(Transaction $transaction): array
    {

        $receiptNO = "B1E489" . $this->GC;
        $transactionDate = getTransactionDate($transaction->id);
        $transactionTime = getTransactionTime($transaction->id);

        $token = $this->getToken();

       $payloadData = "<RCT><DATE>$transactionDate</DATE><TIME>$transactionTime</TIME><TIN>$this->tin</TIN><REGID>$this->regID</REGID><EFDSERIAL>$this->efd_serial</EFDSERIAL><CUSTIDTYPE>6</CUSTIDTYPE><CUSTID></CUSTID><CUSTNAME>tarimo shop manyanya mtambani</CUSTNAME><MOBILENUM></MOBILENUM><RCTNUM>1</RCTNUM><DC>1</DC><GC>2</GC><ZNUM>20210930</ZNUM><RCTVNUM>$receiptNO</RCTVNUM><ITEMS><ITEM><ID>609d20795e866461ac9a6563</ID><DESC>Mayonaise 12x946ml</DESC><QTY>11</QTY><TAXCODE>1</TAXCODE><AMT>602272.00</AMT></ITEM><ITEM><ID>609d20795e866461ac9a65af</ID><DESC>Peanut Butter 6x800gms</DESC><QTY>2</QTY><TAXCODE>1</TAXCODE><AMT>47011.20</AMT></ITEM><ITEM><ID>609d20795e866461affc9a6569</ID><DESC>Tomato sauce  Bei Bomba  5kgs</DESC><QTY>200</QTY><TAXCODE>1</TAXCODE><AMT>850780.00</AMT></ITEM></ITEMS><TOTALS><TOTALTAXEXCL>1271240.00</TOTALTAXEXCL><TOTALTAXINCL>1500063.2</TOTALTAXINCL><DISCOUNT>0.0</DISCOUNT></TOTALS><PAYMENTS><PMTTYPE>CASH</PMTTYPE><PMTAMOUNT>1500063.2</PMTAMOUNT></PAYMENTS><VATTOTALS><VATRATE>A</VATRATE><NETTAMOUNT>1271240.00</NETTAMOUNT><TAXAMOUNT>228823.20</TAXAMOUNT></VATTOTALS></RCT>";
        $payloadDataSignatureReceipt = $this->signPayloadPlain($payloadData);
        $signedMessageReceipt = $this->xml_doc . $this->efdms_open . $payloadData . $this->efdms_signatureOpen . $payloadDataSignatureReceipt . $this->efdms_signatureClose . $this->efdms_close;


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

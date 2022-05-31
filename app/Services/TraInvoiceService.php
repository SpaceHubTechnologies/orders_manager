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
        $this->efd_serial = "10TZ100705";

        // Extract Client Public and Private Digital Signatures
        $path = storage_path() . '/' . 'files/VFD_PERGAMON_10TZ100705.pfx';

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
    public function getToken($customerID)
    {
        $username = $this->username;
        $password = $this->password;
        $urlReceipt = 'https://virtual.tra.go.tz/efdmsRctApi/vfdtoken';
        $headers = '';
        $authenticationData = "username=$username&password=$password&grant_type=password";
        $tokenACKData = $this->sendRequest($urlReceipt, $headers, $authenticationData);


        if (in_array('invalid_grant', $tokenACKData, true)) {
            if (isset($tokenACKData['ACKCODE'])) {
                $payload = $tokenACKData['ACKCODE'];
                handleCommand($payload, $customerID);
            }

            return response()->json('Something went wrong please try again later', 500);

        }

        $token = $tokenACKData['access_token'];

        session(['TRA_token' => $token]);

        return $token;


    }


    /**
     * @throws Exception
     */
    public function postInvoice(Transaction $transaction)
    {

        $receiptNO = "B1E489" . $this->GC;
        $transactionDate = getTransactionDate($transaction->id);
        $transactionTime = getTransactionTime($transaction->id);

        $token = $this->getToken($transaction->customer_id);

        Log::info($token);
        Log::info("Here is the token");
        if ($token) {

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

            Log::info(print_r($ackReceiptMessage, true));

            $response["code"] = $ackCodeReceipt;
            $response["message"] = $ackReceiptMessage;

            return $response;
        }
        return response()->json("Something went wrong while posting Receipt, please try again", 500);


    }

    /**
     * @throws Exception
     */
    public function postZReport(Transaction $transaction)
    {
        $transactionDate = getTransactionDate($transaction->id);
        $transactionTime = getTransactionTime($transaction->id);
        $znumber = getZnum($transaction->id);


        $token = $this->getToken($transaction->id);

        $z_report = "<ZREPORT><DATE>$transactionDate</DATE><TIME>$transactionTime</TIME><HEADER><LINE>PERGAMON GROUP LIMITED</LINE><LINE>MWAIKIBAKI RD MIKOCHENI OPP MWINYI RESIDENCE</LINE><LINE>TEL NO:+255 716684002</LINE><LINE>DAR ES SALAAM,TANZANIA</LINE></HEADER><VRN>40006908G</VRN><TIN>$this->tin</TIN><TAXOFFICE>Kinondoni</TAXOFFICE><REGID>$this->regID</REGID><ZNUMBER>$znumber</ZNUMBER><EFDSERIAL>$this->efd_serial</EFDSERIAL><REGISTRATIONDATE>2022-02-03</REGISTRATIONDATE><USER>09VFDWEBAPI-10131758711078151210TZ100705</USER><SIMIMSI>WEBAPI</SIMIMSI><TOTALS><DAILYTOTALAMOUNT>2143250.00</DAILYTOTALAMOUNT><GROSS>513880841.00</GROSS><CORRECTIONS>0.00</CORRECTIONS><DISCOUNTS>0.00</DISCOUNTS><SURCHARGES>0.00</SURCHARGES><TICKETSVOID>0</TICKETSVOID><TICKETSVOIDTOTAL>0.00</TICKETSVOIDTOTAL><TICKETSFISCAL>36</TICKETSFISCAL><TICKETSNONFISCAL>6</TICKETSNONFISCAL></TOTALS><VATTOTALS><VATRATE>A-18.00</VATRATE><NETTAMOUNT>1816313.55</NETTAMOUNT><TAXAMOUNT>326936.45</TAXAMOUNT><VATRATE>B-0.00</VATRATE><NETTAMOUNT>0.00</NETTAMOUNT><TAXAMOUNT>0.00</TAXAMOUNT><VATRATE>C-0.00</VATRATE><NETTAMOUNT>0.00</NETTAMOUNT><TAXAMOUNT>0.00</TAXAMOUNT><VATRATE>D-0.00</VATRATE><NETTAMOUNT>0.00</NETTAMOUNT><TAXAMOUNT>0.00</TAXAMOUNT><VATRATE>E-0.00</VATRATE><NETTAMOUNT>0.00</NETTAMOUNT><TAXAMOUNT>0.00</TAXAMOUNT></VATTOTALS><PAYMENTS><PMTTYPE>CASH</PMTTYPE><PMTAMOUNT>2143250.00</PMTAMOUNT><PMTTYPE>CHEQUE</PMTTYPE><PMTAMOUNT>0.00</PMTAMOUNT><PMTTYPE>CCARD</PMTTYPE><PMTAMOUNT>0.00</PMTAMOUNT><PMTTYPE>EMONEY</PMTTYPE><PMTAMOUNT>0.00</PMTAMOUNT><PMTTYPE>INVOICE</PMTTYPE><PMTAMOUNT>0.00</PMTAMOUNT></PAYMENTS><CHANGES><VATCHANGENUM>0</VATCHANGENUM><HEADCHANGENUM>0</HEADCHANGENUM></CHANGES><ERRORS></ERRORS><FWVERSION>3.0</FWVERSION><FWCHECKSUM>WEBAPI</FWCHECKSUM></ZREPORT>";

        $payloadDataZReport = $this->signPayloadPlain($z_report);
        $signedMessageZReport = $this->xml_doc . $this->efdms_open . $z_report . $this->efdms_signatureOpen . $payloadDataZReport . $this->efdms_signatureClose . $this->efdms_close;


        $urlZReport = 'https://virtual.tra.go.tz/efdmsRctApi/api/efdmszreport';

        $headers = array(
            'Content-type: application/xml',
            'Routing-Key: vfdzreport',
            'Cert-Serial: ' . $this->certBase,
            'Client: WEBAPI',
            'Authorization: bearer ' . $token
        );

        return $this->sendRequest($urlZReport, $headers, $signedMessageZReport);
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


    public function blockReceipt()
    {
        /*The aim for this command is to disable the system and stop it from issuing receipt and also display to user why system is blocked (Message from TRA)*/

    }

    public function unBlockReceipt()
    {
        /*If System was blocked as above this command will unblock it and allow it to issue receipts*/
    }

    public function RctVCode()
    {
        /*This command changes QR Code sequence i.e if it was 73281C TRA may change it to 54683D*/
    }

    public function EnableVat()
    {
        //  if a trader is not registered for VAT (Not allowed to charge VAT)
        // TRA may issue this command to allow him start charging VAT on items and their receipt/Zreport will display VRN number in the header (Instead of Not Registered)
    }

    public function DisAbleVat()
    {
        /* This command disable system (trader) from charging VAT in items sold and removes the VRN no from the receipt and Z Report header (Displays Not Registered)*/
    }
}



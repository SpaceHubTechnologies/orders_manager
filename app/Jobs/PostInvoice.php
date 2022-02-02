<?php

namespace App\Jobs;

use App\Models\Transaction;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class PostInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transaction;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     *
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function handle()
    {

        $endPoint = "";




            if ($this->transaction != null) {
                //send the Alert
                $client = new Client();

                $timestamps = date('H:i:s', $this->transaction->created_at);
                $regId= "&*&*&&(()(()";


                $xml = "<?xml version='1.0' encoding='utf-8'?>
         <EFDMS>
        <RCT>
        <DATE>{{$this->transaction->date_sale}}</DATE>
        <TIME>{{$timestamps}}</TIME>
        <TIN>{{env('TRA_TIN')}}</TIN>
        <REGID>{{$regId}}</REGID>
        <EFDSERIAL>01TZ000001</EFDSERIAL>
        <CUSTIDTYPE>6</CUSTIDTYPE>
       <RCTNUM>1</RCTNUM>
       <DC>9</DC>
       <GC>1098</GC>
       <ZNUM>100</ZNUM>
       <RCTVNUM>AAAA119</RCTVNUM>

     <TOTALS>
      <TOTALTAXEXCL>{{$this->transaction->total_paid}}</TOTALTAXEXCL>
      <TOTALTAXINCL>{{$this->transaction->total_value}}</TOTALTAXINCL>
      <DISCOUNT>0.00</DISCOUNT>
   </TOTALS>
   <PAYMENTS>
     <PMTTYPE>CASH</PMTTYPE>
     <PMTAMOUNT>50000.00</PMTAMOUNT>
     <PMTTYPE>CHEQUE</PMTTYPE>
     <PMTAMOUNT>100000.00</PMTAMOUNT>
     <PMTTYPE>CCARD</PMTTYPE>
     <PMTAMOUNT>68000.00</PMTAMOUNT>
     <PMTTYPE> EMONEY </PMTTYPE>
     <PMTAMOUNT>68000.00</PMTAMOUNT>
   </PAYMENTS>
   <VATTOTALS>
    <VATRATE>A</VATRATE>
    <NETTAMOUNT>100000.00</NETTAMOUNT>
    <TAXAMOUNT>16500.00</TAXAMOUNT>
    <VATRATE>B</VATRATE>
    <NETTAMOUNT>100000.00</NETTAMOUNT>
    <TAXAMOUNT>0.00</TAXAMOUNT>
    <VATRATE>C</VATRATE>
    <NETTAMOUNT>100000.00</NETTAMOUNT>
   <TAXAMOUNT>0.00</TAXAMOUNT>
</VATTOTALS>
</RCT>
       </EFDMS> ";

                return $client->request('POST', $endPoint, [
                    'headers' => [
                        'Content-Type' => 'Application/xml',
                        'Cert-Serial' => '',
                    ],
                    'body' => $xml
                ]);
            }

    }
}

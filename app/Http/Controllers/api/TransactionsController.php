<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostTransactionRequest;
use App\Jobs\PostInvoice;
use App\Jobs\PostReceiptJob;
use App\Models\CommandManager;
use App\Models\Transaction;
use App\Services\TraInvoiceService;
use App\Transformers\Json;
use App\Transformers\TransactionTransformer;
use App\Transformers\UserTransformer;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Fractalistic\ArraySerializer;

class TransactionsController extends Controller
{

    /**
     * Check Customer Status
     * @throws Exception
     */
    public function init(Request $request): JsonResponse
    {
        $customerID = $request->customer_id;
        (new TraInvoiceService())->getToken($customerID);

        $commandStatus = CommandManager::whereCustomerId($customerID)->first();
        $response = [];

        if (!empty($commandStatus)) {
            if ($commandStatus->is_blocked) {

                $response['code'] = 500;
                $response['message'] = "Customer is Blocked";
                $response['details'] = $commandStatus->block_reason;
            }

            if (!$commandStatus->is_vat_enabled) {
                $response['code'] = 500;
                $response['message'] = "VAT is Disabled";
            }

            if (!$commandStatus->change_qr_code) {
                $response['code'] = 500;
                $response['message'] = "Change QR CODE";
                $response['details'] = $commandStatus->new_qr_code;
            }
        }


        $response['code'] = 200;
        $response['message'] = "Ok";

        return response()->json($response);


    }

    /**
     * @throws Exception
     */
    public function createTransaction(PostTransactionRequest $request)
    {

        $customer_id = $request->customer_id;
        $code_sale_master = $request->code_sale_master;
        $status = $request->status;
        $date_sale = $request->date_sale;
        $last_update = $request->last_update;
        $payment_method = $request->payment_method;
        $total_value = $request->total_value;
        $total_paid = $request->total_paid;
        $sale_type = $request->sale_type;
        $description = $request->description;

        //if true create the transaction

        DB::beginTransaction();
        $transaction = new Transaction();
        $transaction->customer_id = $customer_id;
        $transaction->code_sale_master = $code_sale_master;
        $transaction->status = $status;
        $transaction->reference = generate_transaction_reference();
        $transaction->date_sale = $date_sale;
        $transaction->last_update = $last_update;
        $transaction->payment_method = $payment_method;
        $transaction->total_value = $total_value;
        $transaction->total_paid = $total_paid;
        $transaction->sale_type = $sale_type;
        $transaction->description = $description;
        $transaction->save();

        DB::commit();

        $transactionRef = $transaction->reference;
        if ($transactionRef) {
            dispatch(new PostReceiptJob($transaction));
            $response = [
                'error' => false,
                'message' => 'Transaction Created Successfully',
                'transaction' => fractal()
                    ->item($transaction, new TransactionTransformer())
                    ->serializeWith(new ArraySerializer())
            ];


            return response()->json($response, 200, [], JSON_PRETTY_PRINT);
        }

        return response()->json(Json::response(true, 'OOps ! Something went wrong please try again'), 400);

    }


}

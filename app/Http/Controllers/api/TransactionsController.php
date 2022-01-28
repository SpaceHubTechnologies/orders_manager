<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostTransactionRequest;
use App\Models\Transaction;
use App\Transformers\Json;
use App\Transformers\TransactionTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Serializer\ArraySerializer;

class TransactionsController extends Controller
{
    public function createTransaction(PostTransactionRequest $request): \Illuminate\Http\JsonResponse
    {
        //check if user is logged in

        //check the payment API response
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
            //on creating the transaction send the payload to TRA

            //success post to KRA return Response
            $includes = ['customer'];
            $response = [
                'error' => false,
                'message' => 'Transaction Created Successfully',
                'transaction' => fractal()
                    ->item($transaction, new TransactionTransformer())
                    ->parseIncludes($includes)
                    ->serializeWith(new ArraySerializer())
            ];

            return response()->json($response, 200, [], JSON_PRETTY_PRINT);
        } else {
            return response()->json(Json::response(true, 'OOps ! Something went wrong please try again'), 400);
        }

    }


}

<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Transformers\Json;
use App\Transformers\TransactionTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Serializer\ArraySerializer;

class TransactionsController extends Controller
{
    public function createTransaction(Request $request)
    {
        //check if user is logged in

        //check the payment API response
        $customer_id = 1;
        $amount = $request->amount;
        $gateway_status = $request->payment_status;

        //if true create the transaction

        DB::beginTransaction();
        $transaction = new Transaction();
        $transaction->user_id = $customer_id;
        $transaction->amount = $amount;
        $transaction->charge = 0.00;
        $transaction->reference = generate_transaction_reference();
        $transaction->comments = 'Simple payment Test';
        $transaction->type = 'credit';
        $transaction->payment_gateway = 'm-pesa';
        $transaction->save();

        DB::commit();

        $transactionRef = $transaction->reference;
        if ($transactionRef) {
            //on creating the transaction send the payload to TRA

            $includes = ['customer'];

            $response = [
                'error' => false,
                'message' => 'Transaction Created Successfully',
                'transaction' => fractal()
                    ->item($transaction, new TransactionTransformer())
                    //  ->parseIncludes($includes)
                    ->serializeWith(new ArraySerializer())
            ];

            return response()->json($response, 200, [], JSON_PRETTY_PRINT);
        } else {
            return response()->json(Json::response(true, 'Something went wrong please try again'), 400);
        }

    }
}

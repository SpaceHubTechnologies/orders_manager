<?php

use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

if (!function_exists('generate_transaction_reference')) {
    /**
     * Generate user account no
     * @return string
     */
    function generate_transaction_reference(): string
    {
        $number = Str::random(10) . mt_rand(1000000, 9999999);
        if (transaction_code_exist($number)) {
            return generate_transaction_reference();
        }

        return $number;
    }

}

if (!function_exists('transaction_code_exist')) {
    /**
     * Checks if the transaction_code already exists
     * @param $txn_code
     * @return bool
     */
    function transaction_code_exist($txn_code): bool
    {
        return Transaction::whereReference($txn_code)->exists();
    }

}

if (!function_exists('getTransactionTime')) {
    function getTransactionTime($transactionID): string
    {
        $transaction = Transaction::whereId($transactionID)->first();
        $time = $transaction->created_at;
        return $time->format('H:i:s');

    }
}

if (!function_exists('getTransactionDate')) {
    function getTransactionDate($transactionID): string
    {
        $transaction = Transaction::whereId($transactionID)->first();
        $date = $transaction->created_at;
        return $date->format('Y-m-d');

    }
}

if (!function_exists('generateRCTNo')) {
    function generateRCTNo($transactionID): string
    {
        $transaction = Transaction::whereId($transactionID)->first();
        return $transaction->created_at->format('Y:m:d');

    }
}
if (!function_exists('getDailyCounter')) {
    function getDailyCounter(Transaction $transaction)
    {

    }
}

if (!function_exists('getZnum')) {
    function getZnum($transactionID)
    {
        $transaction = Transaction::whereId($transactionID)->first();

      /*  $year = $transaction->created_at->year;
        $day = date('d', $transaction->created_at);*/

        return 20220215;

    }
}

if (!function_exists('generateRecieptNo')) {
    function generateRecieptNo()
    {


    }
}




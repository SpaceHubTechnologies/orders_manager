<?php

use App\Models\Transaction;
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



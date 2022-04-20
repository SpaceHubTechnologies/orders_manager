<?php

use App\Models\CommandManager;
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
    /**
     * Get Transaction Time
     * @param $transactionID
     * @return string
     */
    function getTransactionTime($transactionID): string
    {
        $transaction = Transaction::whereId($transactionID)->first();
        $time = $transaction->created_at;
        return $time->format('H:i:s');

    }
}

if (!function_exists('getTransactionDate')) {
    /**
     * Get Transaction Date
     * @param $transactionID
     * @return string
     */
    function getTransactionDate($transactionID): string
    {
        $transaction = Transaction::whereId($transactionID)->first();
        $date = $transaction->created_at;
        return $date->format('Y-m-d');

    }
}

if (!function_exists('generateRCTNo')) {
    /**
     * Generate receipt No
     * @param $transactionID
     * @return string
     */
    function generateRCTNo($transactionID): string
    {
        $transaction = Transaction::whereId($transactionID)->first();
        return $transaction->created_at->format('Y:m:d');

    }
}

if (!function_exists('handleCommand')) {
    /**
     * Handle commands
     * @param $payload
     * @return void
     */
    function handleCommand($payload)
    {
        if ($payload['command'] === 'BLOCK') {

            $commandManager = new CommandManager();
            $commandManager->customer_id = $payload['customer_id'];
            $commandManager->block_receipt = true;
            $commandManager->block_reason = $payload['message'];
            $commandManager->save();
        }
        //unblock the user
        if ($payload['command'] === 'UNBLOCK') {

            $customerId = $payload['customer_id'];

            $userCommand = CommandManager::whereCustomerId($customerId)->first();
            if ($userCommand) {
                $userCommand->block_receipt = false;
                $userCommand->block_reason = null;
                $userCommand->update();
            }

        }
        //enable VAT

        if ($payload['command'] === 'ENABLEVAT') {

            $commandManager = new CommandManager();
            $commandManager->customer_id = $payload['customer_id'];
            $commandManager->is_vat_enabled = true;
            $commandManager->save();
        }
        //DISABLE VAT

        if ($payload['command'] === 'DISBLEVAT') {

            $customerId = $payload['customer_id'];

            $userCommand = CommandManager::whereCustomerId($customerId)->first();
            if ($userCommand) {
                $userCommand->is_vat_enabled = false;
                $userCommand->update();
            }
        }

        //CHANGE QR CODE SEQUENCE
        if ($payload['command'] === 'RCTVCODE') {

            $commandManager = new CommandManager();
            $commandManager->customer_id = $payload['customer_id'];
            $commandManager->change_qr_code = true;
            $commandManager->new_qr_code = $payload['RCTVCODE'];
            $commandManager->save();

        }

    }
}




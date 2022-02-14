<?php

namespace App\Transformers;

use App\Models\Transaction;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class TransactionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = ['customer'];

    public function transform(Transaction $transaction): array
    {
        return [
            "reference" => $transaction->reference,
            "code_sale_master" => $transaction->code_sale_master,
            "total_value" => $transaction->total_value,
            "total_paid" => $transaction->total_paid,
            "sale_type" => $transaction->sale_type,
            "date_sale" => $transaction->date_sale,
            "last_update" => $transaction->last_update,
            "description" => $transaction->description,
        ];

    }

    /**
     * @param Transaction $transaction
     * @return Item
     */
    public function includeCustomer(Transaction $transaction): Item
    {
        return $this->item($transaction->customer, new UserTransformer(), 'customer');


    }


}

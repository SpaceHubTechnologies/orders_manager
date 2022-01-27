<?php

namespace App\Transformers;

use App\Models\Transaction;
use App\Models\User;
use League\Fractal\Resource\Collection;
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
            "Reference" => $transaction->reference,
            "amount" => $transaction->amount,
            "charges" => $transaction->charge,
            "details" => $transaction->comments,
            "Channel" => $transaction->payment_gateway,
            "created_at" => $transaction->created_at,
        ];

    }

    /**
     * @param User $user
     * @return Collection
     */
    public function includeCustomer(Transaction $transaction): Collection
    {
        //return a role if user has one
        return $this->collection($transaction->customer, new UserTransformer());

    }


}

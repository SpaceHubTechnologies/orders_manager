<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Transaction
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $reference
 * @property string $type Type of transaction credit/debit
 * @property float $amount
 * @property string|null $comments Details of this transaction
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Transaction newModelQuery()
 * @method static Builder|Transaction newQuery()
 * @method static Builder|Transaction query()
 * @method static Builder|Transaction whereAmount($value)
 * @method static Builder|Transaction whereComments($value)
 * @method static Builder|Transaction whereCreatedAt($value)
 * @method static Builder|Transaction whereId($value)
 * @method static Builder|Transaction whereReference($value)
 * @method static Builder|Transaction whereType($value)
 * @method static Builder|Transaction whereUpdatedAt($value)
 * @method static Builder|Transaction whereUserId($value)
 * @mixin \Eloquent
 * @property string $payment_gateway
 * @property float $charge
 * @method static Builder|Transaction whereCharge($value)
 * @method static Builder|Transaction wherePaymentGateway($value)
 * @property-read \App\Models\User|null $customer
 */
class Transaction extends Model
{
    use HasFactory;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

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
 * @property int|null $customer_id
 * @property string $code_sale_master
 * @property int $status
 * @property string $date_sale
 * @property string $last_update
 * @property string $payment_method
 * @property float $total_value
 * @property float $total_paid
 * @property string $sale_type
 * @property string|null $description Details of this transaction
 * @method static Builder|Transaction whereCodeSaleMaster($value)
 * @method static Builder|Transaction whereCustomerId($value)
 * @method static Builder|Transaction whereDateSale($value)
 * @method static Builder|Transaction whereDescription($value)
 * @method static Builder|Transaction whereLastUpdate($value)
 * @method static Builder|Transaction wherePaymentMethod($value)
 * @method static Builder|Transaction whereSaleType($value)
 * @method static Builder|Transaction whereStatus($value)
 * @method static Builder|Transaction whereTotalPaid($value)
 * @method static Builder|Transaction whereTotalValue($value)
 * @property int $post_receipt_status
 * @method static Builder|Transaction wherePostReceiptStatus($value)
 */
class Transaction extends Model
{
    use HasFactory;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CommandManager
 *
 * @property int $id
 * @property int $block_receipt
 * @property int $change_Rct_VCode
 * @property int $disable_vat
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|CommandManager newModelQuery()
 * @method static Builder|CommandManager newQuery()
 * @method static Builder|CommandManager query()
 * @method static Builder|CommandManager whereBlockReceipt($value)
 * @method static Builder|CommandManager whereChangeRctVCode($value)
 * @method static Builder|CommandManager whereCreatedAt($value)
 * @method static Builder|CommandManager whereDisableVat($value)
 * @method static Builder|CommandManager whereId($value)
 * @method static Builder|CommandManager whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $customer_id
 * @property int $is_blocked
 * @property string|null $block_reason
 * @property int $is_vat_enabled
 * @method static Builder|CommandManager whereBlockReason($value)
 * @method static Builder|CommandManager whereCustomerId($value)
 * @method static Builder|CommandManager whereIsBlocked($value)
 * @method static Builder|CommandManager whereIsVatEnabled($value)
 * @property int $change_qr_code
 * @property string|null $new_qr_code
 * @method static Builder|CommandManager whereChangeQrCode($value)
 * @method static Builder|CommandManager whereNewQrCode($value)
 */
class CommandManager extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'block_receipt', 'change_Rct_VCode', 'disable_vat'];


    /*Commands:-
	BLOCK:

The aim for this command is to disable the system and stop it from issuing receipt and also display to user why system is blocked (Message from TRA)

UNBLOCK:

	If System was blocked as above this command will unblock it and allow it to issue receipts

RCTVCODE:

	This command changes QR Code sequence i.e if it was 73281C TRA may change it to 54683D

ENABLEVAT:


	If a trader is not registered for VAT (Not allowed to charge VAT) TRA may issue this command to allow him start charging VAT on items and their receipt/Zreport will display VRN number in the header (Instead of Not Registered)

DISBLEVAT:
	This command disable system (trader) from charging VAT in items sold and removes the VRN no from the receipt and Z Report header (Displays Not Registered)*/
}

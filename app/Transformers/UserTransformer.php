<?php

namespace App\Transformers;

use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = ['roles', 'wallet'];

    public function transform(User $user): array
    {
        try {
            $access_token = $user->tokens()->first()->accessToken;
            if (empty($access_token)) {
                $access_token = @$user->createToken('personal')->accessToken;
            }
        } catch (Exception $e) {
            $access_token = @$user->createToken('personal')->accessToken;
        }

        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'display_name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'access_token'=> $access_token,
        ];
    }




}

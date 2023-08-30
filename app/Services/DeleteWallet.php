<?php

namespace App\Services;

use App\Models\Wallet;
use Illuminate\Http\JsonResponse;

class DeleteWallet{

    public function delete(Wallet $wallet)
    {
        $wallet->delete();
        return new JsonResponse([
            'status'=>'Successful',
            'message'=> "$wallet->name is deleted successfully"
        ],204);
    }
}

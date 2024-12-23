<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;

class BalanceTransactions extends Controller
{
    public function listAllBalanceTransactions(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        return response()->json(
            $stripe->balanceTransactions->all(
                ['limit' => 3]
            )
        );
    }
}

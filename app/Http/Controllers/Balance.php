<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;

class Balance extends Controller
{
    public function retrieveBalance(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        return response()->json(
            $stripe->balanceTransactions->retrieve($request->id, [
                'expand' => ['source.payment_intent.latest_charge']
            ])
        );
    }
}

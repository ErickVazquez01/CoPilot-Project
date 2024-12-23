<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;

class Refunds extends Controller
{
    public function createRefund(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        return response()->json(
            $stripe->refunds->create([
                'payment_intent' => $request->input('payment_intent'),
                'amount' => 599,
            ])
        );
    }
}

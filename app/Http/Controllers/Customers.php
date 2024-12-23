<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
class Customers extends Controller
{
    public function createCustomer(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));
        $query_params = $request->query();

        return response()->json(
            $stripe->customers->create($query_params)
        );
    }
}

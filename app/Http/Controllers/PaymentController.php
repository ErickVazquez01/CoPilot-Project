<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        return response()->json(
            $stripe->paymentIntents->create([
                'amount' => 1299,
                'currency' => 'usd',
                /*'metadata' => ['idempotency' => 'KG5LxwFBepaKHyUD',
                                'reason' => 'new parameters same idempotency'],*/
                //'automatic_payment_methods' => ['enabled' => true],
                //'payment_method_types' => ['card', 'link'],
                //'payment_method_configuration' => 'pmc_1QUdO8AmVfjFMv0DN1OrMgiO'
                //'customer' => 'cus_RPiif38xTFV0jg',
                //'setup_future_usage' => 'off_session',
                //'payment_method_types' => ['card'],
                //'capture_method' => 'manual', //to hold on the payment
                //'automatic_payment_methods' => ['enabled' => true],
                /*'payment_method_types' => [
                    'card',
                    'oxxo',
                ],*/
                //'payment_method_types' => ['oxxo', 'card', 'link'],
            ])
        );
    }

    public function retrievePaymentIntent(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        return response()->json(
            $stripe->paymentIntents->retrieve($request->id, ['expand' => ['latest_charge']])
        );
    }

    public function capturePaymentIntent(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));
        $query_params = $request->query();
        return response()->json(
            $stripe->paymentIntents->capture($request->id, $query_params)
        );
    }

    public function cancelPaymentIntent(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        return response()->json(
            $stripe->paymentIntents->cancel($request->id)
        );
    }

    public function createConfirmPaymentIntent(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        // Genera una clave de idempotencia única
        $idempotencyKey = (string) Str::uuid();

        try {
            $intent = $stripe->paymentIntents->create([
                'confirm' => true,
                'amount' => 1099,
                'currency' => 'usd',
                'automatic_payment_methods' => ['enabled' => true],
                'confirmation_token' => $request->input('confirmationTokenId'),
                //'metadata' => ['stripe-pai' => 'stripe copilot example'],
            ], ['idempotency_key' => 'KG5LxwFBepaKHyUD']);
            return response()->json(['paymentIntent' => $intent]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        //return response()->json(['confirmationToken' => $request->input('confirmationTokenId')]);
    }

    public function listPaymentIntent(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        try{
            $intents = $stripe->paymentIntents->all();
            $ids_amount_above_1099 = [];

            foreach($intents->autoPagingIterator() as $intent) {
                if($intent->amount > 1099) {
                    array_push($ids_amount_above_1099, $intent->id);
                }
            }

            return response()->json(['paymentIntents' => $ids_amount_above_1099]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

    }
}

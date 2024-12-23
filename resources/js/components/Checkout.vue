<script setup>
import { onMounted } from 'vue';

const stripe = Stripe('pk_test_51QXRUxAiCyfpC5ntVYXYX1Lm67bcwp4YwZLuaSjzu0plPlmdAZAoAwebt9mhBcHRQxq9Dc5GZIIwFop9ux35Sl1V00Nb8O65yd');



function buildCheckout() {
    const appearance = {}
    const options = {
        //payment_method_types: ['card', 'link', 'paypal'],
    };
    const elements = stripe.elements({
        mode: 'payment',
        amount: 1099,
        currency: 'usd',
        appearance,
    });

    const expressCheckoutElement = elements.create('expressCheckout', options);
    expressCheckoutElement.mount('#express-checkout-element');

    expressCheckoutElement.on('click', (event) => {
        const options = {
            emailRequired: true,
            phoneNumberRequired: true
        };
        event.resolve(options);
    });

    expressCheckoutElement.on('cancel', () => {
        elements.update({ amount: 1099 })
    });

    expressCheckoutElement.on('confirm', async (event) => {
        const { error: submitError } = await elements.submit();
        if (submitError) {
            handleError(submitError);
            return;
        }

        // Create the PaymentIntent and obtain clientSecret
        const res = await fetch('/payment-intents', {
            method: 'POST',
        });
        const { client_secret: clientSecret } = await res.json();

        const { error } = await stripe.confirmPayment({
            // `elements` instance used to create the Express Checkout Element
            elements,
            // `clientSecret` from the created PaymentIntent
            clientSecret,
            confirmParams: {
                return_url: 'http://127.0.0.1:8000/redirect-payment',
            },
        });

        if (error) {
            // This point is only reached if there's an immediate error when
            // confirming the payment. Show the error to your customer (for example, payment details incomplete)
            handleError(error);
        } else {
            // The payment UI automatically closes with a success animation.
            // Your customer is redirected to your `return_url`.
        }
    });

}

const handleError = (error) => {
    const messageContainer = document.querySelector('#error-message');
    messageContainer.textContent = error.message;
}


onMounted(() => {
    buildCheckout();
});

</script>
<template>
    <div>
        <div id="express-checkout-element">
            <!-- Express Checkout Element will be inserted here -->
        </div>
        <div id="error-message">
            <!-- Display an error message to your customers here -->
        </div>
    </div>
</template>
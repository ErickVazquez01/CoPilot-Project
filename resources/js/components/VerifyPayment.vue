<script setup>
import { onMounted } from 'vue';

//stripe ES 
//const stripe = Stripe('pk_test_51QXRUxAiCyfpC5ntVYXYX1Lm67bcwp4YwZLuaSjzu0plPlmdAZAoAwebt9mhBcHRQxq9Dc5GZIIwFop9ux35Sl1V00Nb8O65yd');
//stripe MX
const stripe = Stripe('pk_test_51QUdNbAmVfjFMv0Dc5Dg8aJcSKNDSZiHCfeFdv27IHhdF9Amk4hicn0LrWRmVjv1dWIgZTqN739iiyEiqY8vrDBH00oimECW2u');

const urlParams = new URLSearchParams(window.location.search);
const clientSecret = urlParams.get('payment_intent_client_secret');

async function confirmPayment() {
    const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);
    if (paymentIntent && paymentIntent.status === 'succeeded') {
        // Payment was successful
        // Clear the paymentId from localStorage
        localStorage.removeItem('paymentId');
        document.getElementById('message').innerText = 'Payment was successful!';
    } else if(paymentIntent && paymentIntent.status === 'requires_action') {
        localStorage.removeItem('paymentId');
        document.getElementById('message').innerText = 'Payment requires additional action. Please try again or complete the additional steps.';
    }else {
        // Payment was not successful
        // Create a new PaymentIntent
        document.getElementById('message').innerText = 'Payment was not successful. Please try again.';
    }
}

onMounted(() => {
    confirmPayment();
});

</script>
<template>
    <div>
        <p id="message"></p>
    </div>
</template>
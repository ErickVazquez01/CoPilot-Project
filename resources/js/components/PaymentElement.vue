<script setup>
import { onMounted } from 'vue'

//stripe ES 
//const stripe = Stripe('pk_test_51QXRUxAiCyfpC5ntVYXYX1Lm67bcwp4YwZLuaSjzu0plPlmdAZAoAwebt9mhBcHRQxq9Dc5GZIIwFop9ux35Sl1V00Nb8O65yd');
//stripe MX
const stripe = Stripe('pk_test_51QUdNbAmVfjFMv0Dc5Dg8aJcSKNDSZiHCfeFdv27IHhdF9Amk4hicn0LrWRmVjv1dWIgZTqN739iiyEiqY8vrDBH00oimECW2u');
let elements;

function findPaymentId() {
    if (localStorage.getItem('paymentId')) {
        retrievePaymentIntent()
    } else {
        createPaymentIntent()
    }
}

async function retrievePaymentIntent() {
    const paymentId = localStorage.getItem('paymentId')
    const response = await fetch(`/payment-intents/${paymentId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    }).then((res) => res.json())

    // If the payment was successful or canceled, create a new PaymentIntent, otherwise, retry with the last payment data
    if (response.status === 'succeeded' || response.status === 'canceled') {
        // Create a new PaymentIntent
        createPaymentIntent()
    } else {
        // Retry with the last payment data
        buildPaymentElement(response.client_secret)
    }
}

async function createPaymentIntent() {
    try {
        const response = await fetch('/payment-intents', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            // body: JSON.stringify({
            //     items: [{ id: 'photo-subscription' }],
            // }),
        }).then((res) => res.json())

        //localStorage.setItem('paymentId', response.id)
        buildPaymentElement(response.client_secret)
    } catch (error) {
        console.error('Error:', error)
    }
}

function buildPaymentElement(clientSecretID) {
    const options = {
        clientSecret: clientSecretID,
        appearance: {
            theme: 'night',
            labels: 'floating'
        },
    }

    elements = stripe.elements(options);

    const paymentElementOptions = { layout: 'accordion'};
    const paymentElement = elements.create('payment', paymentElementOptions);
    paymentElement.mount('#payment-element');
}

async function handleSubmit() {
    const { error, paymentIntent } = await stripe.confirmPayment({
        elements,
        confirmParams: {
            return_url: 'http://127.0.0.1:8000/redirect-payment',
        },
    });

    if (error) {
        // This point will only be reached if there is an immediate error when
        // confirming the payment. Show error to your customer (for example, payment
        // details incomplete)
        const messageContainer = document.querySelector('#error-message');
        messageContainer.textContent = error.message;
    } else if (paymentIntent.status === 'succeeded'){
        // Your customer will be redirected to your `return_url`. For some payment
        // methods like iDEAL, your customer will be redirected to an intermediate
        // site first to authorize the payment, then redirected to the `return_url`.
        console.log(paymentIntent)
        const messageContainer = document.querySelector('#success-message');
        messageContainer.textContent = 'Success! Your payment is confirmed.';
    }

}

onMounted( async() => {
    //findPaymentId()
    createPaymentIntent()
})
</script>
<template>
    <div>
        <h1>Payment Element</h1>
        <form id="payment-form" @submit.prevent="handleSubmit">
            <div id="payment-element">
                <!-- Elements will create form elements here -->
            </div>
            <button id="submit">Submit</button>
            <div id="error-message">
                <!-- Display error message to your customers here -->
            </div>
            <div id="success-message">
                <!-- Display error message to your customers here -->
            </div>
        </form>
    </div>
</template>
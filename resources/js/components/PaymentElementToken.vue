<script setup>
import { onMounted } from 'vue'
//stripe ES 
//const stripe = Stripe('pk_test_51QXRUxAiCyfpC5ntVYXYX1Lm67bcwp4YwZLuaSjzu0plPlmdAZAoAwebt9mhBcHRQxq9Dc5GZIIwFop9ux35Sl1V00Nb8O65yd');
//stripe MX
const stripe = Stripe('pk_test_51QUdNbAmVfjFMv0Dc5Dg8aJcSKNDSZiHCfeFdv27IHhdF9Amk4hicn0LrWRmVjv1dWIgZTqN739iiyEiqY8vrDBH00oimECW2u');

const form = document.getElementById('payment-form');
const submitBtn = document.getElementById('submit');
let elements;

function elementsInstance() {
    const options = {
        mode: 'payment',
        amount: 1099,
        currency: 'usd',
        paymentMethodCreation: 'manual',
        // Fully customizable with appearance API.
        appearance: {/*...*/ },
    };

    // Set up Stripe.js and Elements to use in checkout form
    elements = stripe.elements(options);

    // Create and mount the Payment Element
    const paymentElementOptions = { layout: 'accordion' };
    const paymentElement = elements.create('payment', paymentElementOptions);
    paymentElement.mount('#payment-element');
}

const handleError = (error) => {
    const messageContainer = document.querySelector('#error-message');
    messageContainer.textContent = error.message;
    submitBtn.disabled = false;
}

async function handleSubmit() {
    // Prevent multiple form submissions
    /*if (submitBtn.disabled) {
        return;
    }*/

    // Disable form submission while loading
    //submitBtn.disabled = true;

    // Trigger form validation and wallet collection
    const { error: submitError } = await elements.submit();
    if (submitError) {
        handleError(submitError);
        return;
    }

    // Create the ConfirmationToken using the details collected by the Payment Element
    // and additional shipping information
    const { error, confirmationToken } = await stripe.createConfirmationToken({
        elements,
        params: {
            shipping: {
                name: 'Jenny Rosen',
                address: {
                    line1: '1234 Main Street',
                    city: 'San Francisco',
                    state: 'CA',
                    country: 'US',
                    postal_code: '94111',
                },
            },
            return_url: 'http://127.0.0.1:8000/redirect-payment'
        }
    });

    if (error) {
        // This point is only reached if there's an immediate error when
        // creating the ConfirmationToken. Show the error to your customer (for example, payment details incomplete)
        handleError(error);
        return;
    }

    // Create the PaymentIntent
    const res = await fetch("/create-confirm-intent", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            confirmationTokenId: confirmationToken.id,
        }),
    });

    const data = await res.json();

    // Handle any next actions or errors. See the Handle any next actions step for implementation.
    handleServerResponse(data);
}

const handleServerResponse = async (response) => {
    if (response.error) {
        // Show error from server on payment form
        const messageContainer = document.querySelector('#error-message');
        messageContainer.textContent = response.error;
    } else if (response.status === "requires_action") {
        // Use Stripe.js to handle the required next action
        const {
            error,
            paymentIntent
        } = await stripe.handleNextAction({
            clientSecret: response.clientSecret
        });

        if (error) {
            // Show error from Stripe.js in payment form
            const messageContainer = document.querySelector('#error-message');
            messageContainer.textContent = error.message;
        } else {
            // Actions handled, show success message
        }
    } else {
        // No actions needed, show success message
    }
}

onMounted(async () => {
    elementsInstance();
})
</script>
<template>
    <div>
        <h1>Payment Confirmation Token</h1>
        <form id="payment-form" @submit.prevent="handleSubmit">
            <div id="payment-element">
                <!-- Elements will create form elements here -->
            </div>
            <button id="submit">Submit</button>
            <div id="error-message">
                <!-- Display error message to your customers here -->
            </div>
        </form>
    </div>
</template>
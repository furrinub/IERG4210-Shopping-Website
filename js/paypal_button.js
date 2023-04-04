
// paypal checkout button in cart UI
paypal.Buttons({
    /* Sets up the transaction when a payment button is clicked */
    async createOrder(data, actions) {
        console.log('createOrder called:');
        /* create an order from localStorage */
        let order_details = await fetch("create_order.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(get_cart_array(), null, 2)
        }).then(response => response.text().then(t => {
            console.log(t)
            return JSON.parse(t)
        }));

        console.log(order_details);

        return actions.order.create(order_details);
    },

    /* Finalize the transaction after payer approval */
    // https://developer.paypal.com/sdk/js/reference/#onapprove
    async onApprove(data, actions) {
        return actions.order.capture()
            .then(async orderData => {
                /* Successful capture! For dev/demo purposes: */
                console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));

                await fetch('save_order.php', {
                    method: "POST",
                    headers: {
                    "Content-Type": "application/json",
                    },
                    body: JSON.stringify(orderData, null, 2)
                }).then(response => response.text().then(t => console.log(t)));

                clear_cart(); // Clear the web shop cart
                window.location.href = "index.php"; // Redirect to another page
            });
    },
}).render('#paypal-button-container')

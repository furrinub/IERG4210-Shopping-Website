# IERG4210 Shopping Website
IERG4210 shopping website built with bootstrap and PHP.

# Phase 1
Currently there are only 2 products: apple and banana. And there is one catagory: Food. More will be added in the later phrase.

# Phase 2
- Website is hosted on Amazon EC2.
- Products info is fetched from the database using PHP. (PID, CID, NAME, PRICE, DESCRIPTION, QUANTITY)
- Admin Page is created for updating database with a friendly interface.
- When a product image is uploaded, the server will store a resized big image and a thumbnail.
- HTML5 Drag-and-drop upload is implemented in the product image uploading section.
- Admin panel username: s46
- Password: ierg4210

# Phase 3
- Fixed a lot of bugs.
- Implement infinite scroll.
- Shopping cart is now working.

# Phase 4
- Fixed add-to-cart bugs in infinite scroll.
- Add nonces to forms.
- Use DB to store accounts.
- Use cookie to store auth token.
- Now only admin can edit the database through the webpages.
- All connections are forced to be HTTPS.
- Admin email: s46@ie.cuhk
- Password: ierg4210
- User: test@ie.cuhk
- Password: ierg4210

# Phase 5
- Integrated with Paypal. secret.json is stored under /var/www.
- account.php shows order records and "change password" page.
- order_details.php shows all information about an order with provided transaction id. Only the buyer and admin can view the order.
- Admin Panel shows all orders.
- Create a db table "orders" to store orders. The command is inside save_order.php.
- Special modification:
    - create_order.php
        - gen_uuid is modified to return valid uuid v4
        - The first use of function gen_digest is commented out
    - payment.php
        - Add website header and footer
        - Move the inline script into paypal_button.js

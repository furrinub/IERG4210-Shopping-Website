/*
https://stackoverflow.com/questions/2669690/why-does-google-prepend-while1-to-their-json-responses
*/

const buy_count_elem = document.getElementById('buy_quantity')
const cart_price = document.getElementById('cart_price_display')
const cart_count = document.getElementById('cart_item_count_display')
const cart_list = document.getElementById('CartList')
const FETCH_LINK = '/admin_process.php?action=prod_fetchAll'
let info = null // array

async function fetch_info() {
    const PREFIX = "while(1);";
    const response = await fetch(FETCH_LINK)
    let body = await response.text()

    if (body.startsWith(PREFIX)) {
        body = body.slice(PREFIX.length);
    }
    info = JSON.parse(body).success
}

// set name and price of products in cart
// info is sorted by pid but some pid may be missing
function set_cart_display() {
    let html = ''
    let total_price = 0
    let item_count = 0
    for (const pid of Object.keys(cart)) {
        for (const prod of info) {
            if (prod['PID'] == pid) {
                html += `
                <li class="list-group-item">
                    <div class="row align-items-center" data-pid="${prod['PID']}">
                        <span class="cart-del-btn mx-2 p-0 col-1"><i class="bi bi-trash"></i></span>
                        <a href="prduct.php?pid=${prod['PID']}" class="mx-2 p-0 col-2"><img src="product_images/thumbnails/${prod['PID']}.webp" width="64" height="64"></a>
                        <span class="mx-3 p-0 col">${prod['NAME']}</span>
                        <input type="number" name="quantity" min="1" max="${prod['QUANTITY']}" value="${cart[pid]}" class="cart-input-quantity mx-3 col-3">
                    </div>
                </li>`
                total_price += parseFloat(prod['PRICE']) * cart[pid]
                break
            }
        }
        item_count += cart[pid]
    }
    cart_list.innerHTML = html
    cart_price.textContent = "" + Math.round(total_price*10) / 10
    cart_count.textContent = "" + item_count + (item_count < 2 ? " item" : " items")


    // update cart when quantity change
    for (const elem of cart_list.getElementsByClassName('cart-input-quantity')) {
        elem.onchange = (e) => {
            let i = e.currentTarget
            let pid = i.parentElement.dataset.pid
            cart[pid] = parseInt(i.value)
            localStorage.cart = JSON.stringify(cart)
            // need to update price
            set_cart_display()
        }
    }

    // update cart when delete
    for (const elem of cart_list.getElementsByClassName('cart-del-btn')) {
        elem.onclick = (e) => {
            let pid = e.currentTarget.parentElement.dataset.pid
            delete cart[pid]
            localStorage.cart = JSON.stringify(cart)
            set_cart_display()
        }
    }
}



if (!('cart' in localStorage)) {
    localStorage.cart = '{}'
}
let cart = JSON.parse(localStorage.cart) // {str: int}
fetch_info().then(set_cart_display)

// add to cart function
for (const elem of document.getElementsByClassName('add-cart')) {
    elem.onclick = (e) => {
        let pid = e.currentTarget.dataset.pid
        let buy_count = buy_count_elem ? parseInt(buy_count_elem.value) : 1;
        if (pid in cart) {
            cart[pid] += buy_count
        } else {
            cart[pid] = buy_count
        }
        localStorage.cart = JSON.stringify(cart)
        set_cart_display()
    }
}


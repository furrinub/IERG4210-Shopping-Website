const RANDOM_FETCH_LINK = '/admin_process.php?action=prod_fetchThreeRandom'
const load_cooldown_ms = 1000
let more_prod_list = document.getElementById('more_prod_list')
let last_load = Date.now();
let scrolled = true;


async function load_more() {
    const PREFIX = "while(1);";
    const response = await fetch(RANDOM_FETCH_LINK)
    let body = await response.text()

    if (body.startsWith(PREFIX)) {
        body = body.slice(PREFIX.length);
    }
    //console.log(body)
    let prods = JSON.parse(body).success

    let html = ''
    for (const p of prods) {
        html += `
<li class="home-item d-inline-block pb-2 col-3">
    <a href="product.php?pid=${p['PID']}" class="text-decoration-none text-black">
        <img src="product_images/thumbnails/${p['PID']}.webp" width="160" height="160">
        <p>${p['NAME']}</p>
    </a>
    <hr>
    <p class="price">HK$${p['PRICE']}</p>
    <button type="button" class="add-cart" data-pid="${p['PID']}"><i class="bi bi-cart"></i> Add to Cart</button>
</li>`
    }

    more_prod_list.innerHTML += html

    set_add_cart_onclick() // function from cart.js; set onclick function for every add-cart button
}


let load_timer = null
window.onscroll = () => {
    // 60px above the bottom of the page
    if ((window.innerHeight + window.pageYOffset + 60) >= document.body.offsetHeight) {
        let date_now = Date.now()
        if (last_load + load_cooldown_ms <= date_now) { // cooldown finish
            last_load = Date.now()
            load_more().then()
            if (load_timer) {
                clearTimeout(load_timer)
                load_timer = null
            }
        } else if (!load_timer) { // cooldowning, but not pending to load more
            load_timer = setTimeout(() => {
                window.onscroll()
                load_timer = null
            }, last_load + load_cooldown_ms - date_now + 1)
        }
        
    }
};

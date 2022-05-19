jQuery(document).ready(async function ($) {

    async function getCart() {
        const url = `${window.location.origin}/wp-json/wc/store/cart`;
        const options = {
            credentials: 'include',
            method: "GET",
            headers: {
                'Content-Type': 'application/json'
            },
        };

        return await fetch(url, options).then(response => response.json());
    }

    $(document.body).on('updated_cart_totals', async function () {
        const cart = await getCart()
        const api = await window.BOLD.pre.ready()
        const boldCart = await api.getCart()

        boldCart?.items.forEach(item => {
            let cartItemToAdd = cart?.items.find(cartItem => cartItem.key === item.id)
            let quantity = cartItemToAdd && cartItemToAdd.quantity ? cartItemToAdd.quantity : 0
            window.BOLD.pre.events.emit('cart_updated_qty', { "qty": quantity, "id": item.id });
        })


    });
    $(document.body).on('wc_cart_emptied', async function () {
        window.BOLD && window.BOLD.common && window.BOLD.common.eventEmitter.emit("BOLD_NEW_CART", { lineItems: { "items": [], "products": [], "cart_total": 0 } });
    })
})
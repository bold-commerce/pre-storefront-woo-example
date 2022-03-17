jQuery(document).ready(function ($) {
	function addXMLRequestCallback(callback) {
		var oldSend, i;
		if (XMLHttpRequest.callbacks) {
			// we've already overridden send() so just add the callback
			XMLHttpRequest.callbacks.push(callback);
		} else {
			// create a callback queue
			XMLHttpRequest.callbacks = [callback];
			// store the native send()
			oldSend = XMLHttpRequest.prototype.send;
			// override the native send()
			XMLHttpRequest.prototype.send = function () {

				for (i = 0; i < XMLHttpRequest.callbacks.length; i++) {
					XMLHttpRequest.callbacks[i](this);
				}
				// call the native send()
				oldSend.apply(this, arguments);
			}
		}
	}

	// e.g.
	addXMLRequestCallback(function (xhr) {
		addListeners(xhr)
	});

	function addListeners(xhr) {
		xhr.addEventListener('loadend', handleEvent);
	}

	async function handleEvent(e) {
		if (e && e.currentTarget && e.currentTarget.responseURL && e.currentTarget.responseURL.includes('get_refreshed_fragments')) {
			await getCart().then(data => {
				if (data && data.items) {
					data.items.map(item => {
						window.BOLD.pre.events.emit('cart_updated_qty', { "qty": item.quantity, "id": item.key });
					})
				}
			})
		}

		if (e && e.currentTarget && e.currentTarget.responseURL && e.currentTarget.responseURL.includes(`removed_item`)) {
			await getCart().then(data => {
				if(data && data.items_count === 0){
					window.BOLD && window.BOLD.common && window.BOLD.common.eventEmitter.emit("BOLD_NEW_CART", { lineItems: {"items":[],"products":[],"cart_total":0} });
					
					$( document ).trigger( 'wc_update_cart' );
				}
				if (data && data.items && data.items_count >= 1) {
					let platformData = JSON.parse($('#bold-platform-data').html());
					let boldCart = platformData['cart']

					let boldCartItems = Object.keys(boldCart['items']).filter(i => data.items.find(x => x.key === i))
					let mergedItems = boldCartItems.map(x => {
						if (x === boldCart['items'][x].key) {
							let wooItem = data.items.find(item => item.key === boldCart['items'][x].key)

							boldCart['items'][x].quantity = wooItem.quantity
							return { [x]: boldCart['items'][x] }
						}
					})

					let cartItems = Object.assign.apply(boldCart['items'], mergedItems)
					let cartProducts = boldCart.products.filter(i => data.items.find(x => x.id === i.id))

					let cartTotal = (data.totals.total_items / 100).toFixed(2)

					window.BOLD && window.BOLD.common && window.BOLD.common.eventEmitter.emit("BOLD_NEW_CART", { lineItems: { "items": cartItems, "products": cartProducts, "cart_total": cartTotal } });
					
					$( document ).trigger( 'wc_update_cart' );
				}
			})
		}
	}

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
});

jQuery(document).ready(function($){
	function addXMLRequestCallback(callback){
		var oldSend, i;
		if( XMLHttpRequest.callbacks ) {
			// we've already overridden send() so just add the callback
			XMLHttpRequest.callbacks.push( callback );
		} else {
			// create a callback queue
			XMLHttpRequest.callbacks = [callback];
			// store the native send()
			oldSend = XMLHttpRequest.prototype.send;
			// override the native send()
			XMLHttpRequest.prototype.send = function(){
	
				for( i = 0; i < XMLHttpRequest.callbacks.length; i++ ) {
					XMLHttpRequest.callbacks[i]( this );
				}
				// call the native send()
				oldSend.apply(this, arguments);
			}
		}
	}
	
	// e.g.
	addXMLRequestCallback( function( xhr ) {
		addListeners(xhr)
	});
	
	function addListeners(xhr) {
		xhr.addEventListener('loadend', handleEvent);
	}
	
    async function handleEvent(e) {
        if(e && e.currentTarget && e.currentTarget.responseURL.responseURL === `${window.location.origin}/cart/`){
            console.log(e)
            let cart = await getCart()
            cart.items.map(item => {
                window.BOLD.pre.events.emit('cart_updated_qty', { qty: item.quantity, id: item.key });
            })
        }
    }
	
	async function getCart(){
	const url = `/wp-json/wc/store/cart`;
	const options = {
		credentials: 'include',
		method: "GET",
		headers: {
		  'Content-Type':'application/json'
		},
	  };
	
	  return await fetch(url, options).then(response => response.json());
	}
});

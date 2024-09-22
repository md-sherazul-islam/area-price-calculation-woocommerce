jQuery(window).ready(()=>{
	jQuery('#spIncrement').click(()=>{
		jQuery('#numOfBoxes').val( function(i, oldval) {
			return ++oldval;
		});
		totalAreaCalculation();
		spPriceCalculation();
	});

	jQuery('#spDecrement').click(()=>{
		jQuery('#numOfBoxes').val( function(i, oldval) {
			return --oldval;
		});
		totalAreaCalculation();
		spPriceCalculation();
	})

	jQuery('#numOfBoxes').keyup(()=>{
		let areaPerBox = jQuery('#perBoxArea').val();
		let numOfBoxes = jQuery('#numOfBoxes');
		let totalArea = jQuery('#totalArea');
		let totalAreaNumber = (areaPerBox*numOfBoxes.val()).toFixed(4);
		totalArea.val(totalAreaNumber);
		spPriceCalculation();
	})

	jQuery('#requiredArea').keyup(()=>{
		spCalculation();
		spPriceCalculation();
	})

	function spCalculation(){
		let areaPerBox = jQuery('#perBoxArea').val();
		let areaRequired = jQuery('#requiredArea').val();
		let totalArea = jQuery('#totalArea');
		let numOfBoxes = jQuery('#numOfBoxes');

		let boxRequired = Math.ceil(areaRequired/areaPerBox);

		numOfBoxes.val(boxRequired);
		let totalAreaNumber = (areaPerBox*numOfBoxes.val()).toFixed(4);
		totalArea.val(totalAreaNumber);
		spPriceCalculation();
	}

	function totalAreaCalculation(){
		let areaPerBox = jQuery('#perBoxArea').val();
		let numOfBoxes = jQuery('#numOfBoxes');
		let totalArea = jQuery('#totalArea');
		let totalAreaNumber = (areaPerBox*numOfBoxes.val()).toFixed(4);
		totalArea.val(totalAreaNumber);
	}

	function spPriceCalculation(){
		let numOfBoxes = jQuery('#numOfBoxes').val();
		let spPrice = jQuery('#spPrice').val();

		let spTotalPrice = (numOfBoxes*spPrice).toFixed(2);

		jQuery('#spPriceAmount').text(spTotalPrice);
	}

	jQuery('#spAddToCart').click(()=>{
		var product_id = jQuery('#spAddToCart').attr('data-productid');
		var quantity = jQuery('#numOfBoxes').val();
		var variationId = jQuery('.variation_id').val();
		console.log(product_id, quantity, variationId);
		jQuery.ajax({
			url: '/wp-admin/admin-ajax.php',
			type: 'POST',
			data: {
				action: 'sp_add_to_cart',
				product_id: product_id,
				quantity: quantity,
				variation_id: jQuery('.variation_id').val(),
			},
			success: function(response) {
				if (response.error & response.product_url) {
					window.location = response.product_url;
					return;
				} else {
					jQuery(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, jQuery(this)]);
				}
				console.log(response);
			},
			error: function(response) {
				// Handle error
				alert('Failed to add products to cart.');
			}
		});
	})
	
})
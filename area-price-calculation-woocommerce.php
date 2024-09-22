<?php
/*
 * Plugin Name:       Area Price Calculator
 * Description:       Calculate price based on the area selected.
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            sherazul
 * Author URI:        https://seopage1.net/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       area-price-calculation
 * Requires Plugins:  woocommerce
 */

if (!defined('ABSPATH')) { exit; }
 
// Add style sheet
wp_enqueue_style( 'wc-custom-fields-css', plugin_dir_url(__FILE__) . 'css/sp-style.css' );
wp_enqueue_script( 'wc-custom-fields-js', plugin_dir_url(__FILE__) . 'js/sp-script.js', array('jquery'), null, true );


// Add the custom fields to the product data tab
function add_custom_product_data_fields() {
    global $woocommerce, $post;

    echo '<div class="options_group">';

    // Custom Text Field
    woocommerce_wp_text_input( 
        array( 
            'id'          => '_area_symbol', 
            'label'       => __( 'Area symbol', 'woocommerce' ), 
            'placeholder' => 'Enter text here',
            'desc_tip'    => 'true',
            'description' => __( 'Enter the custom area symbol for this product.', 'woocommerce' ) 
        )
    );

    // Custom Number Field
    woocommerce_wp_text_input( 
        array( 
            'id'                => '_area_per_box', 
            'label'             => __( 'Area Per Box', 'woocommerce' ), 
            'placeholder'       => 'Enter number here',
            'desc_tip'          => 'true',
            'description'       => __( 'Enter per box area number for this product.', 'woocommerce' ),
            'type'              => 'number',
            'custom_attributes' => array(
                'step'  => 'any',
                'min'   => '1'
            )
        )
    );

    woocommerce_wp_text_input( 
        array( 
            'id'                => '_price_for_per_area', 
            'label'             => __( 'Price Per area', 'woocommerce' ), 
            'placeholder'       => 'Enter price here',
            'desc_tip'          => 'true',
            'description'       => __( 'Enter per area price here', 'woocommerce' ),
            'type'              => 'number',
            'custom_attributes' => array(
                'step'  => 'any',
                'min'   => '1'
            )
        )
    );

    echo '</div>';
}
add_action( 'woocommerce_product_options_general_product_data', 'add_custom_product_data_fields' );

// Save the custom fields
function save_custom_product_data_fields( $post_id ) {
    $per_box_price = $_POST['_price_for_per_area'];
    // if( !empty( $custom_text_field ) )
        update_post_meta( $post_id, '_price_for_per_area', esc_attr( $per_box_price ) );

    $area_per_box = $_POST['_area_per_box'];
    // if( !empty( $custom_number_field ) )
        update_post_meta( $post_id, '_area_per_box', esc_attr( $area_per_box ) );

    $area_symbol = $_POST['_area_symbol'];
    // if( !empty( $custom_number_field ) )
        update_post_meta( $post_id, '_area_symbol', esc_attr( $area_symbol ) );
}
add_action( 'woocommerce_process_product_meta', 'save_custom_product_data_fields' );


// Display custom fields on the product page
function display_custom_product_data_fields() {
    global $post;
    global $product;

    // Get the custom field values
    $custom_text_field = get_post_meta( $post->ID, '_custom_text_field', true );
    $custom_number_field = get_post_meta( $post->ID, '_custom_number_field', true );
    $per_box_price = get_post_meta( $post->ID, '_price_for_per_area', true );
    $area_symbol = get_post_meta( $post->ID, '_area_symbol', true );
    $area_per_box = get_post_meta( $post->ID, '_area_per_box', true );

    // Display custom order calculation
    echo '<div class="sp-order-calculation">';
        echo '<input type="hidden" id="perBoxArea" value="'.$area_per_box.'"/>';
        echo '<input type="hidden" id="spPrice" value="'. $product->get_price() .'"/>';
        if($per_box_price){
        echo '<div class="sp-row">';
            echo '<div class="sp-col-6">';
                echo '<p class="sp-paragraph"><b> Price (/'.$area_symbol.')</b></p>';
                //echo '<p class="sp-paragraph">'. $custom_text_field . '</p>';
                echo '<p class="sp-paragraph box-price">(price per box:'. $product->get_price_html() .') </p>';
            echo '</div>';
            
            echo '<div class="sp-col-6">';
                echo '<h3 class="price">'. get_woocommerce_currency_symbol(). $per_box_price . '</h3>';
            echo '</div>';
        echo '</div>';
        }

        if($area_per_box){
        echo '<div class="sp-row">';
            echo '<div class="sp-col-6">';
                echo '<p class="sp-paragraph"><b>Area Required*</b></p>';
                echo '<p class="sp-paragraph box-price">Mandatory Field</p>';
            echo '</div>';

            echo '<div class="sp-col-6">';
                echo '<div class="input-wrapper">';
                    echo '<input type="number" name="required_area" id="requiredArea" class="sp-input-field">';
                    echo '<p class="sp-paragraph">'. $area_symbol . '</p>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
        }
        if($area_per_box){
        echo '<div class="sp-row">';
            echo '<div class="sp-col-6">';
                echo '<p class="sp-paragraph"><b>Boxes: </b></p>';
                echo '<p class="sp-paragraph box-price">'.  $area_per_box. '/box';
            echo '</div>';

            echo '<div class="sp-col-6">';
                echo '<div class="input-wrapper quantity-input">';
                    echo '<button id="spDecrement" type="button"> - </button>';
                    echo '<input type="number" name="number_of_boxes" id="numOfBoxes" class="sp-input-field" value="1">';
                    echo '<button id="spIncrement" type="button"> + </button>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
        }
        if($area_per_box){
        echo '<div class="sp-row">';
            echo '<div class="sp-col-6">';
                echo '<p class="sp-paragraph"><b>Total Area: </b></p>';
            echo '</div>';

            echo '<div class="sp-col-6">';
                echo '<div class="input-wrapper">';
                    echo '<input type="text" name="total_area" id="totalArea" class="sp-input-field" value="'.$area_per_box.'" readonly>';
                    echo '<p class="sp-paragraph">'. ! empty( $area_symbol )?$area_symbol : '' . '</p>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
        }
        if($area_per_box){
        echo '<div class="sp-row">';
            echo '<div class="sp-col-6">';
                echo '<p class="sp-paragraph"><b>Total Cost: </b></p>';
            echo '</div>';

            echo '<div class="sp-col-6">';
                echo '<div class="sp-product-price">';
                    echo '<h3 id="spTotalPrice">'.get_woocommerce_currency_symbol().'<span id="spPriceAmount">'. $product->get_price() . '</span></h3>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
        }
    echo '</div>';


    // End display custom order calculation
    if ( ! empty( $custom_text_field ) ) {
        echo '<div class="product-custom-field">';
        echo '<h2>' . __( 'Custom Text', 'woocommerce' ) . '</h2>';
        echo '<p>' . esc_html( $custom_text_field ) . '</p>';
        echo '</div>';
    }

    if ( ! empty( $custom_number_field ) ) {
        echo '<div class="product-custom-field">';
        echo '<h2>' . __( 'Custom Number', 'woocommerce' ) . '</h2>';
        echo '<p>' . esc_html( $custom_number_field ) . '</p>';
        echo '</div>';
    }
}
add_action( 'woocommerce_single_product_summary', 'display_custom_product_data_fields', 25 );

function wc_custom_add_to_cart_button() {
    global $post;

    // Get the custom field values
    $per_box_price = get_post_meta( $post->ID, '_price_for_per_area', true );
    $area_symbol = get_post_meta( $post->ID, '_area_symbol', true );
    $area_per_box = get_post_meta( $post->ID, '_area_per_box', true );

    if ( ! empty( $per_box_price ) || ! empty( $area_symbol ) || ! empty( $area_per_box ) ) {
        // Remove the default add to cart button
        // remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		function add_custom_inline_css_for_variable_product() {
            
            echo '<style>
                .woocommerce-variation-add-to-cart.variations_button.woocommerce-variation-add-to-cart-disabled,
				.woocommerce-variation-add-to-cart.variations_button.woocommerce-variation-add-to-cart-enabled
				{					
                    opacity: 0;
                    height: 0px;
					visibility: hidden;
                }
            </style>';
            
        }
        add_action('wp_head', 'add_custom_inline_css_for_variable_product');
        // Add custom add to cart button
        add_action( 'woocommerce_single_product_summary', 'wc_show_custom_add_to_cart_button', 30 );
    }
}
add_action( 'wp', 'wc_custom_add_to_cart_button' );

// Function to display custom add to cart button
function wc_show_custom_add_to_cart_button() {
    global $product;
    echo '<button type="button" id="spAddToCart" class="single_add_to_cart_button button alt" data-productid="'.esc_attr($product->get_id()) .'">Add to Cart</button>';
}

// AJAX handler for custom add to cart
function wc_sp_add_to_cart() {
    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
    $quantity = apply_filters('woocommerce_stock_amount', absint($_POST['quantity']));
	$variationId = absint($_POST['variation_id']);
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variationId);

    if ($passed_validation) {
		if(!empty($variationId)){
			WC()->cart->add_to_cart($product_id, $quantity, $variationId);
		} else {
			WC()->cart->add_to_cart($product_id, $quantity);
		}
		
        do_action('woocommerce_ajax_added_to_cart', $product_id);
        
        if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
            wc_add_to_cart_message(array($product_id), true);
        }

        WC_AJAX::get_refreshed_fragments();
    } else {
        $data = array(
            'error' => true,
            'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id),
            'product_id' => $product_id,
            'quantity' => $quantity
        );
        wp_send_json($data);
    }

    wp_die();
}
add_action('wp_ajax_sp_add_to_cart', 'wc_sp_add_to_cart');
add_action('wp_ajax_nopriv_sp_add_to_cart', 'wc_sp_add_to_cart');
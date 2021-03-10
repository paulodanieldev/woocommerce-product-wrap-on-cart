<?php
/**
 * Plugin's main class
 *
 * @package Pwc_For_WooCommerce
 */

/**
 * WooCommerce bootstrap class.
 */
class WC_PWC {

	/**
	 * Initialize the plugin public actions.
	 */
	public static function init() {
		// Checks if WooCommerce is installed.
		if ( class_exists( 'WC_Payment_Gateway' ) ) {
			self::includes();

			//add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wc_pwc_load_scripts'));
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'wc_pwc_load_scripts'), 999);
			
			add_filter( 'plugin_action_links_' . plugin_basename( WC_PWC_PLUGIN_FILE ), array( __CLASS__, 'plugin_action_links' ) );

			// Register the integration.
			add_filter( 'woocommerce_integrations', array( __CLASS__, 'add_integration' ) );

            // Create shortcodes
            add_shortcode('hide_wrap_like_product', array( __CLASS__, 'hide_wrap_like_product')); 
            add_shortcode('add_wrap_checkbox_field', array( __CLASS__, 'add_wrap_checkbox_field')); 
            add_shortcode('add_wrap_cart_header', array( __CLASS__, 'add_wrap_cart_header')); 

			// Create the new order column		
            add_action( 'woocommerce_before_calculate_totals', array( __CLASS__, 'add_wrap_checkbox_data_in_session'), 11, 1);

            // Show the wrap fields on checkout
            add_filter( 'woocommerce_get_item_data', array( __CLASS__, 'ic_display_wrap_fields_text_on_cart'), 10, 2 );
            add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__, 'ic_add_wrap_field_text_to_order_items'), 10, 4 );

            // AJAX - REMOVE PRODUTO DO CARRINHO
            add_action('wp_ajax_woocommerce_ajax_add_to_cart', array( __CLASS__, 'woocommerce_ajax_add_to_cart'));
            add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', array( __CLASS__, 'woocommerce_ajax_add_to_cart'));

            // AJAX - ADICIONA PRODUTO AO CARRINHO
            add_action('wp_ajax_woocommerce_ajax_remove_from_cart', array( __CLASS__, 'woocommerce_ajax_remove_from_cart'));
            add_action('wp_ajax_nopriv_woocommerce_ajax_remove_from_cart', array( __CLASS__, 'woocommerce_ajax_remove_from_cart'));
		
            // AJAX - ADICIONA PRODUTO AO CARRINHO
            add_action('wp_ajax_woocommerce_ajax_remove_from_cart2', array( __CLASS__, 'woocommerce_ajax_remove_from_cart2'));
            add_action('wp_ajax_nopriv_woocommerce_ajax_remove_from_cart2', array( __CLASS__, 'woocommerce_ajax_remove_from_cart2'));

            // exclui embalagem após produto
            // add_action( 'woocommerce_cart_item_removed', array( __CLASS__,'action_woocommerce_cart_item_removed'), 10, 1); 

            //insere os métodos java scripts no footer
            add_action( 'wp_footer', array( __CLASS__, 'ic_add_wrap_to_cart_js') , 99);

            //oculta a categoria embalagem
            add_action( 'pre_get_posts', array( __CLASS__, 'ic_exclude_product_category') );

            //adiciona o css da mensagem de baixa de estoque
            add_action( 'wp_head', array( __CLASS__, 'ic_wrap_css_message') );

            add_filter( 'woocommerce_add_cart_item_data', array( __CLASS__, 'ic_force_individual_cart_items'), 10, 2 );
            
        } else {
			add_action( 'admin_notices', array( __CLASS__, array( __CLASS__, 'woocommerce_missing_notice' )) );
		}
	}


	/**
     * Add a new integration to WooCommerce.
     */
    public static function add_integration( $integrations ) {
		$integrations[] = 'WC_Integration_PWC';
		return $integrations;
	}


	/**
	 * Set Script files.
	 */
	public static function wc_pwc_load_scripts(){
		// load the main css scripts file
		wp_enqueue_style( 'wc-pwc-styles-css', plugins_url( '/css/styles.css', __FILE__ ) );
		
		// load the main js scripts file
		wp_enqueue_script( 'wc-pwc-main-js', plugins_url( '/js/main.js', __FILE__ ), array(), '1.0.0', true );
	}

	/**
	 * Action links.
	 *
	 * @param array $links Action links.
	 *
	 * @return array
	 */
	public static function plugin_action_links( $links ) {
		$plugin_links   = array();
		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=integration&section=pwc' ) ) . '">Configuração</a>';

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Includes.
	 */
	private static function includes() {
		include_once dirname( __FILE__ ) . '/class-wc-pwc-integration.php';
		include_once dirname( __FILE__ ) . '/class-wc-pwc-methods.php';
	}

	/**
	 * WooCommerce missing notice.
	 */
	public static function woocommerce_missing_notice() {
		include dirname( __FILE__ ) . '/admin/views/html-notice-missing-woocommerce.php';
	}

	/**
	 * Create a shortcode to hide wrap like products on the cart
	 */

    public static function hide_wrap_like_product($atts){
        $pwc_methods = new WC_Methods_PWC();
        return $pwc_methods->is_wrap_product($atts);
    }
    

    /**
     * Create a shortcode to add a new cart column
     */

    public static function add_wrap_checkbox_field($atts){
        $pwc_methods = new WC_Methods_PWC();
        return $pwc_methods->wrap_checkbox_field($atts);
    }

    public static function add_wrap_cart_header($atts){
        $pwc_methods = new WC_Methods_PWC();
        return $pwc_methods->wrap_cart_header($atts);
    }

    public static function add_wrap_checkbox_data_in_session($cart){
        $pwc_methods = new WC_Methods_PWC();
        $pwc_methods->set_wrap_checkbox_data_in_session($cart);
    }

    public static function ic_display_wrap_fields_text_on_cart($item_data, $cart_item){
        $pwc_methods = new WC_Methods_PWC();
        return $pwc_methods->display_wrap_fields_text_on_cart($item_data, $cart_item);
    }

    public static function ic_add_wrap_field_text_to_order_items($item, $cart_item_key, $values, $order){
        $pwc_methods = new WC_Methods_PWC();
        $pwc_methods->add_wrap_field_text_to_order_items($item, $cart_item_key, $values, $order);
    }
            
    public static function woocommerce_ajax_add_to_cart() {
        ob_start();

        $wrap_product_id = $_POST['ic_product_id'];
        $result = array();
        //global $product;

        $wrap_check_qty = self::get_qty_wrap_checked($wrap_product_id);
        $product_wrap_obj = wc_get_product( $wrap_product_id ); 
        // var_dump(($wrap_check_qty +1));
        // var_dump($product_wrap_obj->get_stock_quantity());
        if ($product_wrap_obj->get_stock_quantity() >= ($wrap_check_qty +1) ){
            $resultado = WC()->cart->add_to_cart($wrap_product_id);
            $result = $resultado != false ? ['response' => true, 'mensagem' => $resultado] : ['response' => false, 'mensagem' => $resultado];
            //do_action( 'woocommerce_ajax_added_to_cart', $wrap_product_id );
        }
        echo json_encode($result);
        wp_die();
    }
            
    public static function woocommerce_ajax_remove_from_cart() {
        ob_start();
        //if ( is_admin() ) return;
        $wrap_product_id = $_POST['ic_product_id'];
        $specific_ids = array($wrap_product_id);
        $result = array();
        // Checking cart items
        foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $product_id = $cart_item['data']->get_id();
            // Check for specific product IDs and change quantity
            if( in_array( $product_id, $specific_ids )){
                $new_qty = $cart_item['quantity'] -1;
                $resultado = WC()->cart->set_quantity( $cart_item_key, $new_qty ); // Change quantity
                $result = $resultado != false ? ['response' => true, 'mensagem' => $resultado] : ['response' => false, 'mensagem' => $resultado];
                break;
            }
        }
        //do_action( 'woocommerce_ajax_added_to_cart', $wrap_product_id );
        echo json_encode($result);
        wp_die();
    }

    public static function woocommerce_ajax_remove_from_cart2() {
        // //if ( is_admin() ) return;
        $cart_item_key = $_POST['cart_item_key'];
    
        if ( ! empty( $_REQUEST['cart'] ) ) {
            $lorem_price = $_REQUEST['cart'];
        }
    
        if ( empty( $lorem_price ) ) {
            $lorem_price = WC()->session->get( 'my_lorem_price' );      // fetch all checkboxes information from session
        }
        
        $result = array();
        // remove o produto filho
        if ( isset( $lorem_price[ $cart_item_key ]['lorem'] ) ) {
            //var_dump( $lorem_price, $lorem_price[ $cart_item_key ]['lorem'] , $cart_item_key);
            $cart_item = WC()->cart->get_cart_item($cart_item_key);
            if ($cart_item){
                $product_id = $cart_item['data']->get_id();
                $pwc_methods = new WC_Methods_PWC();
                $pwc_acf_wrap_field_name = $pwc_methods->get_pwc_acf_wrap_field_name();
                $product_wrap_id = (int)get_post_meta($product_id, $pwc_acf_wrap_field_name, true)[0];

                foreach( WC()->cart->get_cart() as $cart_item_key_w => $cart_item_w ) {
                    if($cart_item_w['data']->get_id() == $product_wrap_id){
                        $new_qty = $cart_item['quantity'] -1;
                        $resultado = WC()->cart->set_quantity( $cart_item_key_w, $new_qty ); // Change quantity
                        $result = $resultado != false ? ['response' => true, 'mensagem' => $resultado] : ['response' => false, 'mensagem' => $resultado];
                        break;
                    }
                }

                //do_action( 'woocommerce_ajax_added_to_cart', $product_wrap_id );
            }

            $new_lorem_price = $lorem_price;
            var_dump($new_lorem_price);
            unset($new_lorem_price[ $cart_item_key ]['lorem']);
            var_dump($new_lorem_price);
            WC()->session->set( 'my_lorem_price', $new_lorem_price);
            
        }else{
            $result = ['response' => false, 'mensagem' => 'sem embalagem'];
        }
        // remove o produto pai
        WC()->cart->set_quantity( $cart_item_key, 0 );
        echo json_encode($result);
        wp_die();
    }

    private static function get_qty_wrap_checked($product_wrap_id){
        global $woocommerce; 
        $lorem_price = null;
        $qtd_checked = 0;

        if ( ! empty( $_REQUEST['cart'] ) ) {
            $lorem_price = $_REQUEST['cart'];
        }

        if ( empty( $lorem_price ) ) {
            $lorem_price = WC()->session->get( 'my_lorem_price' );      // fetch all checkboxes information from session
        }

        foreach ( $woocommerce->cart->cart_contents as $cart_item_key => $cart_item ) {
            if ( isset( $lorem_price[ $cart_item_key ]['lorem'] ) ) {
                $cart_item_obj = wc_get_product($cart_item['data']);
                $product_wrap_array_id = get_post_meta($cart_item_obj->get_id(), 'embalagem_produto', true);
                if ($product_wrap_array_id[0] == $product_wrap_id){
                    $qtd_checked ++;
                }
            }
        }
        
        return $qtd_checked;
    }

    public static function ic_add_wrap_to_cart_js(){
        if (is_cart()) {
            ?>
            <script>
            function ic_add_wrap_to_cart(wrap_id, wrap_el){
                //jQuery.blockUI({message: null, overlayCSS: { backgroundColor: '#fff'} });
                console.log('adiciona');
                jQuery.post('/lojateste.com.br/wp-admin/admin-ajax.php', {
                action: 'woocommerce_ajax_add_to_cart',
                ic_product_id: wrap_id
                }, function(data){
                    // console.log(data);
                    data = JSON.parse(data);
                    console.log(data.response);
                    // console.log(data, data.response, typeof data.response);
                    // jQuery.unblockUI();
                    if (data.response){
                        jQuery("[name='update_cart']").prop("disabled", false);
                        jQuery("[name='update_cart']").trigger("click");
                    }else{
                        wrap_el.checked = false;
                        let divId = wrap_el.getAttribute("name").split('cart')[1].split('[')[1].replace("]", "");
                        jQuery('div#'+divId).show();
                        setTimeout(function(){ jQuery('div#'+divId).fadeOut("slow"); }, 5000);
                    }
                })
            }

            function ic_remove_wrap_from_cart(wrap_id){
                //jQuery.blockUI({message: null, overlayCSS: { backgroundColor: '#fff'} });
                console.log('remove');
                
                jQuery.post('/lojateste.com.br/wp-admin/admin-ajax.php', {
                action: 'woocommerce_ajax_remove_from_cart',
                ic_product_id: wrap_id
                }, function(data){
                    // console.log(data);
                    data = JSON.parse(data);
                    console.log(data.response);
                    // console.log(data, data.response, typeof data.response);
                    //jQuery.unblockUI();
                    jQuery("[name='update_cart']").prop("disabled", false);
                    jQuery("[name='update_cart']").trigger("click");
                })
            }

            function ic_remove_wrap_from_cart2(item_key){
                //jQuery.blockUI({message: null, overlayCSS: { backgroundColor: '#fff'} });
                console.log('remove');
                jQuery.post('/lojateste.com.br/wp-admin/admin-ajax.php', {
                action: 'woocommerce_ajax_remove_from_cart2',
                cart_item_key: item_key
                }, function(data){
                    //data = JSON.parse(data);
                    console.log(data);
                    let wrap_elelemt = document.getElementsByName("cart["+ item_key +"][lorem]");
                    wrap_elelemt[0].checked = false;
                    jQuery("[name='update_cart']").prop("disabled", false);
                    jQuery("[name='update_cart']").trigger("click");
                })
            }
            
            function ic_change_wrap_checkbox(element_param){  
                if (element_param.checked){
                    ic_add_wrap_to_cart(element_param.getAttribute("id"), element_param);
                }else{
                    ic_remove_wrap_from_cart(element_param.getAttribute("id"));
                }
                
            }

            function ic_sanitize_wrap_input(x) {
                x.value = x.value.replace(/[^a-zA-Z\u00C0-\u00FF ]+/i, "");
            }
            
            </script>
            <?php
        }

    }

    public static function ic_wrap_css_message(){
        if (is_cart()) {
            ?>
            <style>
                .ic_wrap_alerta {
                    padding: 5px;
                    border: 1px solid gray !important;
                    border-radius: 5px;
                    margin-bottom: 5px;
                    font-size: 14px;
                    line-height: 1.5 !important;
                }
                .ic_wrap_error {
                    border-color: #e8273b !important;
                    color: #FFF !important;
                    background-color: #ed5565 !important;
                }
            </style>
            <?php
        }
    }

    public static function ic_exclude_product_category( $query ) {
        if ( is_admin() ) return;
        if ($query->is_main_query()) {

            $pwc_methods = new WC_Methods_PWC();
            $pwc_category_name = $pwc_methods->get_pwc_category_name();
            $tax_query = (array) $query->get('tax_query');
    
            $tax_query[] = array(
                   'taxonomy' => 'product_cat',
                   'field' => 'slug',
                   'terms' => array($pwc_category_name),
                   'operator' => 'NOT IN'
            );        
            
            $query->set('tax_query', $tax_query);        
    
        }
        return $query;
    }

    // FORÇA A ADIÇÃO DE MAIS DE UM IGUAL PARA PRODUTOS
    public static function ic_force_individual_cart_items( $cart_item_data, $product_id ) {

        $product_cats = wp_get_post_terms( $product_id, 'product_cat' );
        $single_cat = array_shift( $product_cats );

        $pwc_methods = new WC_Methods_PWC();
        $pwc_category_name_var = $pwc_methods->get_pwc_category_name();

        if ($single_cat->slug == $pwc_category_name_var) {
        	$unique_cart_item_key = md5( microtime() . rand() );
            $cart_item_data['unique_key'] = $unique_cart_item_key;
        }
        
    
        return $cart_item_data;
    }
	
}


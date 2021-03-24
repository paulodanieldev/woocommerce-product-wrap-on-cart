<?php
/**
 * PWC Integration.
 *
 * @package Pwc_For_WooCommerce
 */
if (! class_exists ( 'WC_Methods_PWC' )):
class WC_Methods_PWC extends WC_Integration {
	/**
	 * Init and hook in the integration.
	 */
	
	private $pwc_category_name         = null;
	public function __construct() {
		$this->id                 	                = 'pwc';
		$this->pwc_category_name			        = $this->get_option( 'pwc_category_name' );
        $this->pwc_acf_wrap_field_name		        = $this->get_option( 'pwc_acf_wrap_field_name' );
        $this->pwc_wrap_field_header_title          = $this->get_option( 'pwc_wrap_field_header_title' );
        $this->pwc_wrap_field_title	                = $this->get_option( 'pwc_wrap_field_title' );
        $this->pwc_show_gifted_field	            = $this->get_option( 'pwc_show_gifted_field' );
		$this->pwc_gifted_field_title	            = $this->get_option( 'pwc_gifted_field_title' );
		$this->pwc_show_sender_field	            = $this->get_option( 'pwc_show_sender_field' );
		$this->pwc_sender_field_title	            = $this->get_option( 'pwc_sender_field_title' );
		$this->pwc_out_of_stock_message_text	    = $this->get_option( 'pwc_out_of_stock_message_text' );
        $this->pwc_wrapless_message_text	        = $this->get_option( 'pwc_wrapless_message_text' );
        $this->pwc_hide_wrap_from_cart              = $this->get_option( 'pwc_hide_wrap_from_cart' );
        $this->pwc_show_wrap_details_on_checkout    = $this->get_option( 'pwc_show_wrap_details_on_checkout' );
        
    }
    
    public function get_pwc_category_name(){
        return $this->pwc_category_name;
    }

    public function get_pwc_acf_wrap_field_name(){
        return $this->pwc_acf_wrap_field_name;
    }
    
    public function get_pwc_wrap_field_header_title(){
        return $this->pwc_wrap_field_header_title;
    }

    public function get_pwc_wrap_field_title(){
        return $this->pwc_wrap_field_title;
    }

    public function get_pwc_show_gifted_field(){
        return $this->pwc_show_gifted_field;
    }

    public function get_pwc_gifted_field_title(){
        return $this->pwc_gifted_field_title;
    }

    public function get_pwc_show_sender_field(){
        return $this->pwc_show_sender_field;
    }

    public function get_pwc_sender_field_title(){
        return $this->pwc_sender_field_title;
    }

    public function get_pwc_out_of_stock_message_text(){
        return $this->pwc_out_of_stock_message_text;
    }

    public function get_pwc_wrapless_message_text(){
        return $this->pwc_wrapless_message_text;
    }
  
    public function get_pwc_hide_wrap_from_cart(){
        return $this->pwc_hide_wrap_from_cart;
    }

    public function is_wrap_product($atts){
        $result = false;
        if (!empty($this->pwc_hide_wrap_from_cart)){
            if ($this->pwc_hide_wrap_from_cart == 'yes'){
                $product_cats = wp_get_post_terms( $atts['product_id'], 'product_cat' );
                $single_cat = array_shift( $product_cats );
                
                if ($single_cat->slug == $this->pwc_category_name) {
                    $result = true;
                }
            }
        }
        return $result;
    }

    public function wrap_cart_header($atts){
        $titulo_cabecalho = !empty($this->pwc_wrap_field_header_title) ? $this->pwc_wrap_field_header_title : __('Adicionar Embalagem', 'pwc-integration');
        $html = '<th class="ic-product-wrap">'. $titulo_cabecalho .'</th>';
        return $html;
    }

    public function wrap_checkbox_field($atts){

        $elements = WC()->session->get( 'my_lorem_price');
        $cart_item_key = $atts['cart_item_key'];
        $product_wrap_id = get_post_meta($atts['product_id'], $this->pwc_acf_wrap_field_name, true);
        // checks if there is product packaging related to it 
        if((int)$product_wrap_id[0] > 0){
            $product_wrap_obj = wc_get_product( $product_wrap_id[0] ); 
            $product_wrap_title = $product_wrap_obj->get_title();
            // VERIFICA SE POSSUI EMBALAGEM NO ESTOQUE
            if ($product_wrap_obj->managing_stock() && $product_wrap_obj->is_in_stock()){
                $product_wrap_value = (float)$product_wrap_obj->get_price() ?? 0;
                $product_wrap_value_html = $product_wrap_obj->get_price_html() ?? 'R$0,00';
                $product_wrap_id = $product_wrap_obj->get_id();   
                //tooltip doc - https://gist.github.com/gdometrics/fc5e03a6f05845746a18
                $tooltip_wrap_img = '<span style="padding: 5px 10px;" class="ctooltip" data-avia-tooltip-class="av-tt-pos-right av-tt-align-centered" data-avia-tooltip-position="right" data-avia-tooltip-alignment="left" data-avia-tooltip="<img src=&quot;'. get_the_post_thumbnail_url($product_wrap_id) .'&quot;> ">
                                        <i class="fa fa-picture-o"></i>
                                    </span>';
                $titulo_checkbox = !empty($this->pwc_wrap_field_title) ? str_replace(["{wrap_name}", "{wrap_price}"], [$product_wrap_title, $product_wrap_value_html], $this->pwc_wrap_field_title) : 'Adicionar '.$product_wrap_title.' por '.$product_wrap_value_html;
                $html = '<div class="ic_wrap_alerta ic_wrap_error" style="display:none" id="'.$cart_item_key.'">Embalagem com estoque insuficiente.</div>';
                $html .= sprintf( '<div class="lorem wrap_select"><input style="width: 20px !important;margin-top: -5px;" type="checkbox" name="cart[%s][lorem]" id="'. $product_wrap_id .'" value="%s" size="4"  class="ic-wrap-checkbox" onchange="ic_change_wrap_checkbox(this)" %s />'.$tooltip_wrap_img.' '.$titulo_checkbox.'</div>', $cart_item_key, esc_attr( $values['url'] ), esc_attr( isset( $elements[$cart_item_key]['lorem'] ) ? 'checked' : '' ) );
                // $html .= '<div> <button type="button" onclick="ic_add_wrap_to_cart('. $product_wrap_id .')">adiciona!</button> <button type="button" onclick="ic_remove_wrap_from_cart('. $product_wrap_id .')">remove</button> </div>';
                // $html = sprintf( '<div class="lorem wrap_select"><input type="checkbox" name="cart[%s][lorem]" value="%s" size="4"  class="url text" %s /> Embalar para presente: R$'.$wrapCost.'</div>', $cart_item_key, esc_attr( $values['url'] ), esc_attr( isset( $elements[$cart_item_key]['lorem'] ) ? 'checked' : '' ) );
                
                if(isset( $elements[$cart_item_key]['lorem'] )){
                    //$html .= sprintf( '<div class="lorem"><div class="label_gift_name">Nome do presenteado  <span class="ctooltip" data-avia-tooltip="Em caso de vários presentes no mesmo pedido, escreveremos o nome em uma etiqueta para você poder identificar o pacote"><i class="fa fa-info-circle"></i></span></div><div class="gift_input"><input type="text" name="cart[%s][lorem_name]" value="%s" class="url text lorem_name" placeholder="Digite o nome" /> </div></div>', $cart_item_key, esc_attr( isset( $elements[$cart_item_key]['lorem_name'] ) ? $elements[$cart_item_key]['lorem_name'] : '' ) );
                    $html .= '<div class="lorem" style="text-align: left; padding-left: 10px;">';
                    if ($this->pwc_show_gifted_field == "yes"){
                        $nome_presenteado = !empty($this->pwc_gifted_field_title) ? $this->pwc_gifted_field_title : __('Nome do presenteado', 'pwc-integration');
                        $html .= sprintf( '<div class="label_gift_name" style="text-align: left; padding-left: 10px;">
                                                '. $nome_presenteado .'  
                                                <span class="ctooltip" data-avia-tooltip="'. __( 'Na etiqueta ‘DE/PARA’ podemos escrever o nome do PRESENTEADO e também o
                                                nome de quem está dando o presente (REMETENTE). 
                                                Lembre-se que a Nota Fiscal irá sempre junto com o nome de quem comprou', 'pwc-integration' ) . '."><i class="fa fa-info-circle"></i></span>
                                            </div>
                                            <div class="gift_input" style="padding-left: 10px; width: auto;">
                                                <input type="text" maxlength="20" onkeyup="ic_sanitize_wrap_input(this)" name="cart[%s][lorem_name]" value="%s" class="url text lorem_name" placeholder="Digite o nome" />
                                            </div>', $cart_item_key, esc_attr( isset( $elements[$cart_item_key]['lorem_name'] ) ? $elements[$cart_item_key]['lorem_name'] : '' ) );
                    }
                    if ($this->pwc_show_sender_field == "yes"){
                        $nome_remetente = !empty($this->pwc_sender_field_title) ? $this->pwc_sender_field_title : __('Nome do Remetente', 'pwc-integration');
                        $html .= sprintf( '<div class="label_gift_name" style="text-align: left; padding-left: 10px;">
                                                '. $nome_remetente .'  
                                            </div>
                                            <div class="gift_input" style="padding-left: 10px; width: auto;">
                                                <input type="text" maxlength="20" onkeyup="ic_sanitize_wrap_input(this)" name="cart[%s][lorem_rem_name]" value="%s" class="url text lorem_rem_name" placeholder="Digite o nome" />
                                            </div>', $cart_item_key, esc_attr( isset( $elements[$cart_item_key]['lorem_rem_name'] ) ? $elements[$cart_item_key]['lorem_rem_name'] : '' ) );
                    }
                    $html .= '</div>';
                }
            }else {
                $msg = !empty($this->pwc_out_of_stock_message_text) ? str_replace("{wrap_name}", $product_wrap_title, $this->pwc_out_of_stock_message_text) : 'Estamos sem '.$product_wrap_title.' no momento.';
                $html = '<div class="lorem">'. $msg .'</div>';
            }
        }else{
            $msg = !empty($this->pwc_wrapless_message_text) ? $this->pwc_wrapless_message_text : __('Opção de embalagem presente não disponível', 'pwc-integration');
            $html = '<div class="lorem">'. $msg .'</div>';
        }
        $result = $html;
        return $result;
    }
    
    public function set_wrap_checkbox_data_in_session( $cart ) {
      if ( ! empty( $cart->cart_contents ) ) {
        if ( ! empty( $_REQUEST['cart'] ) ) {
          // check if any of the checkboxes is checked and set all checkboxes information in session
          WC()->session->set( 'my_lorem_price', $_REQUEST['cart'] );
        }
      }
    }

    public function display_wrap_fields_text_on_cart( $item_data, $cart_item ) {
        if (is_checkout()){
            if ($this->pwc_show_wrap_details_on_checkout == 'yes'){
                $elements = WC()->session->get( 'my_lorem_price');
                $checked = esc_attr( isset( $elements[$cart_item['key']]['lorem'] ) ? 'checked' : '' );
                $name = esc_attr(isset($elements[$cart_item['key']]['lorem_name']) ? $elements[$cart_item['key']]['lorem_name'] : '');
                $rem_name = esc_attr(isset($elements[$cart_item['key']]['lorem_rem_name']) ? $elements[$cart_item['key']]['lorem_rem_name'] : '');

                if ( !empty( $checked ) ) {
                    $item_data[] = array(
                        'key'     => __( 'Embrulhar para presente', 'pwc-integration' ),
                        'value'   => wc_clean('Sim'),
                        'display' => '',
                    );
                    // verifica se exibe informações do presenteado
                    if ($this->pwc_show_gifted_field == "yes"){
                        if ( !empty( $name ) ) {
                            $item_data[] = array(
                            'key'     => __( 'Presenteado', 'pwc-integration' ),
                            'value'   => wc_clean($name),
                            'display' => '',
                            );
                        }
                    }
                    if ($this->pwc_show_sender_field == "yes"){
                        if ( !empty( $rem_name ) ) {
                            $item_data[] = array(
                            'key'     => __( 'Remetente', 'pwc-integration' ),
                            'value'   => wc_clean($rem_name),
                            'display' => '',
                            );
                        }
                    }
                }
            }
        }
        return $item_data;
    }

    public function add_wrap_field_text_to_order_items( $item, $cart_item_key, $values, $order ) {
        
        $elements = WC()->session->get('my_lorem_price');
        $checked = esc_attr( isset( $elements[$cart_item_key]['lorem'] ) ? 'checked' : '' );
        $name = esc_attr( isset( $elements[$cart_item_key]['lorem_name'] ) ? $elements[$cart_item_key]['lorem_name'] : '' );
        $rem_name = esc_attr( isset( $elements[$cart_item_key]['lorem_rem_name'] ) ? $elements[$cart_item_key]['lorem_rem_name'] : '' );

        if ( empty( $checked ) ) {
            return;
        }
        if ($checked) {
            $item->add_meta_data( __( 'Embalagem para presente', 'pwc-integration' ), 'Sim' );
        }
        if ($name){
            $item->add_meta_data( __( 'Presenteado', 'pwc-integration' ), $name );
        }
        if ($rem_name){
            $item->add_meta_data( __( 'Remetente', 'pwc-integration' ), $rem_name );
        }

        $order->add_order_note($item->get_name()." - Embalagem Adicionada");
        
    }

}
endif ;
<?php
/**
 * PWC Integration.
 *
 * @package Pwc_For_WooCommerce
 */
if (! class_exists ( 'WC_Integration_PWC' )):
class WC_Integration_PWC extends WC_Integration {
	/**
	 * Init and hook in the integration.
	 */
	
	public function __construct() {
		global $woocommerce;
		$this->id                 = 'pwc';
		$this->method_title       = __( 'Wrap like product on cart', 'pwc-integration' );
		$this->method_description = __( 'The following options are used to configure the plugin', 'pwc-integration' );
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		// Define user set variables.
		$this->pwc_category_name	= $this->get_option( 'pwc_category_name' );
		$this->pwc_acf_wrap_field_name	= $this->get_option( 'pwc_acf_wrap_field_name' );

		$this->pwc_wrap_field_title	= $this->get_option( 'pwc_wrap_field_title' );

		$this->pwc_show_gifted_field	= $this->get_option( 'pwc_show_gifted_field' );
		$this->pwc_gifted_field_title	= $this->get_option( 'pwc_gifted_field_title' );

		$this->pwc_show_sender_field	= $this->get_option( 'pwc_show_sender_field' );
		$this->pwc_sender_field_title	= $this->get_option( 'pwc_sender_field_title' );

		$this->pwc_out_of_stock_message_text	= $this->get_option( 'pwc_out_of_stock_message_text' );
		$this->pwc_wrapless_message_text	= $this->get_option( 'pwc_wrapless_message_text' );

		// Actions.
		add_action('woocommerce_update_options_integration_' . $this->id, array($this, 'process_admin_options'));
	}
	/**
	 * Initialize integration settings form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'pwc_category_name' => array(
				'title'             => __( 'Nome da categoria de embalagens', 'pwc-integration' ),
				'type'              => 'text',
				'label'             => __( 'Nome da categoria de embalagens', 'pwc-integration' ),
				'default'           => '',
				'description'       => __( 'Entre com o nome da categoria de embalagens.', 'pwc-integration' ),
				'desc_tip'          => true
			),
			'pwc_acf_wrap_field_name' => array(
				'title'             => __( 'Nome do campo de embalagens em ACF', 'pwc-integration' ),
				'type'              => 'text',
				'label'             => __( 'Nome do campo de embalagens em ACF', 'pwc-integration' ),
				'default'           => '',
				'description'       => __( 'Entre com o nome do campo de embalagens criado em Advance Custon Fields (Campos Personalizados).', 'pwc-integration' ),
				'desc_tip'          => true
			),
			'pwc_wrap_field_title' => array(
				'title'             => __( 'Titulo do checkbox de embalagem', 'pwc-integration' ),
				'type'              => 'text',
				'label'             => __( 'Titulo do checkbox de embalagem', 'pwc-integration' ),
				'default'           => '',
				'description'       => __( 'Entre com o Titulo do campo de embalagens do carrinho, utilize a variavel {wrap_name} para exibir o nome da embalagem e {wrap_price} para exibir o valor da mesma.', 'pwc-integration' ),
				'desc_tip'          => true
			),
			'pwc_show_gifted_field' => array(
				'title'             => __( 'Exibir campo Nome do presenteado?', 'pwc-integration' ),
				'type'              => 'checkbox',
				'label'             => __( 'sim', 'pwc-integration' ),
				'class'				=> 'ic_pwc_show_gifted_field',
				'default'           => '',
				'description'       => __( 'Se marcado, exibirá um campo de texto para inserir o nome do presenteado.', 'pwc-integration' ),
				'desc_tip'          => true
			),
			'pwc_gifted_field_title' => array(
				'title'             => __( 'Titulo do campo Nome do Presenteado', 'pwc-integration' ),
				'type'              => 'text',
				'label'             => __( 'Titulo do campo Nome do Presenteado', 'pwc-integration' ),
				'default'           => '',
				'description'       => __( 'Entre com o titulo do campo de Nome do Presenteado que aparecerá no carrinho.', 'pwc-integration' ),
				'desc_tip'          => true
			),
			'pwc_show_sender_field' => array(
				'title'             => __( 'Exibir campo Nome do remetente?', 'pwc-integration' ),
				'type'              => 'checkbox',
				'label'             => __( 'sim', 'pwc-integration' ),
				'class'				=> 'ic_pwc_show_sender_field',
				'default'           => '',
				'description'       => __( 'Se marcado, exibirá um campo de texto para inserir o nome do remetente.', 'pwc-integration' ),
				'desc_tip'          => true
			),
			'pwc_sender_field_title' => array(
				'title'             => __( 'Titulo do campo Nome do Remetente', 'pwc-integration' ),
				'type'              => 'text',
				'label'             => __( 'Titulo do campo Nome do Remetente', 'pwc-integration' ),
				'default'           => '',
				'description'       => __( 'Entre com o titulo do campo de Nome do Remetente que aparecerá no carrinho.', 'pwc-integration' ),
				'desc_tip'          => true
			),
			'pwc_out_of_stock_message_text' => array(
				'title'             => __( 'Texto da mensagem de embalagem sem estoque', 'pwc-integration' ),
				'type'              => 'text',
				'label'             => __( 'Texto da mensagem de embalagem sem estoque', 'pwc-integration' ),
				'default'           => '',
				'description'       => __( 'Entre com o texto da mensagem que aparecerá quando não possuir estoque de embalagem suficiente para todos os produtos no carrinho, a variável {wrap_name} será substituida pelo nome do produto embalagem', 'pwc-integration' ),
				'desc_tip'          => true
			),
			'pwc_wrapless_message_text' => array(
				'title'             => __( 'Texto da mensagem Embalagem não disponível', 'pwc-integration' ),
				'type'              => 'text',
				'label'             => __( 'Texto da mensagem Embalagem não disponível', 'pwc-integration' ),
				'default'           => '',
				'description'       => __( 'Entre com o texto da mensagem que aparecerá quando o produto não possuir embalagem configurada', 'pwc-integration' ),
				'desc_tip'          => true
			),
		);
	}

}
endif ;
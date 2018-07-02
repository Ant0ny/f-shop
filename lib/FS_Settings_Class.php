<?php

namespace FS;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Класс выводит страницу настроек в админке
 */
class FS_Settings_Class {

	private $settings_page = 'fast-shop-settings';

	public function __construct() {
		add_action( 'admin_menu', array( &$this, 'add_menu' ) );
		add_action( 'admin_init', array( &$this, 'init_settings' ) );
	}

	/**
	 * метод содержит массив базовых настроек плагина
	 * @return array|mixed|void
	 */
	function register_settings() {
		global $fs_config;
		$settings = array(
			'general'    => array(
				'name'   => __( 'Главное', 'fast-shop' ),
				'fields' => array(
					array(
						'type'  => 'checkbox',
						'name'  => 'discounts_on',
						'label' => 'Включить систему скидок',
						'help'  => 'вы сможете показать лояльное отношение к клиенту и таким способом привлечь покупателей',
						'value' => fs_option( 'discounts_on' )
					),
					array(
						'type'  => 'checkbox',
						'name'  => 'multi_currency_on',
						'label' => 'Включить мультивалютность',
						'help'  => 'Если вы планируете, чтобы стоимость товара конвертировалась автоматически по установленному курсу',
						'value' => fs_option( 'multi_currency_on' )
					)

				)


			),
			'shoppers'   => array(
				'name'   => __( 'Покупатели', 'fast-shop' ),
				'fields' => array(
					array(
						'type'  => 'checkbox',
						'name'  => 'autofill',
						'label' => 'Заполнять данные пользователя автоматически',
						'help'  => 'используется при оформлении заказа, если пользователь авторизован',
						'value' => fs_option( 'autofill' )
					),
					array(
						'type'  => 'checkbox',
						'name'  => 'auto_registration',
						'label' => 'Регистрировать пользователя при покупке',
						'help'  => 'каждый зарегистрированный пользователь получит доступ к личному кабинету, сможет увидеть купленные товары и прочие привилегии',
						'value' => fs_option( 'auto_registration' )
					)

				)


			),
			'currencies' => array(
				'name'   => __( 'Currencies', 'fast-shop' ),
				'fields' => array(
					array(
						'type'  => 'text',
						'name'  => 'currency_symbol',
						'label' => 'Знак валюты <span>(по умолчанию отображается $):</span>',
						'value' => fs_option( 'currency_symbol', '$' )
					),
					array(
						'type'  => 'text',
						'name'  => 'currency_delimiter',
						'label' => 'Разделитель цены <span>(по умолчанию .)</span>',
						'value' => fs_option( 'currency_delimiter', '.' )
					),
					array(
						'type'  => 'checkbox',
						'name'  => 'price_cents',
						'label' => 'Использовать копейки?',
						'value' => fs_option( 'price_cents', '0' )
					),
					array(
						'type'  => 'checkbox',
						'name'  => 'price_conversion',
						'label' => 'Конвертация стоимости товара в зависимости от языка',
						'help'=>'Если выбрано, то цена будет автоматически конвертироваться в необходимую валюту. Важно! Для того, чтобы это сработало необходимо указать локаль в настрйках валюты.',
						'value' => fs_option( 'price_conversion', '0' )
					),
				)
			),
			'letters'    => array(
				'name'   => __( 'Letters', 'fast-shop' ),
				'fields' => array(
					array(
						'type'  => 'text',
						'name'  => 'manager_email',
						'label' => 'Куда отправлять письма',
						'help'  => 'можно указать несколько адресатов через запятую',
						'value' => fs_option( 'manager_email', get_option( 'admin_email' ) )
					),
					array(
						'type'  => 'text',
						'name'  => 'site_logo',
						'label' => 'Ссылка на изображение логотипа',
						'value' => fs_option( 'site_logo' )
					),
					array(
						'type'  => 'email',
						'name'  => 'email_sender',
						'label' => 'Email отправителя писем',
						'value' => fs_option( 'email_sender' )
					),
					array(
						'type'  => 'text',
						'name'  => 'name_sender',
						'label' => 'Название отправителя писем',
						'value' => fs_option( 'name_sender', get_bloginfo( 'name' ) )
					),
					array(
						'type'  => 'text',
						'name'  => 'customer_mail_header',
						'label' => 'Заголовок письма заказчику',
						'value' => fs_option( 'customer_mail_header', 'Заказ товара на сайте «' . get_bloginfo( 'name' ) . '»' )
					),
					array(
						'type'  => 'editor',
						'name'  => 'customer_mail',
						'label' => 'Текст письма заказчику после отправки заказа',
						'value' => fs_option( 'customer_mail' )
					),
					array(
						'type'  => 'editor',
						'name'  => 'admin_mail',
						'label' => 'Текст письма администратору после отправки заказа',
						'value' => fs_option( 'admin_mail' )
					),
				)


			),
			'pages'      => array(
				'name'   => __( 'Page', 'fast-shop' ),
				'fields' => array(
					array(
						'type'  => 'pages',
						'name'  => 'page_cart',
						'label' => 'Страница корзины',
						'value' => fs_option( 'page_cart', 0 )
					),
					array(
						'type'  => 'pages',
						'name'  => 'page_checkout',
						'label' => 'Страница оформление покупки',
						'value' => fs_option( 'page_checkout', 0 )
					),
					array(
						'type'  => 'pages',
						'name'  => 'page_payment',
						'label' => 'Страница оплаты',
						'value' => fs_option( 'page_payment', 0 )
					),
					array(
						'type'  => 'pages',
						'name'  => 'page_success',
						'label' => 'Страница успешной отправки заказа',
						'value' => fs_option( 'page_success', 0 )
					),
					array(
						'type'  => 'pages',
						'name'  => 'page_whishlist',
						'label' => 'Страница списка желаний',
						'value' => fs_option( 'page_whishlist', 0 )
					),
					array(
						'type'  => 'pages',
						'name'  => 'page_cabinet',
						'label' => 'Страница личного кабинета',
						'value' => fs_option( 'page_cabinet', 0 )
					),
					array(
						'type'  => 'pages',
						'name'  => 'page_auth',
						'label' => 'Страница авторизации',
						'value' => fs_option( 'page_auth', 0 )
					),
					array(
						'type'  => 'pages',
						'name'  => 'page_order_detail',
						'label' => 'Страница информации о заказе',
						'value' => fs_option( 'page_order_detail', 0 )
					),
				)


			)
		);

		if ( taxonomy_exists( $fs_config->data['currencies_taxonomy'] ) ) {
			$settings['currencies']['fields'] [] = array(
				'type'  => 'custom',
				'name'  => 'default_currency',
				'label' => 'Валюта по умолчанию',
				'html'  => wp_dropdown_categories( array(
					'taxonomy'         => 'fs-currencies',
					'echo'             => false,
					'hide_empty'       => false,
					'selected'         => fs_option( 'default_currency' ),
					'name'             => 'default_currency',
					'show_option_none' => __( 'Select currency', 'fast-shop' )
				) ),
				'value' => fs_option( 'default_currency' )
			);
		}
		$settings = apply_filters( 'fs_plugin_settings', $settings );


		return $settings;
	}


	public function settings_section_description() {
		echo 'Определите настройки вашего магазина.';
	}

	/**
	 * add a menu
	 */
	public function add_menu() {

		// Регистрация страницы настроек
		add_submenu_page(
			'edit.php?post_type=product',
			__( 'Store settings', 'fast-shop' ),
			__( 'Store settings', 'fast-shop' ),
			'manage_options',
			$this->settings_page,
			array( &$this, 'settings_page' )
		);
	} // END public function add_menu()

	/**
	 * Выводит поля, табы настройки плагина в подменю товары
	 */
	public function settings_page() {
		echo '<div class="wrap fast-shop-settings">';
		echo ' <h2>' . esc_html__( 'Store settings', 'fast-shop' ) . '</h2>';
		settings_errors();
		$settings      = $this->register_settings();
		$settings_keys = array_keys( $settings );
		$tab           = $settings_keys[0];
		if ( ! empty( $_GET['tab'] ) ) {
			$tab = esc_attr( $_GET['tab'] );
		}
		echo '<form method="post" action="' . esc_url( add_query_arg( array( 'tab' => $tab ), 'options.php' ) ) . '">';
		echo ' <h2 class="nav-tab-wrapper">';

		foreach ( $settings as $key => $setting ) {
			$class = $tab == $key ? 'nav-tab-active' : '';
			echo '<a href="' . esc_url( add_query_arg( array( "tab" => $key ) ) ) . '" class="nav-tab ' . esc_attr( $class ) . '">' . esc_html( $setting['name'] ) . '</a>';
		}
		echo "</h2>";
		settings_fields( "fs_{$tab}_section" );
		do_settings_sections( $this->settings_page );
		submit_button( null, 'button button-primary button-large' );
		echo '  </form></div>';
	}


	/**
	 * Получает активнй таб настроек
	 *
	 * @param $key
	 *
	 * @return string
	 */
	function get_tab( $key ) {
		$settings      = $this->register_settings();
		$settings_keys = array_keys( $settings );

		return ( isset( $_GET[ $key ] ) ? $_GET[ $key ] : $settings_keys[0] );
	}

	/**
	 * Выводит описание секции, таба в настройках
	 */
	function get_tab_description() {
		$settings    = $this->register_settings();
		$setting_key = $this->get_tab( 'tab' );
		$setting     = $settings[ $setting_key ];
		if ( ! empty( $setting['description'] ) ) {
			echo $setting['description'];
		}

	}

	/**
	 * Инициализирует настройки плагина определенные в методе  register_settings()
	 */
	function init_settings() {
		$settings = $this->register_settings();
		// Регистрируем секции и опции в движке
		$setting_key = $this->get_tab( 'tab' );
		$setting     = $settings[ $setting_key ];
		$section     = "fs_{$setting_key}_section";
		add_settings_section(
			$section,
			$setting['name'],
			array( $this, 'get_tab_description' ),
			$this->settings_page
		);
		if ( count( $setting['fields'] ) ) {
			foreach ( $setting['fields'] as $field ) {
				if ( empty( $field['name'] ) ) {
					continue;
				}
				$settings_id = $field['name'];
				add_settings_field(
					$settings_id,
					$field['label'],
					array( $this, 'setting_field_callback' ),
					$this->settings_page,
					$section,
					array( $settings_id, $field )
				);
				register_setting( $section, $settings_id, null );
			}
		}

	}

	/**
	 * Колбек функция отображающая поля настроек из класса  FS_Form_Class
	 *
	 * @param $args
	 */
	function setting_field_callback( $args ) {
		$form_class = new FS_Form_Class();
		if ( in_array( $args[1]['type'], array( 'text', 'email', 'number' ) ) ) {
			$args[1]['class'] = 'regular-text';
		}
		$form_class->fs_form_field( $args[0], $args[1] );
	}
}
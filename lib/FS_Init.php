<?php

namespace FS;
/**
 * Инициализирует функции и классы плагина
 */
class FS_Init {
	public $fs_config;
	public $fs_payment;
	public $fs_api;
	public $fs_users;
	public $fs_action;
	public $fs_taxonomies;
	public $fs_images;
	public $fs_orders;
	public $fs_cart;
	public $fs_filters;
	public $fs_post_types;
	public $fs_post_type;
	public $fs_rating;
	public $fs_shortcode;
	public $fs_ajax;
	public $fs_settings;
	public $fs_option;
	public $fs_widget;
	public $fs_product;
	public $fs_migrate;


	/**
	 * FS_Init constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( &$this, 'fast_shop_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'fast_shop_admin_scripts' ) );

		// Инициализация классов Fast Shop
		$this->fs_option     = get_option( 'fs_option' );
		$this->fs_config     = new FS_Config();
		$this->fs_settings   = new FS_Settings_Class;
		$this->fs_ajax       = new FS_Ajax_Class;
		$this->fs_shortcode  = new FS_Shortcode;
		$this->fs_rating     = new FS_Rating_Class;
		$this->fs_post_type  = new FS_Post_Type;
		$this->fs_post_types = new FS_Post_Types;
		$this->fs_filters    = new FS_Filters;
		$this->fs_cart       = new FS_Cart_Class;
		$this->fs_orders     = new FS_Orders_Class;
		$this->fs_images     = new FS_Images_Class;
		$this->fs_taxonomies = new FS_Taxonomies_Class;
		$this->fs_action     = new FS_Action_Class;
		$this->fs_users      = new FS_Users_Class;
		$this->fs_api        = new FS_Api_Class();
		$this->fs_payment    = new FS_Payment_Class();
		$this->fs_widget     = new FS_Widget_CLass();
		$this->fs_product    = new FS_Product_Class();
		$this->fs_migrate    = new FS_Migrate_Class();

		add_filter( "plugin_action_links_" . FS_BASENAME, array( $this, 'plugin_settings_link' ) );
		add_action( 'plugins_loaded', array( $this, 'true_load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'session_init' ) );
	} // END public function __construct


	/**
	 * инициализируем сессии
	 */
	function session_init() {
		if ( session_id() == '' ) {
			session_start();
		}
	}

	/**
	 * Устанавливаем путь к файлам локализации
	 */
	function true_load_plugin_textdomain() {
		load_plugin_textdomain( 'fast-shop', false, FS_LANG_PATH );
	}


	/**
	 * На странице плагинов добавляет ссылку "настроить" напротив нашего плагина
	 *
	 * @param $links
	 *
	 * @return mixed
	 */
	function plugin_settings_link( $links ) {
		$settings_link = '<a href="edit.php?post_type=product&page=fast-shop-settings">' . __( 'Settings' ) . '</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Подключаем скрипты и стили во фронтэнде
	 */
	function fast_shop_scripts() {
		wp_enqueue_style( FS_PLUGIN_PREFIX . 'lightslider', FS_PLUGIN_URL . 'assets/lightslider/dist/css/lightslider.min.css', array(), $this->fs_config->data['plugin_ver'], 'all' );
		wp_enqueue_style( FS_PLUGIN_PREFIX . 'font_awesome', 'https://use.fontawesome.com/releases/v5.1.0/css/all.css', array(), $this->fs_config->data['plugin_ver'], 'all' );
		wp_enqueue_style( FS_PLUGIN_PREFIX . 'izi-toast', FS_PLUGIN_URL . 'assets/css/iziToast.min.css', array(), $this->fs_config->data['plugin_ver'], 'all' );
		wp_enqueue_style( FS_PLUGIN_PREFIX . 'style', FS_PLUGIN_URL . 'assets/css/f-shop.css', array(), $this->fs_config->data['plugin_ver'], 'all' );
		wp_enqueue_style( FS_PLUGIN_PREFIX . 'lightgallery', FS_PLUGIN_URL . 'assets/plugins/lightGallery/dist/css/lightgallery.min.css' );
		wp_enqueue_style( FS_PLUGIN_PREFIX . 'jquery-ui', FS_PLUGIN_URL . 'assets/css/jquery-ui.min.css' );

		wp_enqueue_script( FS_PLUGIN_PREFIX . 'lightgallery', FS_PLUGIN_URL . "assets/plugins/lightGallery/dist/js/lightgallery-all.js", array( "jquery" ), null, true );
		wp_enqueue_script( 'jquery-ui-core', array( 'jquery' ) );
		wp_enqueue_script( 'jquery-ui-slider', array( 'jquery' ) );
		wp_enqueue_script( FS_PLUGIN_PREFIX . 'jqueryui-touch-punch', '//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js', array(
			'jquery',
			'jquery-ui-core'
		), false, true );
		wp_enqueue_script( FS_PLUGIN_PREFIX . 'jquery-validate', FS_PLUGIN_URL . 'assets/js/jquery.validate.min.js', array( 'jquery' ), null, true );
		wp_enqueue_script( FS_PLUGIN_PREFIX . 'domurl', FS_PLUGIN_URL . 'assets/js/url.min.js', array( 'jquery' ), null, true );
		wp_enqueue_script( FS_PLUGIN_PREFIX . 'izi-toast', FS_PLUGIN_URL . 'assets/js/iziToast.min.js', array( 'jquery' ), null, true );
		wp_enqueue_script( FS_PLUGIN_PREFIX . 'lightslider', FS_PLUGIN_URL . 'assets/lightslider/dist/js/lightslider.min.js', array( 'jquery' ), null, true );

		wp_enqueue_script( 'f-shop-library', FS_PLUGIN_URL . 'assets/js/fs-library.js', array( 'jquery' ), $this->fs_config->data['plugin_ver'], true );
		wp_enqueue_script( 'f-shop', FS_PLUGIN_URL . 'assets/js/f-shop.js', array(
			'jquery',
			'f-shop-library'
		), $this->fs_config->data['plugin_ver'], true );
		wp_enqueue_script( 'fs-events', FS_PLUGIN_URL . 'assets/js/fs-events.js', array(
			'jquery',
			'f-shop-library',
			'f-shop'
		), $this->fs_config->data['plugin_ver'], true );

		$price_max = fs_price_max( false );
		$l10n      = array(
			'ajaxurl'           => admin_url( "admin-ajax.php" ),
			'fs_slider_max'     => intval( $price_max ),
			'fs_nonce'          => wp_create_nonce( 'fast-shop' ),
			'fs_currency'       => fs_currency(),
			'cartUrl'           => fs_cart_url( false ),
			'wishlistUrl'       => fs_wishlist_url(),
			'lang'              => array(
				'success'       => __( 'Success!', 'fast-shop' ),
				'limit_product' => __( 'You have selected all available items from stock.', 'fast-shop' ),
				'addToCart'     => __( 'Item &laquo;%product%&raquo; successfully added to cart. <a href="%cart_url%">Go to shopping cart</a>', 'fast-shop' ),
				'addToWishlist' => __( 'Item &laquo;%product%&raquo; successfully added to wishlist. <a href="%wishlist_url%">Go to wishlist</a>', 'fast-shop' ),
			),
			'fs_slider_val_min' => ! empty( $_REQUEST['price_start'] ) ? (int) $_REQUEST['price_start'] : 0,
			'fs_slider_val_max' => ! empty( $_REQUEST['price_end'] ) ? (int) $_REQUEST['price_end'] : intval( $price_max )
		);
		wp_localize_script( 'f-shop', 'FastShopData', $l10n );
	}


	/**
	 *  Подключаем скрипты и стили во бэкэнде
	 */
	public function fast_shop_admin_scripts() {
		// необходимо для работы загрузчика изображений
		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}

		wp_enqueue_style( FS_PLUGIN_PREFIX . 'spectrum', FS_PLUGIN_URL . 'assets/css/spectrum.css' );
		wp_enqueue_style( FS_PLUGIN_PREFIX . 'fs-tooltipster', FS_PLUGIN_URL . 'assets/plugins/tooltipster-master/dist/css/tooltipster.main.min.css' );
		wp_enqueue_style( FS_PLUGIN_PREFIX . 'fs-tooltipster-bundle', FS_PLUGIN_URL . 'assets/plugins/tooltipster-master/dist/css/tooltipster.bundle.min.css' );
		wp_enqueue_style( FS_PLUGIN_PREFIX . 'fs-tooltipster-theme', FS_PLUGIN_URL . 'assets/plugins/tooltipster-master/dist/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-light.min.css' );
		wp_enqueue_style( FS_PLUGIN_PREFIX . 'fs-admin', FS_PLUGIN_URL . 'assets/css/fs-admin.css' );

		wp_enqueue_script( FS_PLUGIN_PREFIX . 'spectrum', FS_PLUGIN_URL . 'assets/js/spectrum.js', array( 'jquery' ), null, true );
		wp_enqueue_script( FS_PLUGIN_PREFIX . 'js-cookie', FS_PLUGIN_URL . 'assets/js/js.cookie.js', array( 'jquery' ), null, true );
		$screen = get_current_screen();
		if ( $screen->id == 'edit-product' ) {
			wp_enqueue_script( FS_PLUGIN_PREFIX . 'quick-edit', FS_PLUGIN_URL . 'assets/js/quick-edit.js', array( 'jquery' ), null, true );
		}

		wp_enqueue_script( FS_PLUGIN_PREFIX . 'tooltipster', FS_PLUGIN_URL . 'assets/plugins/tooltipster-master/dist/js/tooltipster.bundle.min.js', array( 'jquery' ), null, true );
		wp_enqueue_script( FS_PLUGIN_PREFIX . 'tooltipster', FS_PLUGIN_URL . 'wp-content/plugins/f-shop/assets/plugins/tooltipster-master/dist/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-shadow.min.css', array( 'jquery' ), null, true );
		wp_enqueue_script( FS_PLUGIN_PREFIX . 'library', FS_PLUGIN_URL . 'assets/js/fs-library.js', array( 'jquery' ), null, true );
		wp_enqueue_script( FS_PLUGIN_PREFIX . 'admin', FS_PLUGIN_URL . 'assets/js/fs-admin.js', array(
			'jquery',
			FS_PLUGIN_PREFIX . 'js-cookie',
			FS_PLUGIN_PREFIX . 'library'
		), null, true );

		$l10n = array(
			'allowedImagesType' => fs_allowed_images_type( 'json' ),
			'mediaNonce'        => wp_create_nonce( 'media-form' )

		);
		wp_localize_script( FS_PLUGIN_PREFIX . 'admin', 'fShop', $l10n );
	}

}

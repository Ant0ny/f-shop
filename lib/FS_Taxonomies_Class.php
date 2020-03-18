<?php
/**
 * Class FS_Taxonomies_Class
 *
 * This class is responsible for registering its own taxonomies.
 * category of goods
 * payment methods
 * delivery methods,
 * characteristics of the product,
 * brands,
 * taxes
 * discounts
 * currencies
 *
 * @package FS
 */

namespace FS;
class FS_Taxonomies_Class {
	function __construct() {
		add_action( 'init', array( $this, 'create_taxonomy' ) );
		add_filter( 'manage_fs-currencies_custom_column', array( $this, 'fs_currencies_column_content' ), 10, 3 );
		add_filter( 'manage_fs-currencies_custom_column', array( $this, 'fs_currencies_column_content' ), 10, 3 );
		add_filter( 'manage_edit-fs-currencies_columns', array( $this, 'add_fs_currencies_columns' ) );
		add_filter( 'document_title_parts', array( $this, 'document_title_parts_filter' ), 10, 1 );
		add_filter( 'wpseo_title', array( $this, 'wpseo_title_filter' ), 10, 1 );
		add_action( 'wp_head', array( $this, 'wp_head_action' ), 1 );

	}

	/**
	 * Registration of additional taxonomy fields
	 *
	 * @param null $term объект текущего термина таксономии
	 *
	 * @return array
	 */
	public static function get_taxonomy_fields( $term = null ) {
		$checkout_fields = [];
		foreach ( FS_Users_Class::get_user_fields() as $key => $user_field ) {
			if ( isset( $user_field['checkout'] ) && $user_field['checkout'] == true ) {
				$checkout_fields[ $key ] = $user_field['name'];
			}
		}
		$fields = array(
			FS_Config::get_data( 'product_taxonomy' )       =>
				array(
					'_content'         => array(
						'name' => __( 'Category text', 'f-shop' ),
						'type' => 'editor',
						'args' => array()
					),
					'_seo_slug'        => array(
						'name' => __( 'SEO slug', 'f-shop' ),
						'type' => 'text',
						'args' => array()
					),
					'_seo_title'       => array(
						'name' => __( 'SEO title', 'f-shop' ),
						'type' => 'text',
						'args' => array()
					),
					'_seo_description' => array(
						'name' => __( 'SEO description', 'f-shop' ),
						'type' => 'textarea',
						'args' => array()
					),
					'_thumbnail_id'    => array(
						'name' => __( 'Thumbnail', 'f-shop' ),
						'type' => 'image',
						'args' => array()
					),
					'_icon_id'         => array(
						'name' => __( 'Icon', 'f-shop' ),
						'type' => 'image',
						'args' => array()
					)
				),
			'fs-payment-methods'                            =>
				array(
					'_thumbnail_id'         => array(
						'name' => __( 'Thumbnail', 'f-shop' ),
						'type' => 'image',
						'args' => array()
					),
					'_fs_pay_message'       => array(
						'name' => __( 'E-mail message to the buyer if the order is confirmed by the manager', 'f-shop' ),
						'help' => __( 'This message is sent to the buyer at the time the manager confirms the order. You can use meta data of type: <code>%order_id%</code> - order number, <code>%pay_name%</code> - name of the payment method, <code>%pay_url%</code> - payment reference .', 'f-shop' ),
						'type' => 'textarea',
						'args' => array()
					),
					'_fs_after_pay_message' => array(
						'name' => __( 'Message to the buyer after payment on the site', 'f-shop' ),
						'help' => __( 'This message will be shown if the buyer has successfully paid the order. You can use these variables: <code>%order_id%</code> - order number, <code>%pay_name%</code> - name of the payment method', 'f-shop' ),
						'type' => 'textarea',
						'args' => array()
					),
					'_fs_pay_inactive'      => array(
						'name' => __( 'Unavailable for payment', 'f-shop' ),
						'help' => __( 'If you turn off, then the payment method will not be visible to users, only in the admin panel.', 'f-shop' ),
						'type' => 'checkbox',
						'args' => array()
					),
					'_fs_checkout_redirect' => array(
						'name' => __( 'When choosing this method, send the buyer immediately to the payment page', 'f-shop' ),
						'help' => __( 'This is convenient in some cases, but it is better to leave this option off', 'f-shop' ),
						'type' => 'checkbox',
						'args' => array()
					)
				),
			'fs-delivery-methods'                           =>
				array(
					'_thumbnail_id'      => array(
						'name' => __( 'Thumbnail', 'f-shop' ),
						'type' => 'image',
						'args' => array()
					),
					'_fs_delivery_cost'  => array(
						'name' => __( 'Shipping Cost in Base Currency', 'f-shop' ),
						'type' => 'number',
						'args' => array( 'style' => 'width:72px;', 'step' => 0.01 )
					),
					'_fs_disable_fields' => array(
						'name' => __( 'Отключить некоторые поля при выборе данного типа доставки', 'f-shop' ),
						'type' => 'select',
						'args' => array(
							'values'   => $checkout_fields,
							'multiple' => true
						)
					)
				),
			'fs-currencies'                                 =>
				array(
					'_fs_currency_code'    => array(
						'name' => __( 'International currency code', 'f-shop' ),
						'type' => 'text',
						'args' => array()
					),
					'_fs_currency_cost'    => array(
						'name' => __( 'Cost in base currency', 'f-shop' ),
						'type' => 'text',
						'args' => array()
					),
					'_fs_currency_display' => array(
						'name' => __( 'Display on the site', 'f-shop' ),
						'type' => 'text',
						'args' => array()
					),
					'_fs_currency_locale'  => array(
						'name' => __( 'Currency Language (locale)', 'f-shop' ),
						'type' => 'select',
						'args' => array( 'values' => FS_Config::get_locales(), )
					)
				),
			// Дополнительные поля налога
			FS_Config::get_data( 'product_taxes_taxonomy' ) =>
				array(
					'_fs_tax_value' => array(
						'name' => __( 'The amount or value of tax as a percentage', 'f-shop' ),
						'type' => 'text',
						'args' => array()
					)
				),
			// Дополнительные поля налога
			FS_Config::get_data( 'manufacturer_taxonomy' )  =>
				array(
					'_thumbnail_id' => array(
						'name' => __( 'Thumbnail', 'f-shop' ),
						'type' => 'image',
						'args' => array()
					),
				),
			// Дополнительные поля скидок
			FS_Config::get_data( 'discount_taxonomy' )      =>
				array(
					'discount_type' => array(
						'name' => __( 'DiscountType', 'f-shop' ),
						'type' => 'select',
						'args' => array(
							'values' => array(
								'product' => __( 'Скидка применяется только к товарам', 'f-shop' )


							)
						)
					),

					'discount_categories' => array(
						'name' => __( 'Категории на которые распространяется скидка', 'f-shop' ),
						'type' => 'dropdown_categories',
						'rule' => [ 'discount_where_is', '=', 'category' ],
						'args' => array(
							'taxonomy' => FS_Config::get_data( 'product_taxonomy' ),
							'multiple' => true,

						)
					),
					'discount_brands'     => array(
						'name' => __( 'Бренды на которые распространяется скидка', 'f-shop' ),
						'type' => 'dropdown_categories',
						'rule' => [ 'discount_where_is', '=', 'category' ],
						'args' => array(
							'taxonomy' => FS_Config::get_data( 'brand_taxonomy' ),
							'multiple' => true,

						)
					),
					'discount_features'   => array(
						'name' => __( 'Свойства товаров на которые распространяется скидка', 'f-shop' ),
						'type' => 'dropdown_categories',
						'args' => array(
							'taxonomy' => FS_Config::get_data( 'features_taxonomy' ),
							'multiple' => true,

						)
					),
					'discount_amount'     => array(
						'name'     => __( 'Discount amount', 'f-shop' ),
						'type'     => 'text',
						'args'     => array(),
						'required' => true,
						'help'     => 'Мужно указать фиксированную сумму, например "20" или в процентах "10%"'
					)
				)

		);

		return apply_filters( 'fs_taxonomy_fields', $fields );
	}

	function wpseo_title_filter( $title ) {
		if ( ! is_tax( 'catalog' ) ) {
			return $title;
		}
		$meta_key   = get_locale() == FS_Config::default_language() ? '_seo_title' : '_seo_title__' . get_locale();
		$meta_title = get_term_meta( get_queried_object_id(), $meta_key, 1 );
		$title      = $meta_title ? $meta_title : $title;

		return $title;
	}

	function document_title_parts_filter( $title ) {
		if ( ! is_tax( 'catalog' ) ) {
			return $title;
		}
		$meta_key       = get_locale() == FS_Config::default_language() ? '_seo_title' : '_seo_title__' . get_locale();
		$meta_title     = get_term_meta( get_queried_object_id(), $meta_key, 1 );
		$title['title'] = $meta_title ? $meta_title : $title['title'];

		return $title;
	}

	function wp_head_action() {
		if ( ! is_tax( 'catalog' ) ) {
			return;
		}
		$meta_key         = get_locale() == FS_Config::default_language() ? '_seo_description' : '_seo_description__' . get_locale();
		$meta_description = get_term_meta( get_queried_object_id(), $meta_key, 1 );

		if ( $meta_description ) {
			$meta_description = apply_filters( 'fs_meta_description', $meta_description );
			echo PHP_EOL . '<meta name="description" content="' . esc_html( $meta_description ) . '"/>' . PHP_EOL;
		}
	}


	/**
	 * Register custom taxonomies
	 *
	 * @return array
	 */
	function shop_taxonomies() {
		$config     = FS_Config::get_data();
		$taxonomies = array(
			$config['product_taxonomy']       => array(
				'object_type'        => 'product',
				'label'              => __( 'Product categories', 'f-shop' ),
				'labels'             => array(
					'name'              => __( 'Product categories', 'f-shop' ),
					'singular_name'     => __( 'Product category', 'f-shop' ),
					'search_items'      => __( 'Product categories', 'f-shop' ),
					'all_items'         => __( 'Product categories', 'f-shop' ),
					'parent_item'       => __( 'Product categories', 'f-shop' ),
					'parent_item_colon' => __( 'Product categories', 'f-shop' ),
					'edit_item'         => __( 'Category editing', 'f-shop' ),
					'update_item'       => __( 'Product categories', 'f-shop' ),
					'add_new_item'      => __( 'Add category', 'f-shop' ),
					'new_item_name'     => __( 'Product categories', 'f-shop' ),
					'menu_name'         => __( 'Product categories', 'f-shop' ),
				),
				'metabox'            => true,
				'hierarchical'       => true,
				"public"             => true,
				"show_ui"            => true,
				"publicly_queryable" => true,
				'show_in_rest'       => true,
				'show_admin_column'  => true
			),
			$config['product_pay_taxonomy']   => array(
				'object_type'        => 'product',
				'label'              => __( 'Payment methods', 'f-shop' ),
				'labels'             => array(
					'name'          => __( 'Payment methods', 'f-shop' ),
					'singular_name' => __( 'Payment method', 'f-shop' ),
					'add_new_item'  => __( 'Add a payment method', 'f-shop' ),
				),
				//					исключаем категории из лицевой части
				"public"             => false,
				"show_ui"            => true,
				"publicly_queryable" => false,
				'meta_box_cb'        => false,
				'metabox'            => false,
				'show_admin_column'  => false,

			),
			$config['product_del_taxonomy']   => array(
				'object_type'        => 'product',
				'label'              => __( 'Delivery methods', 'f-shop' ),
				'labels'             => array(
					'name'          => __( 'Delivery methods', 'f-shop' ),
					'singular_name' => __( 'Delivery method', 'f-shop' ),
					'add_new_item'  => __( 'Add a delivery method', 'f-shop' ),
				),
//					исключаем категории из лицевой части
				"public"             => false,
				"show_ui"            => true,
				"publicly_queryable" => false,
				'metabox'            => false,
				'meta_box_cb'        => false,
				'show_admin_column'  => false,
				'show_in_quick_edit' => false
			),
			$config['features_taxonomy']      => array(
				'object_type'        => 'product',
				'label'              => __( 'Product attributes', 'f-shop' ),
				'labels'             => array(
					'name'          => __( 'Product attributes', 'f-shop' ),
					'singular_name' => __( 'Product attributes', 'f-shop' ),
					'add_new_item'  => __( 'Add property / group of properties', 'f-shop' ),
				),
				//					исключаем категории из лицевой части
				"public"             => true,
				"show_ui"            => true,
				"publicly_queryable" => true,
				'show_in_rest'       => true,
				'metabox'            => true,
				'show_admin_column'  => true,
				'hierarchical'       => true,
				'show_in_quick_edit' => true
			),
			$config['brand_taxonomy']         => array(
				'object_type'        => 'product',
				'label'              => __( 'Manufacturers', 'f-shop' ),
				'labels'             => array(
					'name'          => __( 'Manufacturers', 'f-shop' ),
					'singular_name' => __( 'Manufacturer', 'f-shop' ),
					'add_new_item'  => __( 'Add Manufacturer', 'f-shop' ),
				),
				//					исключаем категории из лицевой части
				"public"             => true,
				"show_ui"            => true,
				"publicly_queryable" => true,
				'show_in_rest'       => true,
				'metabox'            => true,
				'show_admin_column'  => true,
				'hierarchical'       => true,
				'show_in_quick_edit' => true
			),
			$config['product_taxes_taxonomy'] => array(
				'object_type'        => $config['post_type'],
				'label'              => __( 'Taxes', 'f-shop' ),
				'labels'             => array(
					'name'          => __( 'Taxes', 'f-shop' ),
					'singular_name' => __( 'Taxes', 'f-shop' ),
					'add_new_item'  => __( 'Add tax', 'f-shop' ),
				),
				//					исключаем категории из лицевой части
				"public"             => false,
				"show_ui"            => true,
				"publicly_queryable" => false,
				'meta_box_cb'        => false,
				'show_admin_column'  => false,
				'hierarchical'       => false,
				'show_in_quick_edit' => false
			)
		);
		if ( fs_option( 'discounts_on' ) == 1 ) {
			$taxonomies['fs-discounts'] = array(
				'object_type'        => 'product',
				'label'              => __( 'Discounts', 'f-shop' ),
				'labels'             => array(
					'name'          => __( 'Discounts', 'f-shop' ),
					'singular_name' => __( 'Discount', 'f-shop' ),
					'add_new_item'  => __( 'Add Discount', 'f-shop' ),
					'edit_item'     => 'Edit Discount',
					'update_item'   => 'Update Discount',
				),
				//					исключаем категории из лицевой части
				"public"             => false,
				"show_ui"            => true,
				"publicly_queryable" => false,
				'metabox'            => false,
				'show_admin_column'  => false,
				'hierarchical'       => false,
				'meta_box_cb'        => false,
				'show_in_quick_edit' => false
			);
		}
		if ( fs_option( 'multi_currency_on' ) == 1 ) {
			$taxonomies['fs-currencies'] = array(
				'object_type'        => 'product',
				'label'              => __( 'Currencies', 'f-shop' ),
				'labels'             => array(
					'name'          => __( 'Currencies', 'f-shop' ),
					'singular_name' => __( 'Currency', 'f-shop' ),
					'add_new_item'  => __( 'Add Currency', 'f-shop' ),
				),
				//					исключаем категории из лицевой части
				"public"             => false,
				"show_ui"            => true,
				"publicly_queryable" => false,
				'metabox'            => false,
				'show_admin_column'  => false,
				'hierarchical'       => false,
				'meta_box_cb'        => false,
				'show_in_quick_edit' => false
			);
		}

		$taxonomies = apply_filters( 'fs_taxonomies', $taxonomies );

		return $taxonomies;
	}

	/**
	 * Creates taxonomy
	 */
	public function create_taxonomy() {
		// сам процесс регистрации таксономий
		if ( $this->shop_taxonomies() ) {
			foreach ( $this->shop_taxonomies() as $key => $taxonomy ) {
				$object_type = $taxonomy['object_type'];
				unset( $taxonomy['object_type'] );
				register_taxonomy( $key, $object_type, $taxonomy );
			}
		}

		// создание дополнительных полей на странице добавления и редактирования таксономии
		if ( $this->shop_taxonomies() ) {
			foreach ( $this->shop_taxonomies() as $key => $taxonomy ) {
				if ( in_array( $key, array( 'product-attributes' ) ) ) {
					continue;
				}
				// поля таксономии категорий товара
				add_action( "{$key}_edit_form_fields", array( $this, 'edit_taxonomy_fields' ), 10, 2 );
				add_action( "{$key}_add_form_fields", array( $this, 'add_taxonomy_fields' ), 10, 1 );
				add_action( "create_{$key}", array( $this, 'save_taxonomy_fields' ), 10, 2 );
				add_action( "edited_{$key}", array( $this, 'save_taxonomy_fields' ), 10, 2 );
			}
		}


		// поля таксономии харакеристик товара
		add_action( "product-attributes_edit_form_fields", array( $this, 'edit_product_attr_fields' ) );
		add_action( "product-attributes_add_form_fields", array( $this, 'add_product_attr_fields' ) );
		add_action( "create_product-attributes", array( $this, 'save_custom_taxonomy_meta' ) );
		add_action( "edited_product-attributes", array( $this, 'save_custom_taxonomy_meta' ) );
	}

	/**
	 * Возвращает значение мета поля для термина таксономии
	 * обертка для функции get_term_meta()
	 *
	 * @param int $term_id
	 * @param string $meta_key
	 * @param int $single
	 * @param null $default
	 *
	 * @return mixed|void
	 */
	public static function fs_get_term_meta( $term_id = 0, $meta_key = '', $single = 1, $default = null ) {
		if ( ! $term_id || ! $meta_key ) {
			return;
		}

		$meta_value = get_term_meta( $term_id, $meta_key, $single );

		if ( $single == 1 && $meta_value == '' && $default != '' ) {
			$meta_value = $default;
		}

		return apply_filters( 'fs_term_field_value', $meta_value, $term_id, $meta_key );
	}

	/**
	 * Метод выводит мета поля таксономии
	 * массив полей берётся из класа FS_Config
	 * из метода get_taxonomy_fields()
	 * который позволяет устанавливать свои поля серез фильтр fs_taxonomy_fields
	 * TODO: в дальнейшем метаполя всех таксономий выводить с помощью этого метода
	 *
	 * @param $term
	 * @param $taxonomy
	 */
	function edit_taxonomy_fields( $term, $taxonomy ) {
		$form   = new FS_Form_Class();
		$fields = self::get_taxonomy_fields( $term );

		if ( count( $fields[ $taxonomy ] ) ) {

			foreach ( $fields[ $taxonomy ] as $name => $field ) {

				$field['args']['value'] = self::fs_get_term_meta( $term->term_id, $name );
				echo '<tr class="form-field taxonomy-thumbnail-wrap">';
				echo '<th scope="row"><label for="taxonomy-thumbnail">' . esc_attr( $field['name'] ) . '</label></th>';

				echo '<td>';
				$form->render_field( $name, $field['type'], $field['args'] );
				if ( ! empty( $field['help'] ) ) {
					printf( '<p class="description">%s</p>', $field['help'] );
				}
				echo '</td>';
				echo '</tr>';
			}
		}
	}

	/**
	 * Сохраняет значение мета - полей при добавлении нового термина
	 *
	 * @param $taxonomy
	 */
	function add_taxonomy_fields( $taxonomy ) {

		$form   = new FS_Form_Class();
		$fields = self::get_taxonomy_fields();
		if ( count( $fields[ $taxonomy ] ) ) {
			foreach ( $fields[ $taxonomy ] as $name => $field ) {
				$id = str_replace( '_', '-', sanitize_title( 'fs-' . $name . '-' . $field['type'] ) );
				echo '<div class="form-field ' . esc_attr( $name ) . '-wrap">';
				echo '<label for="' . esc_attr( $id ) . '">' . esc_attr( $field['name'] ) . '</label>';
				$form->render_field( $name, $field['type'], $field['args'] );
				if ( ! empty( $field['help'] ) ) {
					printf( '<p class="description">%s</p>', esc_html( $field['help'] ) );
				}
				echo '</div>';
			}
		}
	}

	/**
	 * Preserves all metafields of taxonomy
	 * if the value is empty, the field is removed from the database
	 * TODO: удалить дубликаты этой функции
	 *
	 * @param $term_id
	 */
	function save_taxonomy_fields( $term_id ) {
		$term     = get_term( $term_id );
		$taxonomy = $term->taxonomy;
		$fields   = self::get_taxonomy_fields();

		$multi_lang = false;
		$screen     = get_current_screen();
		$lang       = $_POST['wpglobus_language'] ? $_POST['wpglobus_language'] : $_COOKIE['wpglobus_language'];

		if ( fs_option( 'fs_multi_language_support' )
		     && ( is_array( FS_Config::get_languages() ) && count( FS_Config::get_languages() ) )
		     && $screen->id == 'edit-catalog'
		) {
			$multi_lang = true;
		}


		if ( count( $fields[ $taxonomy ] ) ) {
			foreach ( $fields[ $taxonomy ] as $name => $field ) {

				if ( $multi_lang ) {
					foreach ( FS_Config::get_languages() as $key => $language ) {
						if ( $language['locale'] == FS_Config::default_language() ) {
							$meta_key = $name;
						} else {
							$meta_key = $name . '__' . $language['locale'];
						}

						// Если включена локализация ссылок и пустое значение для локализированого слага, делаем это автоматически
						if ( isset( $_POST['wpglobus_language'] ) ) {
							$post_name = $_POST['name'];
						} else {
							$post_name = $_POST[ 'name_' . $key ];
						}


						if ( fs_option( 'fs_localize_slug' )
						     && $name == '_seo_slug' && $post_name != ''
						     && $_POST[ $meta_key ] == ''
						     && $lang == $key ) {
							$_POST[ $meta_key ] = fs_convert_cyr_name( $post_name );
						}

						if ( isset( $_POST[ $meta_key ] ) && $_POST[ $meta_key ] != '' ) {
							update_term_meta( $term_id, $meta_key, $_POST[ $meta_key ] );
						} else {
							delete_term_meta( $term_id, $meta_key );
						}
					}
				} else {
					if ( isset( $_POST[ $name ] ) && $_POST[ $name ] != '' ) {
						update_term_meta( $term_id, $name, $_POST[ $name ] );
					} else {
						delete_term_meta( $term_id, $name );
					}
				}

			}
		}

		// Обновляем правил ЧПУ
		flush_rewrite_rules();
	}

	/**
	 * Displays fields for setting product characteristics.
	 *
	 * @param $term
	 */
	function edit_product_attr_fields( $term ) {

		$att_type   = get_term_meta( $term->term_id, 'fs_att_type', 1 );
		$attr_types = array(
			'text'  => array( 'name' => __( 'text', 'f-shop' ) ),
			'color' => array( 'name' => __( 'color', 'f-shop' ) ),
			'image' => array( 'name' => __( 'image', 'f-shop' ) ),
			'range' => array( 'name' => __( 'range', 'f-shop' ) )
		);
		?>
        <tr class="form-field term-parent-wrap">
            <th scope="row"><label for="fs_att_type"><?php esc_html_e( 'Attribute type', 'f-shop' ); ?></label></th>
            <td>
                <select name="f-shop[fs_att_type]" id="fs_att_type" class="postform">
					<?php if ( ! empty( $attr_types ) ) {
						foreach ( $attr_types as $att_key => $attr_type ) {
							echo ' <option value="' . esc_attr( $att_key ) . '" ' . selected( $att_key, $att_type, 0 ) . ' > ' . esc_html( $attr_type['name'] ) . '</option >';
						}
					} ?>
                </select>
                <p class="description"><?php esc_html_e( 'Products may have different properties. Here you can choose which type of property you need.', 'f-shop' ); ?></p>
            </td>
        </tr>
        <tr class="form-field term-parent-wrap  fs-att-values fs-att-color"
            style="display: <?php if ( $att_type == 'color' ) {
			    echo 'table-row';
		    } else echo 'none' ?>">
            <th scope="row">
                <label for="fs_att_color_value"><?php esc_html_e( 'Color value', 'f-shop' ); ?></label>
            </th>
            <td>
                <input type="text" name="f-shop[fs_att_color_value]"
                       value="<?php echo esc_attr( get_term_meta( $term->term_id, 'fs_att_color_value', 1 ) ) ?>"
                       class="fs-color-select" id="fs_att_color_value">
            </td>
        </tr>
        <tr class="form-field term-parent-wrap fs-att-values fs-att-range"
            style="display: <?php if ( $att_type == 'range' ) {
			    echo 'table-row';
		    } else echo 'none' ?>">
            <th scope="row"><label><?php esc_html_e( 'Beginning of range', 'f-shop' ); ?></label></th>
            <td>
                <input type="number" step="0.01" name="f-shop[fs_att_range_start_value]" placeholder="0"
                       value="<?php echo esc_attr( get_term_meta( $term->term_id, 'fs_att_range_start_value', 1 ) ) ?>">
            </td>
        </tr>
        <tr class="form-field term-parent-wrap fs-att-values fs-att-range"
            style="display: <?php if ( $att_type == 'range' ) {
			    echo 'table-row';
		    } else echo 'none' ?>">
            <th scope="row"><label><?php esc_html_e( 'End of range', 'f-shop' ); ?></label></th>
            <td>
                <input type="number" step="0.01" name="f-shop[fs_att_range_end_value]" placeholder="∞"
                       value="<?php echo esc_attr( get_term_meta( $term->term_id, 'fs_att_range_end_value', 1 ) ) ?>">
            </td>
        </tr>
        <tr class="form-field term-parent-wrap fs-att-values fs-att-range"
            style="display: <?php if ( $att_type == 'range' ) {
			    echo 'table-row';
		    } else echo 'none' ?>">
            <th scope="row">
                <label for="fs_att_compare"><?php esc_html_e( 'Use the number of purchased goods to compare with this attribute.', 'f-shop' ); ?></label>
            </th>
            <td>
                <input type="checkbox"
                       name="f-shop[fs_att_compare]" <?php checked( 1, get_term_meta( $term->term_id, 'fs_att_compare', 1 ) ) ?>
                       value="1" id="fs_att_compare">
            </td>
        </tr>
		<?php
		$atach_image_id = get_term_meta( $term->term_id, 'fs_att_image_value', 1 );
		$att_image      = $atach_image_id ? wp_get_attachment_image_url( $atach_image_id, 'medium' ) : '';
		$display_button = ! empty( $att_image ) ? 'block' : 'none';
		$display_text   = ! empty( $att_image ) ? __( 'change image', 'f-shop' ) : __( 'select image', 'f-shop' );
		if ( ! empty( $att_image ) ) {
			$class = "show";
		} else {
			$class = "hidden";
		}
		?>
        <tr class="form-field term-parent-wrap fs-att-values fs-att-image"
            style="display: <?php if ( $att_type == 'image' ) {
			    echo 'table-row';
		    } else echo 'none' ?>" id="fs-att-image">
            <th scope="row"><label><?php esc_html_e( 'Image', 'f-shop' ); ?></label></th>
            <td>
                <div class="fs-fields-container">';
                    <div class="fs-selected-image <?php echo esc_attr( $class ) ?>"
                         style=" background-image: url(<?php echo esc_attr( $att_image ) ?>);"></div>
                    <button type="button" class="select_file"><?php echo esc_html( $display_text ) ?></button>
                    <input type="hidden" name="f-shop[fs_att_image_value]"
                           value="<?php echo esc_attr( get_term_meta( $term->term_id, 'fs_att_image_value', 1 ) ) ?>"
                           class="fs-image-select">
                    <button type="button" class="delete_file"
                            style="display:<?php echo esc_attr( $display_button ) ?>"> <?php esc_html_e( 'delete image', 'f-shop' ); ?>
                    </button>
                </div>
            </td>
        </tr>
		<?php
	}


	function add_product_attr_fields() {
		?>
        <div class="form-field term-parent-wrap">
            <label for="fs_att_type"> <?php esc_html_e( 'Attribute type', 'f-shop' ); ?> </label>
            <select name="f-shop[fs_att_type]" id="fs_att_type" class="postform">
                <option value="text"> <?php esc_html_e( 'text', 'f-shop' ); ?></option>
                <option value="color"> <?php esc_html_e( 'color', 'f-shop' ); ?></option>
                <option value="image"> <?php esc_html_e( 'image', 'f-shop' ); ?></option>
            </select>
            <p class="description"> <?php esc_html_e( 'Products may have different properties. Here you can choose which type of property you need.', 'f-shop' ); ?>
                .</p>

        </div>
        <div class="form-field term-parent-wrap fs-att-values" style="display: none;" id="fs-att-color">
            <label for="fs_att_color_value"> <?php esc_html_e( 'Color value', 'f-shop' ); ?> </label>
            <input type="text" name="f-shop[fs_att_color_value]" value="" class="fs-color-select"
                   id="fs_att_color_value">
        </div>
        <div class="form-field term-parent-wrap  fs-att-values" style="display: none;" id="fs-att-image">
            <label> <?php esc_html_e( 'Image', 'f-shop' ); ?></label>
            <div class="fs-fields-container">
                <div class="fs-selected-image" style=" background-image: url();"></div>
                <button type="button" class="select_file"> <?php esc_html_e( 'select image', 'f-shop' ); ?></button>
                <input type="hidden" name="f-shop[fs_att_image_value]" value="" class="fs-image-select">
                <button type="button" class="delete_file"
                        style="display:none"> <?php esc_html_e( 'delete image', 'f-shop' ); ?></button>
            </div>
        </div>
		<?php
	}


	/**
	 * The method is triggered at the time of saving. taxonomy fields
	 *
	 * @param $term_id
	 *
	 * @return bool
	 */
	function save_custom_taxonomy_meta( $term_id ) {
		if ( ! isset( $_POST['f-shop'] ) ) {
			return false;
		}
		if ( ! isset( $_POST['f-shop']['fs_att_compare'] ) ) {
			$_POST['f-shop']['fs_att_compare'] = "-";
		}
		$extra = array_map( 'trim', $_POST['f-shop'] );
		foreach ( $extra as $key => $value ) {
			if ( empty( $value ) || $value == "-" ) {
				delete_term_meta( $term_id, $key );
				continue;
			}
			update_term_meta( $term_id, $key, $value );
		}

		return $term_id;
	}

	// List all product categories
	public function get_product_terms( $terms = 'catalog' ) {
		$args    = array(
			'orderby'                => 'id',
			'order'                  => 'ASC',
			'hide_empty'             => false,
			'exclude'                => array(),
			'exclude_tree'           => array(),
			'include '               => array(),
			'number'                 => '',
			'fields'                 => 'all',
			'slug'                   => '',
			'parent'                 => 0,
			'hierarchical'           => true,
			'child_of'               => 0,
			'get'                    => '',
			'name__like'             => '',
			'pad_counts'             => false,
			'offset'                 => '',
			'search'                 => '',
			'cache_domain'           => 'core',
			'name'                   => '',
			'childless'              => false,
			'update_term_meta_cache' => true,
			'meta_query'             => '',
		);
		$myterms = get_terms( array( $terms ), $args );
		if ( $myterms ) {
			echo "<ul>";
			foreach ( $myterms as $term ) {
				$link  = get_term_link( $term->term_id );
				$class = "no-active";
				if ( strripos( $link, $_SERVER['REQUEST_URI'] ) ) {
					$class = 'active';
				}
				echo '<li class="' . esc_attr( $class ) . '"><a href="' . esc_url( $link ) . '">' . esc_html( $term->name ) . '</a></li> ';
			}
			echo "</ul>";
		}

	}

	/**
	 * удаляет все термины из таксономии $taxonomy
	 * не удаляя при этом самой таксономии
	 *
	 * @param null $taxonomy - название таксономии
	 *
	 * @return bool
	 */

	public static function delete_taxonomy_terms( $taxonomy = null ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return false;
		}
		$terms = get_terms( array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false
			)
		);
		if ( $terms ) {
			foreach ( $terms as $term ) {
				$delete = wp_delete_term( intval( $term->term_id ), $taxonomy );
				if ( is_wp_error( $delete ) ) {
					echo esc_html( $delete->get_error_message() );
					continue;
				}
			}
		}

		return true;

	}

	/**
	 * Регистриует колонку код валюты в таксономии валют
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	function add_fs_currencies_columns( $columns ) {
		$columns['сurrency-code'] = __( 'Currency code', 'f-shop' );
		$columns['cost-basic']    = __( 'Cost', 'f-shop' );
		unset( $columns['description'], $columns['posts'] );

		return $columns;
	}

	function fs_currencies_column_content( $content, $column_name, $term_id ) {
		switch ( $column_name ) {
			case 'сurrency-code':
				//do your stuff here with $term or $term_id
				$content = get_term_meta( $term_id, '_fs_currency_code', 1 );
				break;
			case 'cost-basic':
				//do your stuff here with $term or $term_id
				$content = get_term_meta( $term_id, '_fs_currency_cost', 1 );
				break;
			default:
				break;
		}

		return $content;
	}

}
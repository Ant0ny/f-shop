<?php

namespace FS;

class FS_Form {
	/**
	 * Returns all registered field types
	 *
	 * @return mixed|void
	 */
	function get_registered_field_types() {
		$types = array(
			'text',
			'hidden',
			'password',
			'email',
			'tel',
			'textarea',
			'editor',
			'checkbox',
			'radio',
			'select',
			'gallery',
			'image',
			'media',
			'number',
			'dropdown_categories',
			'radio_categories',
			'pages',
			'dropdown_posts',
			'file',
			'html'
		);

		return apply_filters( 'fs_registered_field_types', $types );

	}


	/**
	 * Sending Email
	 *
	 * @param $email
	 * @param $subject
	 * @param $message
	 * @param string $headers
	 * @param array $attachments
	 *
	 * @return bool
	 */
	public static function send_email( $email, $subject, $message, $headers = '', $attachments = array() ) {
		if ( ! $headers ) {

			$headers = 'Content-type: text/html; charset=utf-8';
		}

		return wp_mail( $email, $subject, $message, $headers, $attachments );
	}

	/**
	 * Displays a field of a certain type
	 *
	 * @param $name
	 * @param string $type
	 * @param array $args
	 */
	function render_field( $name, $type = 'text', $args = [] ) {
		$args = wp_parse_args( $args, array(
			'value'          => '',
			'values'         => array(),
			'required'       => false,
			'title'          => '',
			'label'          => '',
			'placeholder'    => '',
			'label_position' => 'before',
			'taxonomy'       => 'category',
			'help'           => '',
			'size'           => '',
			'style'          => '',
			'step'           => 1,
			'first_option'   => __( 'Select' ),
			'class'          => str_replace( '_', '-', sanitize_title( 'fs-' . $type . '-field' ) ),
			'id'             => str_replace( '_', '-', sanitize_title( 'fs-' . $name . '-' . $type ) ),
			'default'        => '',
			'textarea_rows'  => 8,
			'post_type'      => 'post',
			'editor_args'    => array(
				'textarea_rows' => 8,
				'textarea_name' => $name
			)
		) );


		$label_after = $args['required'] ? ' <i>*</i>' : '';

		$multi_lang = false;
		$screen     = is_admin() && get_current_screen() ? get_current_screen() : null;

		if ( fs_option( 'fs_multi_language_support' )
		     && ( is_array( FS_Config::get_languages() ) && count( FS_Config::get_languages() ) )
		     && ( ! in_array( $type, [ 'image' ] ) )
		     && ( isset( $screen->id ) && $screen->id == 'edit-catalog' )
		) {
			$multi_lang = true;
		}

		if ( $multi_lang ) {
			echo '<div class="fs-tabs nav-tab-wrapper">';
			echo '<div class="fs-tabs__header">';
			$count = 0;
			foreach ( FS_Config::get_languages() as $key => $language ) {
				$tab_class = ! $count ? 'nav-tab-active' : '';
				echo '<a href="#fs_' . esc_attr( $name ) . '-' . esc_attr( $key ) . '" class="fs-tabs__title nav-tab ' . esc_attr( $tab_class ) . '">' . esc_html( $language['name'] ) . '</a>';
				$count ++;
			}
			echo '</div><!! end .fs-tabs__header !!>';
		}

		if ( $multi_lang ) {
			$count = 0;
			foreach ( FS_Config::get_languages() as $key => $item ) {
				$tab_class  = ! $count ? 'fs-tabs__body fs-tab-active' : 'fs-tabs__body';
				$base_name  = $name;
				$base_id    = $args['id'];
				$args['id'] = $args['id'] . '-' . $key;

				echo '<div class="' . esc_attr( $tab_class ) . '" id="fs_' . esc_attr( $name ) . '-' . esc_attr( $key ) . '">';
				$name          = $item['locale'] != FS_Config::default_language() ? $name . '__' . $item['locale'] : $name;
				$args['value'] = ! empty( $_GET['tag_ID'] ) ? FS_Taxonomy::fs_get_term_meta( intval( $_GET['tag_ID'] ), $name ) : null;
				if ( ! $args['value'] && $args['default'] ) {
					$args['value'] = $args['default'];
				}

				$args['editor_args']['textarea_name'] = $name;

				if ( in_array( $type, $this->get_registered_field_types() ) && file_exists( FS_PLUGIN_PATH . 'templates/back-end/fields/' . $type . '.php' ) ) {
					if ( ( $args['label'] || $args['help'] ) && $args['label_position'] == 'before' ) {
						echo '<label for="' . esc_attr( $args['id'] ) . '">' . esc_html( $args['label'] ) . $label_after;
						if ( $args['help'] ) {
							echo '<span class="tooltip dashicons dashicons-editor-help" title="' . esc_html( $args['help'] ) . '"></span>';
						}
						echo '</label>';
					}
					include FS_PLUGIN_PATH . 'templates/back-end/fields/' . $type . '.php';

					if ( ( ! empty( $args['label'] ) || ! empty( $args['help'] ) ) && $args['label_position'] == 'after' ) {

						echo '<label for="' . esc_attr( $args['id'] ) . '">' . esc_html( $args['label'] ) . $label_after;
						if ( $args['help'] ) {
							echo '<span class="tooltip dashicons dashicons-editor-help" title="' . esc_html( $args['help'] ) . '"></span>';
						}
						echo '</label>';
					}
				}
				echo '</div><!! end .fs-tabs__body !!>';
				$count ++;
				$name       = $base_name;
				$args['id'] = $base_id;
			}

		} else {
			if ( in_array( $type, $this->get_registered_field_types() ) && file_exists( FS_PLUGIN_PATH . 'templates/back-end/fields/' . $type . '.php' ) ) {
				if ( ( $args['label'] || $args['help'] ) && $args['label_position'] == 'before' ) {
					echo '<label for="' . esc_attr( $args['id'] ) . '">' . esc_html( $args['label'] ) . $label_after;
					if ( $args['help'] ) {
						echo '<span class="tooltip dashicons dashicons-editor-help" title="' . esc_html( $args['help'] ) . '"></span>';
					}
					echo '</label>';
				}
				include FS_PLUGIN_PATH . 'templates/back-end/fields/' . $type . '.php';

				if ( ( ! empty( $args['label'] ) || ! empty( $args['help'] ) ) && $args['label_position'] == 'after' ) {

					echo '<label for="' . esc_attr( $args['id'] ) . '">' . esc_html( $args['label'] ) . $label_after;
					if ( $args['help'] ) {
						echo '<span class="tooltip dashicons dashicons-editor-help" title="' . esc_html( $args['help'] ) . '"></span>';
					}
					echo '</label>';
				}
			}
		}

		if ( $multi_lang ) {
			echo '</div><!! end .fs-tabs !!>';
		}
	}

	/**
	 * @param string $field_name ключ поля в FS_Config::$form_fields
	 * @param array $args атрибуты input (class,id,value,checked)
	 *
	 * @return string html код поля
	 */
	function fs_form_field( $field_name, $args = array() ) {

		$user_id = get_current_user_id();

		$fields = FS_Users::get_user_fields();

		$field = ! empty( $fields[ $field_name ] ) && is_array( $fields[ $field_name ] )
			? $fields[ $field_name ]
			: array();

		$value = ! empty( $field['value'] ) && fs_option( 'fs_autofill_form', false )
			? trim( $field['value'] )
			: ( is_user_logged_in() && fs_option( 'fs_autofill_form', false ) && get_user_meta( $user_id, $field_name, 1 ) ? get_user_meta( $user_id, $field_name, 1 ) : '' );


		$default = array(
			'type'           => ! empty( $field['type'] ) ? $field['type'] : 'text',
			'class'          => 'fs-input form-control',
			'wrapper'        => true,
			'autofill'       => true,
			'wrapper_class'  => 'fs-field-wrap form-group ' . str_replace( '_', '-', $field_name ) . '-wrap',
			'label_class'    => 'fs-form-label',
			'taxonomy'       => ! empty( $field['taxonomy'] ) ? $field['taxonomy'] : 'category',
			'id'             => str_replace( array(
				'[',
				']'
			), array( '_' ), $field_name ),
			'required'       => ! empty( $field['required'] ) ? $field['required'] : false,
			'title'          => ! empty( $field['title'] ) ? $field['title'] : __( 'this field is required', 'f-shop' ),
			'placeholder'    => ! empty( $field['placeholder'] ) ? $field['placeholder'] : null,
			'value'          => $value,
			'label'          => ! empty( $field['label'] ) ? $field['label'] : '',
			'icon'           => ! empty( $field['icon'] ) ? $field['icon'] : '',
			'label_position' => ! empty( $field['label_position'] ) ? $field['label_position'] : 'before',
			'html'           => '',
			'selected'       => '',
			'options'        => array(),
			'values'         => ! empty( $field['values'] ) ? $field['values'] : array(),
			'format'         => '%input% %label%',
			'el'             => 'radio',
			'first_option'   => ! empty( $field['first_option'] ) ? $field['first_option'] : __( 'Select' ),
			'before'         => '',
			'after'          => '',
			'disabled'       => ! empty( $field['disabled'] ) ? 'disabled' : false,
			'editor_args'    => array(
				'textarea_rows' => 8,
				'textarea_name' => $field_name,
				'tinymce'       => false,
				'media_buttons' => false
			)

		);

		$args = wp_parse_args( $args, $default );


		echo $args['before'];
		if ( $args['wrapper'] ) {
			echo '<div class="' . esc_attr( $args['wrapper_class'] ) . '">';
		}
		$this->render_field( $field_name, $args['type'], $args );

		if ( ! empty( $args['help'] ) && ! in_array( $args['type'], array( 'checkbox' ) ) ) {
			echo '<span class="tooltip dashicons dashicons-editor-help" title="' . esc_attr( $args['help'] ) . '"></span>';
		}

		if ( $args['wrapper'] ) {
			echo '</div>';
		}

		return;
	}

	/**
	 * Возвращает открывающий тег формы со скрытыми полями безопасности
	 *
	 * @param $args array дополнительные аргументы формы
	 *
	 * @return string
	 */
	public static function form_open( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'method'       => 'POST',
			'autocomplete' => 'off',
			'ajax'         => 'off',
			'class'        => 'fs-form',
			'id'           => 'fs-form',
			'name'         => 'fs-ajax',
			'enctype'      => 'multipart/form-data',
			'action'       => '',
			'ajax_action'  => 'fs_save_data',
			'validate'     => true
		) );

		$out = '<form';
		$out .= ' action="' . esc_attr( $args['action'] ) . '"';
		$out .= ' data-ajax="' . esc_attr( $args['ajax'] ) . '"';
		$out .= ' name="' . esc_attr( $args['name'] ) . '"';
		$out .= ' method="' . esc_attr( $args['method'] ) . '"';
		$out .= ' autocomplete="' . esc_attr( $args['autocomplete'] ) . '"';
		$out .= ' data-validation="' . esc_attr( $args['validate'] ) . '"';
		$out .= ' enctype="' . esc_attr( $args['enctype'] ) . '"';
		$out .= ' class="' . esc_attr( $args['class'] ) . '"';
		$out .= ' id="' . esc_attr( $args['id'] ) . '">';
		$out .= FS_Config::nonce_field();
		$out .= '<input type="hidden" name="action" value="' . esc_attr( $args['ajax_action'] ) . '">';

		return $out;
	}

	/**
	 * Возвращает закрывающий тег формы
	 *
	 * @return string
	 */
	public static function form_close() {
		return '</form>';
	}

}

<?php
/**
 * Created by PhpStorm.
 * User: karak
 * Date: 27.02.2017
 * Time: 15:57
 */

namespace FS;
class FS_Form_Class {

	/**
	 * @param string $field_name ключ поля в FS_Config::$form_fields
	 * @param array $args атрибуты input (class,id,value,checked)
	 *
	 * @return string html код поля
	 */
	function fs_form_field( $field_name, $args = array() ) {
		global $fs_config;
		$curent_user = wp_get_current_user();
		//подставляем начальное значение в атрибут value интпута формы
		$default_value = '';
		$selected      = '';
		if ( $curent_user->exists() && fs_option('autofill')=='1') {
			switch ( $field_name ) {
				case 'fs_email':
					$default_value = $curent_user->user_email;
					break;
				case 'fs_first_name':
					$default_value = $curent_user->first_name;
					break;
				case 'fs_last_name':
					$default_value = $curent_user->last_name;
					break;
				case 'fs_phone':
					$default_value = get_user_meta( $curent_user->ID, 'fs_phone', 1 );
					break;
				case 'fs_city':
					$default_value = get_user_meta( $curent_user->ID, 'fs_city', 1 );
					break;
				case 'fs_adress':
					$default_value = get_user_meta( $curent_user->ID, 'fs_adress', 1 );
					break;
				case 'fs_delivery_methods':
					$selected = get_user_meta( $curent_user->ID, 'fs_delivery_methods', 1 );
					break;
				case 'fs_payment_methods':
					$selected = get_user_meta( $curent_user->ID, 'fs_payment_methods', 1 );
					break;
				case 'fs_customer_register':
					return;
					break;

			}
		}
		$default     = array(
			'type'          => FS_Config::$form_fields[ $field_name ]['type'],
			'class'         => '',
			'wrapper'       => true,
			'wrapper_class' => 'fs-field-wrapper',
			'label_class'   => 'fs-form-label',
			'id'            => str_replace( array(
				'[',
				']'
			), array( '_' ), $field_name ),
			'required'      => FS_Config::$form_fields[ $field_name ]['required'],
			'title'         => __( 'this field is required', 'fast-shop' ),
			'placeholder'   => FS_Config::$form_fields[ $field_name ]['placeholder'],
			'label'         => FS_Config::$form_fields[ $field_name ]['label'],
			'value'         => $default_value,
			'html'          => '',
			'selected'      => $selected,
			'options'       => array(),
			'format'        => '%input% %label%',
			'el'            => 'select',
			'first_option'  => __( 'Select' ),
			'before'        => '',
			'after'         => '',
			'editor_args'   => array(
				'textarea_rows' => 8,
				'textarea_name' => $field_name,
				'tinymce'       => false,
				'media_buttons' => false
			)

		);
		$args        = wp_parse_args( $args, $default );
		$class       = ! empty( $args['class'] ) ? 'class="' . sanitize_html_class( $args['class'] ) . '"' : '';
		$id          = ! empty( $args['id'] ) ? 'id="' . sanitize_html_class( $args['id'] ) . '"' : 'id=""';
		$title       = ( ! empty( $args['title'] ) && $args['required'] ) ? 'title="' . esc_html( $args['title'] ) . '"' : '';
		$placeholder = ! empty( $args['placeholder'] ) ? 'placeholder="' . esc_html( $args['placeholder'] ) . '"' : '';
		$value       = ! empty( $args['value'] ) ? 'value="' . esc_html( $args['value'] ) . '"' : '';

		$required = ! empty( $args['required'] ) ? 'required' : '';
		$field    = $args['before'];
		if ( $args['wrapper'] ) {
			$field .= '<div class="' . esc_attr( $args['wrapper_class'] ) . '">';
		}
		switch ( $args['type'] ) {
			case 'text':
				$field .= ' <input type="text" name="' . $field_name . '"  ' . $class . ' ' . $title . ' ' . $required . ' ' . $placeholder . ' ' . $value . ' ' . $id . '> ';
				break;
			case 'email':
				$field .= ' <input type="email" name="' . $field_name . '"  ' . $class . ' ' . $title . ' ' . $required . '  ' . $placeholder . ' ' . $value . ' ' . $id . '> ';
				break;
			case 'tel':
				$field .= ' <input type="tel" name="' . $field_name . '"  ' . $class . ' ' . $title . ' ' . $required . '  ' . $placeholder . ' ' . $value . ' ' . $id . '> ';
				break;
			case 'radio':
				$field .= ' <input type="radio" name="' . $field_name . '"  ' . checked( 'on', $value, false ) . ' ' . $class . ' ' . $title . ' ' . $required . '  ' . $placeholder . ' ' . $value . ' ' . $id . '> ';
				break;
			case 'checkbox':
				$field .= ' <input type="checkbox" name="' . $field_name . '"  ' . checked( '1', $args['value'], false ) . ' ' . $class . ' ' . $title . ' ' . $required . '  ' . $placeholder . '  value="1"  ' . $id . '> ';
				break;
			case 'textarea':
				$field .= '<textarea name="' . $field_name . '"  ' . $class . ' ' . $title . ' ' . $required . '  ' . $placeholder . ' ' . $id . '></textarea>';
				break;
			case 'custom':
				$field .= $args['html'];
				break;
			case 'button':
				$field .= '<button type="button" ' . $class . ' ' . $id . '>' . $args['value'] . '</button>';
				break;
			case 'select':
				$field .= '<select name="' . $field_name . '">';
				$field .= '<option value="">' . $args['first_option'] . '</option>';
				foreach ( $args['options'] as $k => $val ) {
					$field .= '<option value="' . $k . '"  ' . selected( $args['value'], $k, 0 ) . '>' . $val . '</option>';
				}
				$field .= '</select>';

				break;
			case 'pages':
				$field .= wp_dropdown_pages( array(
					'show_option_none'  => __( 'Select page', 'fast-shop' ),
					'option_none_value' => 0,
					'name'              => $field_name,
					'echo'              => 0,
					'id'                => $args['id'],
					'selected'          => $args['value']
				) );
				break;
			case 'pay_methods':
				if ( $args['el'] == 'select' ) {
					$field .= wp_dropdown_categories( array(
						'show_option_all' => $args['first_option'],
						'hide_empty'      => 0,
						'name'            => $field_name,
						'selected'        => $args['selected'],
						'class'           => $args['class'],
						'echo'            => 0,
						'taxonomy'        => $fs_config->data['product_pay_taxonomy']
					) );
				} elseif ( $args['el'] == 'radio' ) {
					$pay_methods = get_terms( array(
						'hide_empty' => false,
						'taxonomy'   => $fs_config->data['product_pay_taxonomy']
					) );
					if ( $pay_methods ) {
						foreach ( $pay_methods as $pay_method ) {
							$field .= str_replace( array( '%input%', '%label%' ), array(
								'<input type="radio" name="' . $field_name . '" value="' . $pay_method->term_id . '" class="' . $args['class'] . '" id="fs - del - ' . $pay_method->term_id . '">',
								'<label for="fs - del - ' . $pay_method->term_id . '" class="' . $args['label_class'] . '">' . $pay_method->name . '</label>'
							), $args['format'] );
						}
					}

				}
				break;
			case 'del_methods':
				if ( $args['el'] == 'select' ) {
					$field .= wp_dropdown_categories( array(
						'show_option_all' => $args['first_option'],
						'hide_empty'      => 0,
						'name'            => $field_name,
						'selected'        => $args['selected'],
						'class'           => $args['class'],
						'echo'            => 0,
						'taxonomy'        => $fs_config->data['product_del_taxonomy']
					) );
				} elseif ( $args['el'] == 'radio' ) {
					$del_methods = get_terms( array(
						'hide_empty' => false,
						'taxonomy'   => $fs_config->data['product_del_taxonomy']
					) );
					if ( $del_methods ) {
						foreach ( $del_methods as $del_method ) {
							$field .= str_replace( array( '%input%', '%label%' ), array(
								'<input type="radio" name="' . $field_name . '" value="' . $del_method->term_id . '" class="' . $args['class'] . '" id="fs - del - ' . $del_method->term_id . '">',
								'<label for="fs - del - ' . $del_method->term_id . '" class="' . $args['label_class'] . '">' . $del_method->name . '</label>'
							), $args['format'] );
						}
					}

				}
				break;

			case 'editor':
				wp_editor( esc_html( $args['value'] ), $args['id'], $args['editor_args'] );
				$field = ob_get_clean();
				break;
		}
		if ( ! empty( $args['help'] ) ) {
			$field .= '<span class="tooltip dashicons dashicons-editor-help" title="' . esc_attr( $args['help'] ) . '"></span>';
		}
		if ( $args['wrapper'] ) {
			$field .= '</div>';
		}
		echo apply_filters( 'fs_form_field', $field, $field_name, $args );
	}
}

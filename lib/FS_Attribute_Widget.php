<?php
/**
 * Created by PhpStorm.
 * User: karak
 * Date: 30.04.2018
 * Time: 19:07
 */

namespace FS;

/*
 * Виджет корзины
 */

class FS_Attribute_Widget extends \WP_Widget {
	function __construct() {
		parent::__construct(
			'fs_attribute_widget',
			'Фильтр по атрибутам товара',
			array( 'description' => 'Позволяет вывести фильтр для фильтрации товара по атрибутам' )
		);
	}

	/*
	 * бэкэнд виджета
	 */
	public function form( $instance ) {
		global $fs_config;
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		}
		if ( isset( $instance['fs_att_group'] ) ) {
			$fs_att_group = $instance['fs_att_group'];
		}
		$args = array(
			'show_option_all'  => '',
			'show_option_none' => '',
			'orderby'          => 'name',
			'order'            => 'ASC',
			'show_last_update' => 0,
			'show_count'       => 0,
			'hide_empty'       => 0,
			'child_of'         => 0,
			'exclude'          => '',
			'echo'             => 1,
			'selected'         => $fs_att_group,
			'hierarchical'     => 1,
			'name'             => $this->get_field_name( 'fs_att_group' ),
			'id'               => $this->get_field_id( 'fs_att_group' ),
			'depth'            => 1,
			'tab_index'        => 0,
			'taxonomy'         => $fs_config->data['product_att_taxonomy'],
			'hide_if_empty'    => false,
			'value_field'      => 'term_id', // значение value e option
			'required'         => false,
		);
		?>
      <p>
        <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title','fast-shop') ?></label>
        <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"
               name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text"
               value="<?php echo esc_attr( $title ); ?>"/>
      </p>
      <p>
        <label
          for="<?php echo esc_attr( $this->get_field_id( 'fs_att_group' ) ); ?>"><?php esc_html_e( 'Feature Group', 'fast-shop' ) ?></label><br>
		  <?php wp_dropdown_categories( $args ); ?>
      </p>
		<?php
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		do_action( 'fs_attr_filter', $instance['fs_att_group'] );
		echo $args['after_widget'];
	}

	/*
	 * сохранение настроек виджета
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                 = array();
		$instance['title']        = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['fs_att_group'] = intval( $new_instance['fs_att_group'] );

		return $instance;
	}
}
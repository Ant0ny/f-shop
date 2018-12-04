<table class="wp-list-table widefat fixed striped order-list">
    <thead>
    <tr>
        <th><?php _e( 'ID', 'fast-shop' ) ?></th>
        <th><?php _e( 'Name', 'fast-shop' ) ?></th>
        <th><?php _e( 'SKU', 'fast-shop' ) ?></th>
        <th><?php _e( 'Price', 'fast-shop' ) ?></th>
        <th><?php _e( 'Quantity', 'fast-shop' ) ?></th>
        <th><?php _e( 'Attributes', 'fast-shop' ) ?></th>
        <th><?php _e( 'Cost', 'fast-shop' ) ?></th>
    </tr>
    </thead>
    <tbody>
	<?php foreach ( $products as $variation_id => $product ): ?>
		<?php
		$offer = fs_set_product( $product );
		?>
        <tr>
            <td><?php echo esc_attr( $offer->id ) ?></td>
            <td><a href="<?php echo esc_url( $offer->permalink ) ?>" target="_blank"
                   title="перейти к товару"><?php echo esc_attr( $offer->title ) ?></a></td>
            <td><?php echo esc_attr( $offer->sku ) ?></td>
            <td><?php echo esc_attr( $offer->price_display ) ?>&nbsp;<?php echo esc_attr( $offer->currency ) ?></td>
            <td><?php echo esc_attr( $offer->count ) ?></td>
            <td>
				<?php
				global $fs_config;
				if ( count( $offer->attributes ) ) {
					echo '<ul class="product-att">';
					foreach ( $offer->attributes as $att ) {
						echo '<li><b>' . esc_attr( $att->parent_name ) . '</b>: ' . esc_attr( $att->name ) . '</li>';
					}
					echo '</ul>';
				}
				?>
            </td>
            <td><?php echo esc_attr( $offer->cost_display ) ?>&nbsp;<?php echo esc_attr( $offer->currency ) ?></td>
        </tr>
	<?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="6">Общая стоимость</td>
        <td colspan="1"><?php echo $amount ?></td>
    </tr>
    </tfoot>
</table>
<?php
/**
** A base module for [select] and [select*]
**/
/* form_tag handler */
add_action( 'wpcf7_init', 'wpcf7_add_form_tag_select_custom' );
function wpcf7_add_form_tag_select_custom() {
	wpcf7_add_form_tag( array( 'select_custom', 'select_custom*' ),
		'wpcf7_select_form_tag_handler_custom',
		array(
			'name-attr' => true,
			'selectable-values' => true,
		)
	);
}
function wpcf7_select_form_tag_handler_custom( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}
	$validation_error = wpcf7_get_validation_error( $tag->name );
	$class = wpcf7_form_controls_class( $tag->type );
	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}
	$atts = array();
	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
	$atts['autocomplete'] = $tag->get_option(
		'autocomplete', '[-0-9a-zA-Z]+', true
	);
	if ( $tag->is_required() ) {
		$atts['aria-required'] = 'true';
	}
	if ( $validation_error ) {
		$atts['aria-invalid'] = 'true';
		$atts['aria-describedby'] = wpcf7_get_validation_error_reference(
			$tag->name
		);
	} else {
		$atts['aria-invalid'] = 'false';
	}
	$multiple = $tag->has_option( 'multiple' );
	$include_blank = $tag->has_option( 'include_blank' );
	$first_as_label = $tag->has_option( 'first_as_label' );
	if ( $tag->has_option( 'size' ) ) {
		$size = $tag->get_option( 'size', 'int', true );
		if ( $size ) {
			$atts['size'] = $size;
		} elseif ( $multiple ) {
			$atts['size'] = 4;
		} else {
			$atts['size'] = 1;
		}
	}
	if ( $data = (array) $tag->get_data_option() ) {
		$tag->values = array_merge( $tag->values, array_values( $data ) );
		$tag->labels = array_merge( $tag->labels, array_values( $data ) );
	}
	//custom 
	$custom_data = $tag->raw_values; 
	$values = array();
	$labels = array();
	foreach( $custom_data as $value_chua ){
		$custom_data_ok = explode("|",$value_chua);
		$values[] = $custom_data_ok[0];
		if(isset($custom_data_ok[1])) {
			$labels[] = $custom_data_ok[1];
		}else{
			$labels[] = $custom_data_ok[0];
		}
	}
	// end custom 
	$default = $tag->get_option( 'default', '', true );
	$default_choice = $tag->get_default_option( $default, array(
		'multiple' => $multiple,
	) );
	if ( $include_blank
	or empty( $values ) ) {
		array_unshift(
			$labels,
			__( '&#8212;Please choose an option&#8212;', 'contact-form-7' )
		);
		array_unshift( $values, '' );
	} elseif ( $first_as_label ) {
		$values[0] = '';
	}
	$html = '';
	$hangover = wpcf7_get_hangover( $tag->name );
	$i = 1;
	foreach ( $values as $key => $value ) {
		if ( $hangover ) {
			$selected = in_array( $value, (array) $hangover, true );
		} else {
			$selected = in_array( $value, (array) $default_choice, true );
		}
		$item_atts = array(
			'value' => $value,
			'selected' => $selected,
		);
		$label = isset( $labels[$key] ) ? $labels[$key] : $value;
		$html .= sprintf(
			'<option %1$s>%2$s</option>',
			wpcf7_format_atts( $item_atts ),
			esc_html( $label )
		);
		$i++;
	}
	$atts['multiple'] = (bool) $multiple;
	$atts['name'] = $tag->name . ( $multiple ? '[]' : '' );
	$html = sprintf(
		'<span class="wpcf7-form-control-wrap" data-name="%1$s"><select %2$s>%3$s</select>%4$s</span>',
		esc_attr( $tag->name ),
		wpcf7_format_atts( $atts ),
		$html,
		$validation_error
	);
	return $html;
}
/* Validation filter */
add_filter( 'wpcf7_validate_select_custom', 'wpcf7_select_validation_filter_custom', 10, 2 );
add_filter( 'wpcf7_validate_select_custom*', 'wpcf7_select_validation_filter_custom', 10, 2 );
function wpcf7_select_validation_filter_custom( $result, $tag ) {
	$name = $tag->name;
	if ( isset( $_POST[$name] ) && is_array( $_POST[$name] ) ) {
		foreach ( $_POST[$name] as $key => $value ) {
			if ( '' === $value ) {
				unset( $_POST[$name][$key] );
			}
		}
	}
	$empty = ! isset( $_POST[$name] ) || empty( $_POST[$name] ) && '0' !== $_POST[$name];
	if ( $tag->is_required() && $empty ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
	}
	return $result;
}
/* Tag generator */
add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_menu_custom', 98 );
function wpcf7_add_tag_generator_menu_custom() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	if( version_compare(WPCF7_VERSION,"6.0") >= 0 ){
		$tag_generator->add( 'select_custom', __( 'drop-down menu price', 'cf7-cost-calculator-price-calculation' ),
		'wpcf7_tag_generator_menu_custom_2',array("version"=>2) );
	}else{
		$tag_generator->add( 'select_custom', __( 'drop-down menu price', 'cf7-cost-calculator-price-calculation' ),
		'wpcf7_tag_generator_menu_custom' );
	}
}
function wpcf7_tag_generator_menu_custom_2( $contact_form, $options = '' ){
	$field_types = array(
		'select_custom' => array(
			'display_name' => __( 'Drop-down menu', 'contact-form-7' ),
			'heading' => __( 'Drop-down menu form-tag generator', 'contact-form-7' ),
			'description' => __( 'Generates a form-tag for a <a href="https://contactform7.com/checkboxes-radio-buttons-and-menus/">drop-down menu</a>.', 'contact-form-7' ),
		),
	);
	$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );
	?>
	<header class="description-box">
		<h3><?php
			echo esc_html( $field_types['select_custom']['heading'] );
		?></h3>
		<p><?php
			$description = wp_kses(
				$field_types['select_custom']['description'],
				array(
					'a' => array( 'href' => true ),
					'strong' => array(),
				),
				array( 'http', 'https' )
			);
			echo $description;
		?></p>
	</header>
	<div class="control-box">
		<?php
			$tgg->print( 'field_type', array(
				'with_required' => true,
				'select_options' => array(
					'select_custom' => $field_types['select_custom']['display_name'],
				),
			) );
			$tgg->print( 'field_name' );
			$tgg->print( 'class_attr' );
		?>
		<fieldset>
			<legend id="selectable-values-legend"><?php
				echo esc_html( __( 'Selectable values', 'contact-form-7' ) );
			?></legend>
			<?php
				echo sprintf(
					'<span %1$s>%2$s</span>',
					wpcf7_format_atts( array(
						'id' => 'selectable-values-description',
					) ),
					esc_html( __( "One item per line.", 'contact-form-7' ) )
				);
			?>
			<br />
			<?php
				echo sprintf(
					'<textarea %1$s>%2$s</textarea>',
					wpcf7_format_atts( array(
						'required' => true,
						'data-tag-part' => 'value',
						'aria-labelledby' => 'selectable-values-legend',
						'aria-describedby' => 'selectable-values-description',
					) ),
					esc_html( __( "10|Option 1\n20|Option 2\n30|Option 3", 'contact-form-7' ) )
				);
			?>
			<?php if ( true ) { ?>
			<br />
			<?php
				echo sprintf(
					'<label><input %1$s /> %2$s</label>',
					wpcf7_format_atts( array(
						'type' => 'checkbox',
						'checked' => 'checked',
						'data-tag-part' => 'option',
						'data-tag-option' => 'use_label_element',
					) ),
					esc_html( __( "Wrap each item with a label element.", 'contact-form-7' ) )
				);
			?>
			<?php } ?>
		</fieldset>
	</div>
	<footer class="insert-box">
		<?php
			$tgg->print( 'insert_box_content' );
			$tgg->print( 'mail_tag_tip' );
		?>
	</footer>
	<?php
}
function wpcf7_tag_generator_menu_custom( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$description = __( "Generate a form-tag for a drop-down menu. For more details, see %s.", 'cf7-cost-calculator-price-calculation' );
	$desc_link = wpcf7_link( __( 'https://contactform7.com/checkboxes-radio-buttons-and-menus/', 'cf7-cost-calculator-price-calculation' ), __( 'Checkboxes, Radio Buttons and Menus', 'cf7-cost-calculator-price-calculation' ) );
?>
<div class="control-box">
<fieldset>
<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>
<table class="form-table">
<tbody>
	<tr>
	<th scope="row"><?php echo esc_html( __( 'Field type', 'cf7-cost-calculator-price-calculation' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'cf7-cost-calculator-price-calculation' ) ); ?></legend>
		<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'cf7-cost-calculator-price-calculation' ) ); ?></label>
		</fieldset>
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'cf7-cost-calculator-price-calculation' ) ); ?></label></th>
	<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
	</tr>
	<tr>
	<th scope="row"><?php echo esc_html( __( 'Options', 'cf7-cost-calculator-price-calculation' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Options', 'cf7-cost-calculator-price-calculation' ) ); ?></legend>
		<textarea name="values" class="values" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>"></textarea>
		<?php echo  __( "One option per line (number|text): Ex: <strong>10|Blue $10</strong>", 'cf7-cost-calculator-price-calculation'  ); ?></span></label><br />
		<label><input type="checkbox" name="include_blank" class="option" /> <?php echo esc_html( __( 'Insert a blank item as the first option', 'cf7-cost-calculator-price-calculation' ) ); ?></label>
		</fieldset>
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'cf7-cost-calculator-price-calculation' ) ); ?></label></th>
	<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
	</tr>
	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'cf7-cost-calculator-price-calculation' ) ); ?></label></th>
	<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
	</tr>
</tbody>
</table>
</fieldset>
</div>
<div class="insert-box">
	<input type="text" name="select_custom" class="tag code" readonly="readonly" onfocus="this.select()" />
	<div class="submitbox">
	<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'cf7-cost-calculator-price-calculation' ) ); ?>" />
	</div>
	<br class="clear" />
	<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'cf7-cost-calculator-price-calculation' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
</div>
<?php
}
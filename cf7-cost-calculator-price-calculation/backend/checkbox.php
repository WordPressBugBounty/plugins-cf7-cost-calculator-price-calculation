<?php
/**
** A base module for [checkbox], [checkbox*], and [radio]
**/
/* form_tag handler */
add_action( 'wpcf7_init', 'wpcf7_add_form_tag_checkbox_custom' );
function wpcf7_add_form_tag_checkbox_custom() {
	wpcf7_add_form_tag( array( 'checkbox_custom', 'checkbox_custom*', 'radio_custom','radio_custom*'  ),
		'wpcf7_checkbox_custom_form_tag_handler', array(
			'name-attr' => true,
			'selectable-values' => true,
		 	'multiple-controls-container' => true ) );
}
function wpcf7_checkbox_custom_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}
	$validation_error = wpcf7_get_validation_error( $tag->name );
	$class = wpcf7_form_controls_class( $tag->type );
	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}
	$label_first = $tag->has_option( 'label_first' );
	$use_label_element = $tag->has_option( 'use_label_element' );
	$exclusive = $tag->has_option( 'exclusive' );
	$free_text = $tag->has_option( 'free_text' );
	$multiple = false;
	if ( 'checkbox_custom' == $tag->basetype) {
		$multiple = ! $exclusive;
	} else { // radio
		$exclusive = false;
	}
	if ( $exclusive ) {
		$class .= ' wpcf7-exclusive-checkbox';
	}
	$atts = array();
	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	if ( $validation_error ) {
		$atts['aria-describedby'] = wpcf7_get_validation_error_reference(
			$tag->name
		);
	}
	$tabindex = $tag->get_option( 'tabindex', 'signed_int', true );
	if ( false !== $tabindex ) {
		$tabindex = (int) $tabindex;
	}
	$html = '';
	$count = 0;
	if ( $data = (array) $tag->get_data_option() ) {
		if ( $free_text ) {
			$tag->values = array_merge(
				array_slice( $tag->values, 0, -1 ),
				array_values( $data ),
				array_slice( $tag->values, -1 ) );
			$tag->labels = array_merge(
				array_slice( $tag->labels, 0, -1 ),
				array_values( $data ),
				array_slice( $tag->labels, -1 ) );
		} else {
			$tag->values = array_merge( $tag->values, array_values( $data ) );
			$tag->labels = array_merge( $tag->labels, array_values( $data ) );
		}
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
	$default_choice = $tag->get_default_option( null, array(
		'multiple' => $multiple,
	) );
	$hangover = wpcf7_get_hangover( $tag->name, $multiple ? array() : '' );
	$i=1;
	foreach ( $values as $key => $value ) {
		if ( $hangover ) {
			$checked = in_array( $value, (array) $hangover, true );
		} else {
			//$checked = in_array( $value, (array) $default_choice, true );
			$checked = false;
			if($default){
				$defaults = explode("|",$default);
				if(in_array($i,$defaults) ){
					$checked =true;
				}else{
					$checked = false;
				}
			}
		}
		if ( isset( $labels[$key] ) ) {
			$label = $labels[$key];
		} else {
			$label = $value;
		}
		if( $tag->basetype == "checkbox_custom" || $tag->basetype == "checkbox_custom*"){
			$type = "checkbox";
		}else{
			$type = "radio";
		}
		$item_atts = array(
			'type' => $type,
			'name' => $tag->name . ( $multiple ? '[]' : '' ),
			'value' => $value,
			'checked' => $checked,
			'tabindex' => $tabindex,
		);
		$item_atts = wpcf7_format_atts( $item_atts );
		if ( $label_first ) { // put label first, input last
			$item = sprintf(
				'<span class="wpcf7-list-item-label">%1$s</span><input %2$s />',
				esc_html( $label ),
				$item_atts
			);
		} else {
			$item = sprintf(
				'<input %2$s /><span class="wpcf7-list-item-label">%1$s</span>',
				esc_html( $label ),
				$item_atts
			);
		}
		if ( $use_label_element ) {
			$item = '<label>' . $item . '</label>';
		}
		if ( false !== $tabindex and 0 < $tabindex ) {
			$tabindex += 1;
		}
		$class = 'wpcf7-list-item';
		$count += 1;
		if ( 1 == $count ) {
			$class .= ' first';
		}
		if ( count( $values ) == $count ) { // last round
			$class .= ' last';
			if ( $free_text ) {
				$free_text_name = $tag->name . '_free_text';
				$free_text_atts = array(
					'name' => $free_text_name,
					'class' => 'wpcf7-free-text',
					'tabindex' => $tabindex,
				);
				if ( wpcf7_is_posted()
				and isset( $_POST[$free_text_name] ) ) {
					$free_text_atts['value'] = wp_unslash( $_POST[$free_text_name] );
				}
				$free_text_atts = wpcf7_format_atts( $free_text_atts );
				$item .= sprintf( ' <input type="text" %s id="%s" />', $free_text_atts,'yeeaddons-'.esc_attr( $tag->name ).'-'.esc_attr($i ) );
				$class .= ' has-free-text';
			}
		}
		$item = '<span for="yeeaddons-'.esc_attr( $tag->name ).'-'.esc_attr($i ).'" class="' . esc_attr( $class ) . '">' . $item . '</span>';
		$html .= $item;
		$i++;
	}
	$html = sprintf(
		'<span class="wpcf7-form-control-wrap" data-name="%1$s"><span %2$s>%3$s</span>%4$s</span>',
		esc_attr( $tag->name ),
		wpcf7_format_atts( $atts ),
		$html,
		$validation_error
	);
	return $html;
}
/* Validation filter */
add_filter( 'wpcf7_validate_checkbox_custom', 'wpcf7_checkbox_custom_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_checkbox_custom*', 'wpcf7_checkbox_custom_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_radio_custom', 'wpcf7_checkbox_custom_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_radio_custom*', 'wpcf7_checkbox_custom_validation_filter', 10, 2 );
function wpcf7_checkbox_custom_validation_filter( $result, $tag ) {
	$tag = new WPCF7_FormTag( $tag );
	$type = $tag->type;
	$name = $tag->name;
	$value = isset( $_POST[$name] ) ? (array) $_POST[$name] : array();
	if ( $tag->is_required() && empty( $value ) ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
	}
	return $result;
}
/* Adding free text field */
add_filter( 'wpcf7_posted_data', 'wpcf7_checkbox_custom_posted_data' );
function wpcf7_checkbox_custom_posted_data( $posted_data ) {
	$tags = wpcf7_scan_form_tags(
		array( 'type' => array( 'checkbox_custom', 'checkbox_custom*', 'radio_custom', 'radio_custom*' ) ) );
	if ( empty( $tags ) ) {
		return $posted_data;
	}
	foreach ( $tags as $tag ) {
		$tag = new WPCF7_FormTag( $tag );
		if ( ! isset( $posted_data[$tag->name] ) ) {
			continue;
		}
		$posted_items = (array) $posted_data[$tag->name];
		if ( $tag->has_option( 'free_text' ) ) {
			if ( WPCF7_USE_PIPE ) {
				$values = $tag->pipes->collect_afters();
			} else {
				$values = $tag->values;
			}
			$last = array_pop( $values );
			$last = html_entity_decode( $last, ENT_QUOTES, 'UTF-8' );
			if ( in_array( $last, $posted_items ) ) {
				$posted_items = array_diff( $posted_items, array( $last ) );
				$free_text_name = sprintf(
					'_wpcf7_%1$s_free_text_%2$s', $tag->basetype, $tag->name );
				$free_text = $posted_data[$free_text_name];
				if ( ! empty( $free_text ) ) {
					$posted_items[] = trim( $last . ' ' . $free_text );
				} else {
					$posted_items[] = $last;
				}
			}
		}
		$new_value = array();
		if( isset($_POST[$tag->name]) ){
			$i=0;
			foreach( $posted_items as $vl ){
				$vls = explode("|",$vl);
				if( count($vls) > 1 ){
					if(  $vls[1] != "" ){
						$new_value[] = $vls[1];
					}else{
						$new_value[] = $vls[0];
					}
				}else{
					$new_value[] = $vls[0];
				}
				$i++;
			}
		}
		$posted_data[$tag->name] = implode(",", $new_value);
	}
	return $posted_data;
}
/* Tag generator */
add_action( 'wpcf7_admin_init',
	'wpcf7_add_tag_generator_checkbox_and_radio_custom', 90 );
function wpcf7_add_tag_generator_checkbox_and_radio_custom() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	if( version_compare(WPCF7_VERSION,"6.0") >= 0 ){
		//remove it
		$tag_generator->add( 'checkbox_custom', __( 'Checkbox price', 'cf7-cost-calculator-price-calculation' ),
		'wpcf7_tag_generator_checkbox_custom_2',array("version"=>2));
		$tag_generator->add( 'radio_custom', __( 'Radio price', 'cf7-cost-calculator-price-calculation' ),
		'wpcf7_tag_generator_checkbox_custom_2',array("version"=>2));
	}else{
		$tag_generator->add( 'checkbox_custom', __( 'Checkbox price', 'contact-form-7-cost-calculator' ),
		'wpcf7_tag_generator_checkbox_custom' );
		$tag_generator->add( 'radio_custom', __( 'Radio price', 'contact-form-7-cost-calculator' ),
		'wpcf7_tag_generator_checkbox_custom' );
	}
}
function wpcf7_tag_generator_checkbox_custom_2( $contact_form, $options = '' ) {
	$field_types = array(
		'checkbox_custom' => array(
			'display_name' => __( 'Checkboxes', 'contact-form-7' ),
			'heading' => __( 'Checkboxes form-tag generator', 'contact-form-7' ),
			'description' => __( 'Generates a form-tag for a group of <a href="https://contactform7.com/checkboxes-radio-buttons-and-menus/">checkboxes</a>.', 'contact-form-7' ),
		),
		'radio_custom' => array(
			'display_name' => __( 'Radio buttons', 'contact-form-7' ),
			'heading' => __( 'Radio buttons form-tag generator', 'contact-form-7' ),
			'description' => __( 'Generates a form-tag for a group of <a href="https://contactform7.com/checkboxes-radio-buttons-and-menus/">radio buttons</a>.', 'contact-form-7' ),
		),
	);
	$basetype = $options['id'];
	$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );
	?>
	<header class="description-box">
		<h3><?php
			echo esc_html( $field_types[$basetype]['heading'] );
		?></h3>
		<p><?php
			$description = wp_kses(
				$field_types[$basetype]['description'],
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
				'with_required' => 'checkbox_custom' === $basetype,
				'select_options' => array(
					$basetype => $field_types[$basetype]['display_name'],
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
function wpcf7_tag_generator_checkbox_custom( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = $args['id'];
	$type = ($type=="checkbox_custom")?"checkbox":"radio";
	if ( 'radio' != $type ) {
		$type = 'checkbox';
	}
	if ( 'checkbox' == $type ) {
		$description = __( "Generate a form-tag for a group of checkboxes. For more details, see %s.", 'contact-form-7-cost-calculator' );
	} elseif ( 'radio' == $type ) {
		$description = __( "Generate a form-tag for a group of radio buttons. For more details, see %s.", 'contact-form-7-cost-calculator' );
	}
	$desc_link = wpcf7_link( 'https://contactform7.com/checkboxes-radio-buttons-and-menus/', __( 'Checkboxes, Radio Buttons and Menus', 'contact-form-7-cost-calculator' ) );
?>
<div class="control-box">
<fieldset>
<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>
<table class="form-table">
<tbody>
<?php if ( 'checkbox' == $type ) : ?>
	<tr>
	<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7-cost-calculator' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7-cost-calculator' ) ); ?></legend>
		<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7-cost-calculator' ) ); ?></label>
		</fieldset>
	</td>
	</tr>
<?php endif; ?>
	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7-cost-calculator' ) ); ?></label></th>
	<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
	</tr>
	<tr>
	<th scope="row"><?php echo esc_html( __( 'Options', 'contact-form-7-cost-calculator' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Options', 'contact-form-7-cost-calculator' ) ); ?></legend>
		<textarea name="values" class="values" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>"></textarea>
		<label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><span class="description">
		<?php echo  __( "One option per line (number|text): Ex: <strong>10|Blue $10</strong>", 'contact-form-7-cost-calculator'  ); ?></span></label><br />
		<label><input type="checkbox" name="label_first" class="option" /> <?php echo esc_html( __( 'Put a label first, a checkbox last', 'contact-form-7-cost-calculator' ) ); ?></label><br />
		<label><input type="checkbox" name="use_label_element" class="option" /> <?php echo esc_html( __( 'Wrap each item with label element', 'contact-form-7-cost-calculator' ) ); ?></label>
<?php if ( 'checkbox' == $type ) : ?>
		<br /><label><input type="checkbox" name="exclusive" class="option" /> <?php echo esc_html( __( 'Make checkboxes exclusive', 'contact-form-7-cost-calculator' ) ); ?></label>
<?php endif; ?>
		</fieldset>
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7-cost-calculator' ) ); ?></label></th>
	<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
	</tr>
	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7-cost-calculator' ) ); ?></label></th>
	<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
	</tr>
</tbody>
</table>
</fieldset>
</div>
<div class="insert-box">
	<?php 
		$type = ($type=="checkbox")?"checkbox_custom":"radio_custom";
	 ?>
	<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />
	<div class="submitbox">
	<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7-cost-calculator' ) ); ?>" />
	</div>
	<br class="clear" />
	<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7-cost-calculator' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
</div>
<?php
}
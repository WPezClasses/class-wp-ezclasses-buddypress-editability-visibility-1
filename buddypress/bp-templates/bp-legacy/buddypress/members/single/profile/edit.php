<?php do_action( 'bp_before_profile_edit_content' );

if ( bp_has_profile( 'profile_group_id=' . bp_get_current_profile_group_id() ) ) :
?>
  <?php
  // lets get setup
  global $field;
  
  $obj_bp_ev = new Class_WP_ezClasses_BuddyPress_Editability_Visibility_1;
  // what are the current visitors visibility permissions
  $arr_current_visitor_has_permissions = $obj_bp_ev->ez_bp_profile_current_visitor_visibility_permissions(); 
  
// print_r( $arr_current_visitor_has_permissions);
  ?>
  
	<?php while ( bp_profile_groups() ) : bp_the_profile_group(); ?>
	
	  <form action="<?php bp_the_profile_group_edit_form_action(); ?>" method="post" id="profile-edit-form" class="standard-form <?php bp_the_profile_group_slug(); ?>">

	  <?php do_action( 'bp_before_profile_field_content' ); ?>

	  <h4><?php printf( __( "Editing '%s' Profile Group", "buddypress" ), bp_get_the_profile_group_name() ); ?></h4>

		<ul class="button-nav">
           <?php bp_profile_group_tabs(); ?>
		</ul>
		<div class="clear"></div>

		<?php 
		while ( bp_profile_fields() ) : bp_the_profile_field(); 
		
		  // field ID
		  $int_field_id = esc_html($field->id);
		  
		  // use our (custom) method to get the fields' visibility level
		  $str_field_ev = $obj_bp_ev->ez_bp_get_the_profile_field_visibility_level();
		  
		  // get the visible value
		  $str_field_visible = $obj_bp_ev->get_level_visible($str_field_ev);
		  
		  // is the field's visibile okay for this visitor?
		  if ( isset($arr_current_visitor_has_permissions[$str_field_visible]) ){ 	 
		    ?>
		    <div<?php bp_field_css_class( 'editfield' ); ?>>
			<?php
			// get the edit value
		    $str_field_edit = $obj_bp_ev->get_level_edit($str_field_ev);
			
		    // is the field's edit okay for this visitor?
			if ( ! isset( $arr_current_visitor_has_permissions[$str_field_edit] )){ 
					   echo '<br> user cannot edit<br>';
					   $str_echo2 = '';
					    $str_echo2 .= '<div class="canteditfield field_' . $int_field_id . ' field_' . strtolower(sanitize_file_name($field->name )) . ' field_type_' . esc_html($field->type) . '">'; 
					  
						  $str_echo2 .= '<label for="field_' . $int_field_id .  '">' . bp_get_the_profile_field_name();

//$str_echo2 .= ' [' . $str_field_ev . ' -  (TODO remove)]';

						  $str_echo2 .= '</label>'; 
						  $str_echo2 .= '<div class="bp-view-the-field">' . bp_get_the_profile_field_value() . '</div>';
							
						  $str_echo2 .= '</div>';
						$str_echo2 .= '</div>';
						
						echo $str_echo2 ;
					} else {
// echo '<br> user can edit<br>';					


				   $field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
				   $field_type->edit_field_html();
				   
//	echo '<br> [' .  $str_field_ev . ' -  (TODO remove)]';


				   do_action( 'bp_custom_profile_edit_fields_pre_visibility' );

				   ?>

				   <?php if ( bp_current_user_can( 'bp_xprofile_change_field_visibility' ) ) : 
				
				   // if (  ez_user_can_visibility( bp_the_profile_field_id()) ){
				 
				   // }
				?>
					<p class="field-visibility-settings-toggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
					 
						<?php printf( __( '1 - Visible to: <span class="current-visibility-level">%s</span>', 'buddypress' ), $obj_bp_ev->ez_bp_get_the_profile_field_visibility_level_label($str_field_ev) ) ?> <a href="#" class="visibility-toggle-link"><?php _e( 'Change', 'buddypress' ); ?></a>
					 	
					</p>

					<div class="field-visibility-settings" id="field-visibility-settings-<?php bp_the_profile_field_id() ?>">
						<fieldset>
							<legend><?php _e( 'Who can see this field?', 'buddypress' ) ?></legend>

							<?php echo $obj_bp_ev->ez_bp_profile_get_visibility_radio_buttons(); ?>

						</fieldset>
						<a class="field-visibility-settings-close" href="#"><?php _e( 'Close', 'buddypress' ) ?></a>
					</div>
				<?php else : ?>
					<div class="field-visibility-settings-notoggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
					   <?php 
					   /**
					    * added conditional because "public" visability was removed
						*/
						if ( bp_get_the_profile_field_id() != '1'){ ?>
						
						<?php  printf( __( '2 - Visibility cannot be changed. Visible to: <span class="current-visibility-level">%s</span>', 'buddypress' ), $obj_bp_ev->ez_bp_get_the_profile_field_visibility_level_label($str_field_ev) ) ?>
						
					<?php } else {
					  echo '<p class="field-visibility-settings-toggle" id="field-visibility-settings-toggle-' . bp_get_the_profile_field_id() . '">';
					    printf( __( '3 - Visibility cannot be changed. Visible to: <span class="current-visibility-level">All See</span>', 'buddypress' ), '');
					  echo '</p>';
					  ?>
					  
					<?php
					  } 
					
					?>
					</div>
				<?php endif ?>

				<?php do_action( 'bp_custom_profile_edit_fields' ); ?>

				<p class="description"><?php bp_the_profile_field_description(); ?></p>
			</div>
		<?php
			} // use can edit 
		  } // user can vis		
		?>

		<?php endwhile; ?>

	<?php do_action( 'bp_after_profile_field_content' ); ?>

	<div class="submit">
		<input type="submit" name="profile-group-edit-submit" id="profile-group-edit-submit" value="<?php esc_attr_e( 'Save Changes', 'buddypress' ); ?> " />
	</div>

	<input type="hidden" name="field_ids" id="field_ids" value="<?php bp_the_profile_group_field_ids(); ?>" />

	<?php wp_nonce_field( 'bp_xprofile_edit' ); ?>

</form>

<?php endwhile; endif; ?>

<?php do_action( 'bp_after_profile_edit_content' ); ?>

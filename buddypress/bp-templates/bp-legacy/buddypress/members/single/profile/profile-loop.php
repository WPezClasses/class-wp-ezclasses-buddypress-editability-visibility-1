<?php do_action( 'bp_before_profile_loop_content' ); ?>

<?php if ( bp_has_profile() ) : ?>

  <?php 
  // lets get setup
  global $field;
  
  $obj_bp_ev = new Class_WP_ezClasses_BuddyPress_Editability_Visibility_1;
  // what are the current visitors visibility permissions
  $arr_current_visitor_has_permissions = $obj_bp_ev->ez_bp_profile_current_visitor_visibility_permissions(); 
  ?>

	<?php while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

		<?php if ( bp_profile_group_has_fields() ) : 
		  
		  $str_echo1 = ''; ?>

		  <?php do_action( 'bp_before_profile_field_content' );

			    $str_echo1 .= '<div class="bp-widget ' . bp_get_the_profile_group_slug() . '">';
				$str_echo1 .= '<h4>' . bp_get_the_profile_group_name() . '</h4>';
								 
				 $str_echo2 = '';
				 
				 while ( bp_profile_fields() ) : bp_the_profile_field(); 
				   // field ID
				   $int_field_id = esc_html($field->id);
				   
				   // use our (custom) method to get the fields' vibility level
				   $str_field_ev = $obj_bp_ev->ez_bp_get_the_profile_field_visibility_level();
				   
				   // get the visible value
				   $str_field_visible = $obj_bp_ev->get_level_visible($str_field_ev);
				   
				   // is the field's visibile okay for this visitor?
				   if ( isset($arr_current_visitor_has_permissions[$str_field_visible]) ){ 
				   
				     $str_echo2 .= '<div class="viewfield field_' . $int_field_id . ' field_' . strtolower(sanitize_file_name($field->name )) . ' field_type_' . esc_html($field->type) . '">'; 
					 $str_echo2 .= '<label for="field_' . $int_field_id .  '">' . bp_get_the_profile_field_name();
					 
		//	$str_echo2 .= ' [' . $str_field_ev . ' -  (TODO remove)]';
					 
					 $str_echo2 .= '</label>'; 
					 $str_echo2 .= '<div class="bp-view-the-field">' . bp_get_the_profile_field_value() . '</div>';
					 
					 $str_echo2 .= '</div>';
					 $str_echo2 .= '</div>';
					 
				   } else {
					// TODO REMOVE echo '<br>' . $int_field_id  . ' - display = false - ' . $str_field_ev .  ' [TODO - remove from profile-loop.php]<br>';  
				   } 	
					  ?>

						<?php do_action( 'bp_profile_field_item' ); ?>

					<?php endwhile; 
					
					// we're only gonna output a group if we know there was something in the group to display. 
					if ( ! empty($str_echo2) ){		
					  echo $str_echo1 . $str_echo2 . '</div>';
					}
					?>
			</div>

			<?php do_action( 'bp_after_profile_field_content' ); ?>

		<?php endif; ?>

	<?php endwhile; ?>

	<?php do_action( 'bp_profile_field_buttons' ); ?>

<?php endif; ?>

<?php do_action( 'bp_after_profile_loop_content' ); ?>

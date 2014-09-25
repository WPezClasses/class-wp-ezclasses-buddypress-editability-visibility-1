<?php
/** 
 * Uses BuddyPress' visability setting to do both editability and visibility
 *
 * Editability is who can edit the field. Visibility is who can see it. Now it's possible to have profile fields that are diplayed on a user's profile but are maintained by a level above them (e.g. HR)
 *
 * PHP version 5.3
 *
 * LICENSE: TODO
 *
 * @package WPezClasses
 * @author Mark Simchock <mark.simchock@alchemyunited.com>
 * @since 0.5.1
 * @license TODO
 */
 
/**
 * == Change Log == 
 *
 * 0.5.1 - 18 Sept 2014 - ADDED bp_is_active( ) checks for 'friends' and 'groups'
 *
 */
 
/**
 * == TODOs ==
 *
 * Check for bp_is_active( 'xprofile' ) ?? http://codex.buddypress.org/developer/bp-is-active/
 */
 
// No WP? Die! Now!!
if (!defined('ABSPATH')) {
	header( 'HTTP/1.0 403 Forbidden' );
    die();
}
 
if (! class_exists('Class_WP_ezClasses_BuddyPress_Editability_Visibility_1') ) {
  class Class_WP_ezClasses_BuddyPress_Editability_Visibility_1 {
  
	protected $_arr_defaults;	
	protected $_arr_ev_levels_processed;
		
    public function __construct(){
		  
	  $this->_arr_defaults = $this->init_defaults();
	  
	  $this->_arr_ev_levels_processed = $this->ev_levels_processed();	

      /**
	   * filter the visability levels to add our own custom versions.
	   *
	   * you need to be sure you instantiate the class durring admin load. the instantiation in the profile templates doesn't help you. 
	   */
      add_filter('bp_xprofile_get_visibility_levels', array($this, 'bp_xprofile_get_visibility_levels_filter'), 1);	  
	}
	
	/**
	 * Ref: http://codex.wordpress.org/Roles_and_Capabilities
	 */
	protected function init_defaults(){
	
	  $arr_defaults = array(
	    'key_delimiter'				=> '_',									// underscore is probably best but if you have other ideas then this is easy to change
		'remap'						=> array('public' => 'user_public'),	// which default BP visibility values would you like to remap? 
		'failsafe_edit'				=> 'hr',								// if all else fails (i.e., edit setting is not longer active), who can edit a field
		'failsafe_visible'			=> 'userplus',							// if all else fails, who can view the field
		
	    'hr_wp_capability'			=> 'edit_published_posts',				// wp author
		'owner_wp_capability'		=> 'moderate_comments',					// wp editor
		'admin_wp_capability'		=> 'delete_plugins',					// wp admin
		'super_wp_capability'		=> 'manage_network',								// wp ms super admin TODO
		
		'use_label_short'			=> array('user' => true),				// which edit / user_is level(s) sees the label_short (as defined below)
		'use_label_short_prefix' 	=> 'Visible to: ',						// TODO - REMOVE?? 

        ); 
	  return $arr_defaults;
	}
		
    /**
	 * Note: The array key (e.g., user_public) will be used as the traditional BP id. 
	 *
	 * IMPORTANT - key format matters! Please be sure to use: {edit string} { $defaults['key_delimiter']} {visible sting}
	 */
    public function editability_visibility_levels(){
  
      $arr_ev_levels =  array(
	  
	    'user_public' 	=> array(
		  'label'	 		=> 'User Edit: All See',
		  'label_short'		=> 'All See',
		  ),
		  
	    'user_loggedin'	=> array(
		  'label' 			=> 'User Edit: Members See',
		  'label_short' 	=> 'Members See',
		  ),
		  
	    'user_friends' 	=> array(
		  'label'			=> 'User Edit: Friends See',
		  'label_short'		=> 'Friends See',		  
		  ),
		  
	    'user_groups' 	=> array(
		  'label' 			=> 'User Edit: Co-Group(s) See',
		  'label_short'		=> 'Co-Group(s) See',		  
		  ),
		  
	    'user_user' 	=> array(
		  'label' 			=> 'User Edit: Only User Sees',
		  'label_short' 	=> 'Only User Sees',		  
		  ),
		  
	    'user_userplus' => array(
		  'label' 			=> 'User Edit: User, HR & Owner Sees',
		  'label_short' 	=> 'User, HR & Owner Sees',		  
		  ),
		  
	    'user_hr' 		=> array(
		  'label' 			=> 'User Edit: Only HR Sees',
		  'label_short' 	=> 'Only HR Sees',		  
		  ),
		  
	    'user_owner' 	=> array(
		  'label' 			=> 'User Edit: Only Owner Sees',
		  'label_short' 	=> 'Only Owner Sees',		  
		  ),
		  
	    'hr_public' 	=> array(
	      'label' 			=> 'HR Edit: All See',
	      'label_short' 	=> 'HR Edit: All See',		  
	      ),
		 
	    'hr_loggedin' 	=> array(
	      'label' 			=> 'HR Edit: Members See',
	      'label_short' 	=> 'HR Edit: Members See',		  
	      ),
		  
	    'hr_friends' 	=> array(
	      'label' 			=> 'HR Edit: Friends See',
		  'label_short' 	=> 'HR Edit: Friends See',
	      ),
		  
	    'hr_groups' 	=> array(
		  'label' 			=> 'HR Edit: Co-Group(s) See',
		  'label_short' 	=> 'HR Edit: Co-Group(s) See',		  
		  ),
		  
	    'hr_user'		=> array(
		  'label'			=> 'HR Edit: Only User Sees',
		  'label_short'		=> 'HR Edit: Only User Sees',		  
		  ),
		  
	    'hr_userplus' => array(
		  'label' 			=> 'HR Edit: User, HR & Owner Sees',
		  'label_short' 	=> 'HR Edit: User, HR & Owner Sees',		  
		  ),		  
		  
	    'hr_hr' 		=> array(
		  'label'			=> 'HR Edit: Only HR Sees',
		  'label_short' 	=> 'HR Edit: Only HR Sees',		  
		  ),
		  
	    'hr_owner'		=> array(
		  'label'			=> 'HR Edit: Only Owner Sees',
		  'label_short'		=> 'HR Edit: Only Owner Sees',		  
		  ),	

	    'owner_hr'		=> array(
		  'label'			=> 'Owner Edit: Only HR Sees',
		  'label_short'		=> 'Owner Edit: Only HR Sees',		  
		  ),

	    'owner_owner'	=> array(
		  'label'			=> 'Owner Edit: Only Owner Sees',
		  'label_short'		=> 'Owner Edit: Only Owner Sees',		  
		  ),		  
		  
		'admin_admin' 	=> array(
	      'label' 			=> 'Admins Edit: Only Admin Sees',
	      'label_short'		=> 'Admins Edit: Only Admin Sees',		  
	      ),	   
	   );
	   
	   return $arr_ev_levels;
    }

	/**
	 * Which custom ev levels are active. simple on / off switch decoupled (so to speak) from the settings defined above
	 */
	protected function ev_levels_active(){

	
	  $arr_ev_levels_active = array(
	  
	    'user_public' 		=> true, 
		'user_loggedin' 	=> true,
		'user_friends' 		=> true, 
		'user_groups'		=> true, 
		'user_user'			=> true,
		'user_userplus'		=> true,
		'user_hr'			=> true,
		'user_owner'		=> true,
		
		'hr_public'			=> true,
		'hr_loggedin'		=> true, 
		'hr_friends'		=> true, 
		'hr_groups'			=> true,
		'hr_user'			=> true,
		'hr_userplus'		=> true,		
		'hr_hr'				=> true,
		'hr_owner'			=> true,
		
		'owner_hr'			=> true,
		'owner_owner'		=> true,
		
		'admin_admin'		=> true,
		
		//TODO ms super admin
	  );
	  return $arr_ev_levels_active;
	}
	
	/**
	 * on init lets just roll it all together and use this array as the definite reference of active editability_visibility levels
	 */
	protected function ev_levels_processed(){
	  	  
	  $arr_ev_levels_processed = array();
	  foreach ( $this->editability_visibility_levels() as $str_ev_level => $arr_value ){
	    // we only want to process active == true
	    if ( $this->ev_levels_active_check($str_ev_level)){
		  $arr_ev_levels_processed[$str_ev_level] = $arr_value;
		  // rather than parsing and reparsing, let's just parse once and array the results
		  $arr_ez_bp_get_the_profile_field_visibility_level_parse = $this->ez_bp_get_the_profile_field_visibility_level_parse($str_ev_level);
		  $arr_ev_levels_processed[$str_ev_level]['edit'] = $arr_ez_bp_get_the_profile_field_visibility_level_parse['edit'];
		  $arr_ev_levels_processed[$str_ev_level]['visible'] = $arr_ez_bp_get_the_profile_field_visibility_level_parse['visible']; 
		}
	  }
      return $arr_ev_levels_processed;
	}
	
	/**
	 * is an ev level legit and active? returns a bool
	 */
	public function ev_levels_active_check($str_ev_level=''){
	   
	   $arr_ev_levels_active = $this->ev_levels_active();	   
	   if ( isset($arr_ev_levels_active[$str_ev_level]) && $arr_ev_levels_active[$str_ev_level] === true ){
         return true;
       } 
	   return false;
	}
	
    /**
	 * What are the ev_levels? with an active === true?
	 *
	 * note: we're *not* adding to the $arr_levels passed in, we're just going to roll in our own
	 */
	public function bp_xprofile_get_visibility_levels_filter($arr_levels = array()) {
	
	//  $arr_ev_levels_processed = $this->_arr_ev_levels_processed;
	  $arr_levels_new = array();
      foreach ( $this->_arr_ev_levels_processed as $str_key => $arr_val){
	    // TODO - additional validation?
	    if ( isset( $arr_val['label'] ) ){
	      $arr_levels_new[$str_key] = array( 'id' => $str_key, 'label' => $arr_val['label'] );
	    }
	  }
	  // our new visibility levels
      return $arr_levels_new;
    }
	

	
	/**
	 * Evaluates current visitor and determines what a vistor is relative to various biz rules. these "flags" are used for managing editability and visibility.  
	 */
	public function ez_bp_profile_current_visitor_is($int_compare_to_user_id=''){
	  
	  global $bp;
	  
	  // start with nothing
	  $arr_visitor_is = array();
		
	  // EVERY visitor gets to see public - if there is any public
	  $arr_visitor_is['public'] = true;
		
	  /**
	   * If the visitor is not loggedin then all other checks are irrelevant; so save time and return if not logged in.
	   */
	  if ( is_user_logged_in() ) {
	    $arr_visitor_is['loggedin'] = true;
	  } else {
	    return $arr_visitor_is;
	  }
	  
	  // now that we're sure we have a logged in visitor use the $bp global to work some magic
	  $int_loggedin_user_id = $bp->loggedin_user->id; 
	  $int_displayed_user_id = $bp->displayed_user->id ;
	  
	  /**
	   * fyi - did you notice, we're going to allow a user id to be passed in and used for the current loggedin user. 
	   * we don't really need this now per se, but it made sense to bake it in for later, just in case.
	   */
	  if ( is_int($int_compare_to_user_id) ){
	    $int_displayed_user_id = $int_compare_to_user_id;
	  }
	  
	  /**
	   * what if you want more layers in your organization? then you only have to customize this one method.
	   */
	  $arr_visitor_is_custom = $this->ez_bp_profile_current_visitor_is_custom($int_compare_to_user_id='');
	  
	  // if we get an array back then merge it in
	  if ( is_array($arr_visitor_is_custom) ){
	    $arr_visitor_is = array_merge($arr_visitor_is, $arr_visitor_is_custom);
	  }
	  
	  // if the loggedin user is the display / compare to user then set the user flag and return 'cause checking friends and groups doesn't make sense. 
	  if ( $int_loggedin_user_id == $int_displayed_user_id ){
	    $arr_visitor_is['user'] = true;
		return $arr_visitor_is;
	  }
	  
	  if ( bp_is_active( 'friends' ) ){
	    if ( friends_check_friendship( $int_displayed_user_id, $int_loggedin_user_id ) ){
	      $arr_visitor_is['friends'] = true;
	    }
	  }
	  
	  
	  if ( bp_is_active( 'groups' ) ){
	    // does the current (loggedin) visitor share any groups with the profile'd person?
	    if ( $int_loggedin_user_id != $int_displayed_user_id ){
	      $arr_displayed_user_id_groups = BP_Groups_Member::get_group_ids( $int_displayed_user_id);
		  $arr_loggedin_user_id_groups = BP_Groups_Member::get_group_ids( $int_loggedin_user_id);
		
		  $arr_intersect_groups = array_intersect($arr_displayed_user_id_groups['groups'], $arr_loggedin_user_id_groups['groups']);
		
		  // intersect means the users have a group(s) in common
		  if ( ! empty($arr_intersect_groups) ){
		    $arr_visitor_is['groups'] = true;
		  } 
	  }
	  }
	  	  
	  return $arr_visitor_is;
	}
	
	/**
	 * The default organization has 3 tiers (staff aka user, hr, owner) + admin. if you want something else then this is the place to make that magic happen. 
	 */	
	protected function ez_bp_profile_current_visitor_is_custom($int_compare_to_user_id=''){
	
	  $arr_visitor_is_custom = array();
	  
	  // the admin is his / her own special entity and is not considered a "lower" role (as is often traditional WP)
	  if ( current_user_can($this->_arr_defaults['hr_wp_capability']) && ! current_user_can($this->_arr_defaults['admin_wp_capability']) ){
	    $arr_visitor_is_custom['hr'] = true;
	  }

// TODO - remove...lets pretend the current loggedin user is hr	  
if (true){
  $arr_visitor_is_custom['hr'] = true;
  $arr_visitor_is_custom['owner'] = true;
}
	  
	  // the admin is his / her own special entity  and is not considered a "lower" role (as is often traditional WP)
	  if ( current_user_can($this->_arr_defaults['owner_wp_capability']) && ! current_user_can($this->_arr_defaults['admin_wp_capability']) ){
	    $arr_visitor_is_custom['owner'] = true;
	  }
	  
	  // admin is a "technical" position and not part of the owner's organization. 
	  if ( current_user_can($this->_arr_defaults['admin_wp_capability']) ){
	    $arr_visitor_is_custom['admin'] = true;
	  }	
	  
	  return $arr_visitor_is_custom;
	}
	
	/**
	 * visitor_is is aggregated into visitor visability. 
	 *
	 * That is, result will serve as THE filter for displaying a profile field to a visitor based on the field's visibility setting. 
	 */
	public function ez_bp_profile_current_visitor_visibility_permissions(){
	
	  $arr_visitor_visibility_permissions = $this->ez_bp_profile_current_visitor_is();
	  
	  /**
	   * technically the isset() should be enough but the check for === true can't hurt
	   *
	   * In other words, if the user is visiting their own profile then they should be able to see fields for friends and groups too since a user is part of those relationships. #Duh
	   */
	  if ( isset($arr_visitor_visibility_permissions['user']) && $arr_visitor_visibility_permissions['user'] === true ){
	    $arr_visitor_visibility_permissions['friends'] = true;
		$arr_visitor_visibility_permissions['groups'] = true;
	  }
	  
	  if ( ( isset($arr_visitor_visibility_permissions['user']) && $arr_visitor_visibility_permissions['user'] === true )
		|| ( isset($arr_visitor_visibility_permissions['hr']) && $arr_visitor_visibility_permissions['hr'] === true )
		|| ( isset($arr_visitor_visibility_permissions['owner']) && $arr_visitor_visibility_permissions['owner'] === true ) ){
	    $arr_visitor_visibility_permissions['userplus'] = true;
	  }
	  
	  return $arr_visitor_visibility_permissions;
	}
	
	/**
	 * replace the default BP functionality with a more robust and sophisticated approach.
	 */
	public function ez_bp_get_the_profile_field_visibility_level(){
	
	  global $bp, $field;
	  
	  $arr_ev_levels_processed = $this->_arr_ev_levels_processed;
	  
	  if ( empty($field->id)){
	    return false; // TODO set a default
	  }
	  
	  //TODO - make sure we're on a profile page.
	  
	  $int_displayed_user_id = $bp->displayed_user->id;
	  // this profile has what vis level as stored in bp_xprofile_visibility_levels
	  $arr_bp_xprofile_visibility_levels = bp_get_user_meta( $int_displayed_user_id, 'bp_xprofile_visibility_levels', true );
	  
	  $obj_field = new BP_XProfile_Field($field->id);
	
	  /**
	   * note: not sure why this (i.e., 'disabled') isn't a bool in BP but it's not. 
	   * so just in case, let's make the value a setting (so it's easy to change later). 
	   */
	  $str_custom_viz = 'disabled';
	  $str_return = '';
	  
	  /**
	   * IF ( the user is not allow to set the vis
	   * OR the vis is blank / empty 
	   * OR the current viz level isn't valid (e.g., have been made active => false) )
	   * THEN >> return the field's default_viz
	   */
	  if ( $obj_field->allow_custom_visibility == $str_custom_viz || ( ! isset($arr_bp_xprofile_visibility_levels[$field->id]) || empty($arr_bp_xprofile_visibility_levels[$field->id]) ) || ! isset($arr_ev_levels_processed[$arr_bp_xprofile_visibility_levels[$field->id]]) ){
	    /**
	     * Technically, it's possible for even the default to be invalid but let's not go there - at least for now. 
	     */
	    $str_return = $obj_field->default_visibility;
	  } else {
	    /**
	     * Okay. The user can set vis but to be safe let's make sure nothing has changed, etc.
	     */
		 
		/**
		 * what's the profile's defined level?
		 * note: the validity was checked in the if above (a few lines)
		 */
	    $str_user_bp_vis_level = $arr_bp_xprofile_visibility_levels[$field->id];
		
	    $str_field_edit = $this->get_level_edit($str_user_bp_vis_level);
		$str_field_visible = $this->get_level_visible($str_user_bp_vis_level);
		
		$str_default_edit = $this->get_level_edit($obj_field->default_visibility);
	    /*
         * has the (default) editability changed? that is, the admin side visibility setting is different.
		 * if so, then we need to make sure the old vis is still valid for the new editability tier / defintions. 
	     */
	    if ( $str_default_edit != $str_field_edit ){
	      // does the vis level exist for the new edit level? 	
          if ( isset($arr_ev_levels_processed[$str_default_edit . '_' . $str_field_visible]) ) {
		    /**
			 * the defaul_edit + field _visible is legit so lets use it
			 */
			// TODO update the actual vis in the DB
		    $str_return = $str_default_edit . '_' . $str_field_visible;
		  } else {
		    // else use the default
	        $str_return = $obj_field->default_visibility;
		  }
	    } else {
	  
	      $str_return = $str_user_bp_vis_level;	  
	    }
	  }
	  // with all that said and done, does the ev_level need to be remapped? as defined in the defaults
	  $arr_remap = $this->_arr_defaults['remap'];
	  if ( isset($arr_remap[$str_return]) ){
	    $str_return = $arr_remap[$str_return];
	  }
	  return $str_return;
    }
	
	/**
	 * pass in an ev_level, get the [edit] back
	 */
    public function get_level_edit($str_ev_level=''){
	
	  $arr_ev_levels_processed = $this->_arr_ev_levels_processed;
	  
	  if ( isset($arr_ev_levels_processed[$str_ev_level],$arr_ev_levels_processed[$str_ev_level]['edit']) ){
	    return $arr_ev_levels_processed[$str_ev_level]['edit'];
	  } else {
	    // if for some reason we can't get the [edit] for the ev_level passed in then return this	  
	    return $this->_arr_defaults['failsafe_edit'];	  
	  }  
    }
	
	/**
	 * pass in an ev_level, get the [visible] back
	 */ 
    public function get_level_visible($str_ev_level=''){
  
	  $arr_ev_levels_processed = $this->_arr_ev_levels_processed;
	  
	  if ( isset($arr_ev_levels_processed[$str_ev_level],$arr_ev_levels_processed[$str_ev_level]['visible']) ){
	    return $arr_ev_levels_processed[$str_ev_level]['visible'];
	  } else {	  
	    // if for some reason we can't get the [visible] for the ev_level passed in then return this
	    return $this->_arr_defaults['failsafe_visible'];	  
	  } 
    }
	 
	/**
	 * Parses the visibility id and returns array[edit] and array[display]
	 */
	function ez_bp_get_the_profile_field_visibility_level_parse($str_ev_level=''){
		  
	  $str_key_delimiter = $this->_arr_defaults['key_delimiter'];
	  	  
	  $arr_ret['edit'] = substr( $str_ev_level, 0, strpos($str_ev_level, $str_key_delimiter) );
	  $arr_ret['visible'] = substr( $str_ev_level, strpos($str_ev_level, $str_key_delimiter)+1, strlen($str_ev_level) );

	  return $arr_ret;
	}
	 
 
	/**
	 * Return the field visibility radio buttons
	 */
	function ez_bp_profile_get_visibility_radio_buttons( $arr_args = '' ) {
	
	  global $field;
	  
	  $arr_ev_levels_processed = $this->_arr_ev_levels_processed;	 
	  $str_ev_level = $this->ez_bp_get_the_profile_field_visibility_level();
	  
	  $str_field_edit = $arr_ev_levels_processed[$str_ev_level]['edit'];
	  $str_field_viz = $arr_ev_levels_processed[$str_ev_level]['visible'];
	  
		// Parse optional arguments
		$r = bp_parse_args( $arr_args, array(
			'field_id'     => bp_get_the_profile_field_id(),
			'before'       => '<ul class="radio">',
			'after'        => '</ul>',
			'before_radio' => '<li>',
			'after_radio'  => '</li>',
			'class'        => 'bp-xprofile-visibility'
		), 'xprofile_visibility_radio_buttons' );

		// Empty return value, filled in below if a valid field ID is found
		$retval = '';

		// Only do-the-do if there's a valid field ID
		if ( ! empty( $r['field_id'] ) ) :

			// Start the output buffer
			ob_start();

			// Output anything before
			echo $r['before']; ?>

			<?php if ( bp_current_user_can( 'bp_xprofile_change_field_visibility' ) ) : ?>

				<?php 
				$str_label_needle = ':';
				foreach( bp_xprofile_get_visibility_levels() as $level ) : 
				
				  $str_edit = $this->get_level_edit($level['id']);
				  $str_viz = $this->get_level_visible($level['id']);
				  
				  if ( $str_edit == $str_field_edit ){
				    echo $r['before_radio']; 
					?>
					  
					<label for="<?php echo esc_attr( 'see-field_' . $r['field_id'] . '_' . $level['id'] ); ?>">
						<input type="radio" id="<?php echo esc_attr( 'see-field_' . $r['field_id'] . '_' . $level['id'] ); ?>" name="<?php echo esc_attr( 'field_' . $r['field_id'] . '_visibility' ); ?>" value="<?php echo esc_attr( $level['id'] ); ?>" <?php checked( $level['id'], bp_get_the_profile_field_visibility_level() ); ?> />
						<span class="field-visibility-text"><?php echo esc_html( $this->ez_bp_get_the_profile_field_visibility_level_label($level['id'] )); ?></span>
					</label>

					<?php 
					echo $r['after_radio']; 
				  }
				  ?>

				<?php endforeach; ?>

			<?php endif;

			// Output anything after
			echo $r['after'];

			// Get the output buffer and empty it
			$retval = ob_get_clean();
		endif;

		return apply_filters( 'bp_profile_get_visibility_radio_buttons', $retval, $r, $arr_args );
	}
	
	 
	/**
	 * What (visibility) label should we display? Users (probably) should not be aware of the hr, owner and admin edit / visibility possibilites.
	 */
	function ez_bp_get_the_profile_field_visibility_level_label($str_ev_level=''){
	
	  $arr_defaults = $this->_arr_defaults;
	  $arr_ev_levels_processed = $this->_arr_ev_levels_processed;
	  
	  // is the ev_level active?
      if ( isset($arr_ev_levels_processed[$str_ev_level]) ){
	  	// do we have values for everything we might need?
	    if ( isset( $arr_ev_levels_processed[$str_ev_level]['edit'], $arr_ev_levels_processed[$str_ev_level]['label'], $arr_ev_levels_processed[$str_ev_level]['label_short'], $arr_defaults['use_label_short'] ) && is_string($arr_ev_levels_processed[$str_ev_level]['edit']) ){
	      $str_level_edit = $arr_ev_levels_processed[$str_ev_level]['edit'];
		  $arr_use_label_short = $arr_defaults['use_label_short'];
		  // if the edit level is in the use_label_short array then we return the label_short 
		  if ( isset($arr_use_label_short[$str_level_edit]) && $arr_use_label_short[$str_level_edit] === true ){
		    return $arr_ev_levels_processed[$str_ev_level]['label_short'];
		  } else {
		    // else we return the (full) label
		    return $arr_ev_levels_processed[$str_ev_level]['label'];
		  }
	    } 
		return 'TODO - something required is missing';
	  }
	  return 'TODO - the ev_level is no longer active. Now what?';
	}
	
  }
}
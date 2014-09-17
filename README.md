Class_WP_ezClasses_BuddyPress_Editability_Visibility_1
======================================================

Uses the standard WordPress BuddyPress profile field visibility setting to provide both Editability and Visibility.

Editabiliy - Who can edit the profile field.

Visibility - Who can see the profile field.


#### Examples:
- a profile field can be edited by the user and only visible to a user's friends.
- a profile field can be edited by HR (Human Resources) and be visilble to all site visitors.
- a profile field can be edited by HR and only be visible to other HR level users. 
- a profile field can be edited by Owner and only visible to other Owner level users. 


HR, Owner and Admin user levels are each defined using standard WordPress capabilites and the current_user_can() function. Adding additional custom levels can be done by refactoring the method: ez_bp_profile_current_visitor_is_custom().

BuddyPress' Per-Member Visibility setting continues to function as usual.

Note: Also added was a Co-Groups visibility. That is, do the (logged in) visitor to the profile page's user have a group(s) in common. For large communities this could be used as a backdoor for stalking. That is, the stalker only need to be approved to join a group the stalkee is in. In most cases the stalkee might not have control over who gets into his / her groups.

That said, for smaller communities Co-Groups might not be a bad visibility option to have. 


#### Profile Demo

Open the buddypress/ folder (within the repo) and drill down to the bottom to see examples of how the members / single / profile / edit.php and profile-loop.php templates need to be changed in order to utilze Editability and Visibility.
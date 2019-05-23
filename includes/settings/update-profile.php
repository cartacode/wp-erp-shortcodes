<?php
add_shortcode( 'ac-update-profile', function() {
	wp_enqueue_script( 'settings-js' );

	$current_user = wp_get_current_user();
	$employee = new WeDevs\ERP\HRM\Employee( get_current_user_id() );
	$photo_id = $employee->get_photo_id();
	$avatar_url = $employee->get_avatar_url( $photo_id );

	ob_start();
?>

	<form class="update-profile-form" method="post" enctype="multipart/form-data">
		<div class="update-profile-wrapper d-flex">
			<div class="avatar-wrapper">
				<img class="profile-pic" src="<?php _e( esc_attr( $avatar_url ) ) ?>" />
				<div class="upload-button">
					<i class="fa fa-upload" aria-hidden="true"></i>
				</div>
				<input class="file-upload" name="avatar" type="file" accept="image/*"/>
			</div>

			<div class="user-info-wrapper">
				<div class="form-group name">
					<label class="w-100">Name: </label>
			
					<div class="first-name flex-column">
						<input type="" name="first_name" required="" value="<?php _e( esc_attr( $current_user->user_firstname ) ) ?>">
						<span>First</span>
					</div>
			
					<div class="last-name flex-column">
						<input type="" name="last_name" required="" value="<?php _e( esc_attr( $current_user->user_lastname ) ) ?>">
						<span>Last</span>
					</div>
				</div>
			
				<div class="form-group email">
					<label class="w-100">Email:</label>
			
					<div class="">
						<input type="email" name="user_email" required="" value="<?php _e( esc_attr( $current_user->user_email ) ) ?>">
					</div>
				</div>
			
				<div class="form-group password">
					<label class="w-100">Password:</label>
					
					<div class="real-password flex-column">
						<input type="password" name="password" required="" placeholder="Enter Password">
					</div>
			
					<div class="confirm-password flex-column">
						<input type="password" name="confirm_password" required="" placeholder="Confirm Password">
					</div>
				</div>
			</div>
		</div>

		<?php wp_nonce_field( 'update-profile' ) ?>
		<button id="submit" type="submit" disabled="">Update</button>
		<?php 
				$ACGoogleSSOGmail = new ACGoogleSSOGmail();
				echo $ACGoogleSSOGmail->generate_sso_button();
		?>
	</form>
<?php
	$html = ob_get_contents();
	ob_clean();

	return $html;
} );
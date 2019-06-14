<?php
add_shortcode( 'ac-update-profile', function() {
	wp_enqueue_script( 'settings-js' );

	$current_user = wp_get_current_user();
	$employee = new WeDevs\ERP\HRM\Employee( get_current_user_id() );
	$photo_id = $employee->get_photo_id();
	$avatar_url = $employee->get_avatar_url( $photo_id );
	$work_phone = $employee->get_work_phone();
	$description = $employee->get_data()['personal']['description'];

	ob_start();
?>

	<form class="update-profile-form" method="post" enctype="multipart/form-data">
		<div class="update-profile-wrapper d-flex">
			<div class="avatar-wrapper img-upload-wrapper">
				<img class="profile-pic" src="<?php _e( esc_attr( $avatar_url ) ) ?>" />
				<div class="upload-button">
					<i class="fa fa-upload" aria-hidden="true"></i>
				</div>
				<input class="file-upload" name="avatar" type="file" accept="image/*"/>
			</div>

			<div class="user-info-wrapper info-wrapper">
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
					<div class="flex-column">
						<label>Email:</label>
						<input type="email" name="user_email" required="" value="<?php _e( esc_attr( $current_user->user_email ) ) ?>">
					</div>

					<div class="flex-column">
						<label>Work Phone:</label>			
						<input type="text" name="work_phone" required="" value="<?php _e( esc_attr( $work_phone ) ) ?>" />
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

				<div class="form-group">
					<label class="w-100">Biography:</label>
					<textarea class="w-100" rows="5" name="description"><?php _e( $description ); ?></textarea>
				</div>

			<?php if ( !current_wp_erp_user_is( 'broker' ) && !current_wp_erp_user_is( 'staff' ) && !current_user_can( 'administrator' ) ): ?>
				<div class="form-group">
					<label class="w-100">DRE:</label>
					<div class="">
						<input type="text" name="dre_number" />
					</div>
				</div>
			<?php endif; ?>
			</div>
		</div>

		<?php wp_nonce_field( 'update-profile' ) ?>
		<button id="submit" type="submit" disabled="">Update</button>
		<?php
			if ( class_exists( 'ACGoogleSSOGmail' ) ) {
				$ACGoogleSSOGmail = new ACGoogleSSOGmail();
				echo $ACGoogleSSOGmail->generate_sso_button();
			}
		?>
	</form>


<?php if ( current_wp_erp_user_is( 'broker' ) || current_wp_erp_user_is( 'staff' ) ): ?>
	<form class="update-office-profile-form" method="post" enctype="multipart/form-data">
		<div class="office-profile-wrapper d-flex">
			<?php 
				$photo_id = get_user_meta( get_current_user_id(), 'office_logo', true );
				$office_logo_url = $photo_id ? wp_get_attachment_url( $photo_id ) : '';
			?>
			<div class="office-logo-wrapper img-upload-wrapper">
				<img class="profile-pic" src="<?php esc_attr_e( $office_logo_url ); ?>" />
				<div class="upload-button">
					<i class="fa fa-upload" aria-hidden="true"></i>
				</div>
				<input class="file-upload" name="office_logo" type="file" accept="image/*"/>
			</div>

			<div class="office-info-wrapper info-wrapper">
				<div class="form-group">
					<div class="flex-column">
						<label>Office Address 1:</label>
						<input type="text" name="office_address_1" value="<?php esc_attr_e( get_user_meta( get_current_user_id(), 'office_address_1', true ) ); ?>" />
					</div>

					<div class="flex-column">
						<label>Office Address 2:</label>
						<input type="text" name="office_address_2" value="<?php esc_attr_e( get_user_meta( get_current_user_id(), 'office_address_2', true ) ); ?>" />
					</div>
				</div>

				<div class="form-group">
					<div class="flex-column">
						<label>Office City:</label>
						<input type="text" name="office_city" value="<?php esc_attr_e( get_user_meta( get_current_user_id(), 'office_city', true ) ); ?>" />
					</div>

					<div class="flex-column">
						<label>Office State:</label>
						<input type="text" name="office_state" value="<?php esc_attr_e( get_user_meta( get_current_user_id(), 'office_state', true ) ); ?>" />
					</div>
				</div>

				<div class="form-group">
					<div class="flex-column">
						<label>Office Zipcode:</label>
						<input type="text" name="office_zip" value="<?php esc_attr_e( get_user_meta( get_current_user_id(), 'office_zip', true ) ); ?>" />
					</div>

					<div class="flex-column">
						<label>Office DRE:</label>
						<input type="text" name="office_dre_number" value="<?php esc_attr_e( get_user_meta( get_current_user_id(), 'office_dre_number', true ) ); ?>" />
					</div>
				</div>
			</div>
		</div>

		<?php wp_nonce_field( 'update-office-profile' ) ?>
		<button type="submit">Update</button>
	</form>
<?php endif; ?>
<?php
	$html = ob_get_contents();
	ob_clean();

	return $html;
} );
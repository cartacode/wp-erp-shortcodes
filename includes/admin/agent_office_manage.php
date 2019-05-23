<?php
class Agent_Office_Manage {
	private static $instance = null;
	
	static function getInstance() {
		if ( is_null( self::$instance ) )
			self::$instance = new Agent_Office_Manage();

		return self::$instance;
	}

	function __construct() {
		add_action( 'admin_menu', function() {
			add_submenu_page( 'erp', __( 'Brokerage Office Management', 'erp' ), __('Brokerage Office Management', 'erp' ), 'manage_options', 'erp-broker-office', array( $this, 'overview_page' ) );
		}, 101);
	}

	function overview_page() {
    $agent_email = '';
    $agent_office = null;
    $error_msg = '';

    if ( !empty( $_REQUEST['action'] ) ) {
      switch ( $_REQUEST['action'] ) {
        case 'get-agent-office': 
          $agent_email = $_REQUEST['agent_email'];
          
          if ( function_exists( 'get_brokerage_office_by_agent_user_id' ) ) {
            $agent_user = get_user_by( 'email', $agent_email );
            if ( !$agent_user ) {
              $error_msg = 'User is not found.';
              break;
            }

            $agent_office = get_brokerage_office_by_agent_user_id( $agent_user->ID );
          }
          break;
        
        case 'update-agent-office':
          if ( !empty( $_REQUEST['new_office']) ) {
            $agent_email = $_REQUEST['agent_email'];
            $agent_user = get_user_by( 'email', $agent_email );
            update_user_meta( $agent_user->ID, 'brokerage_office', $_REQUEST['new_office'] );
            $agent_office = $_REQUEST['new_office'];
          }
          break;
      }
    }
	?>
		<div class="">
			<h1>Broker office management</h1>

			<form action="<?php esc_attr_e( admin_url( 'admin.php' ) ); ?>">
				<div style="display: flex;align-items: center;">
          <input type="hidden" name="page" value="erp-broker-office" />
          <input type="hidden" name="action" value="get-agent-office" />
          
					<label style="min-width: 75px;">Agent Email: </label>
					<input type="email" name="agent_email" required value="<?php esc_attr_e( $agent_email )?>">
          <input type="submit" class="button button-primary">
				</div>
        
        <?php if ( !empty( $error_msg ) ): ?> <p><small><?php _e( $error_msg ); ?></small></p><?php endif;?>
			</form>
      
      <?php if ( !is_null( $agent_office ) ): ?>
        <hr style="margin: 2em 0px;">
        <h2>Agent Office</h2>
        <form action="<?php esc_attr_e( admin_url( 'admin.php' ) ); ?>">
          <div style="display: flex;align-items: center;">
            <input type="hidden" name="page" value="erp-broker-office" />
            <input type="hidden" name="action" value="update-agent-office" />
            <input type="hidden" name="agent_email" value="<?php esc_attr_e( $agent_email ); ?>" />
            
            <label style="min-width: 75px;">Update to: </label>
            <select name="new_office" required>
              <option value="">Not selected</option>
              
              <?php foreach( array_values( BROKERAGE_OFFICES ) as $office_name ): ?>
                <option <?php esc_attr_e( $office_name == $agent_office ? 'selected' : '' ); ?> value="<?php esc_attr_e( $office_name ); ?>"><?php _e( $office_name ); ?></option>
              <?php endforeach; ?>
            </select>
            
            <input type="submit" class="button button-primary" value="Update">
          </div>

        </form>

      <?php endif; ?>

		</div>
	<?php
	}
}

function ERP_AOM() {
	return Agent_Office_Manage::getInstance();
}

ERP_AOM();
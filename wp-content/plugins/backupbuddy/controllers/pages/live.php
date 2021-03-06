<?php
if ( ! is_admin() ) { die( 'Access denied' ); }

// Check if running PHP 5.3+.
$php_minimum = 5.3;
if ( version_compare( PHP_VERSION, $php_minimum, '<' ) ) { // Server's PHP is insufficient.
	echo '<br>';
	pb_backupbuddy::alert( __( 'BackupBuddy Stash Live requires PHP version 5.3 or newer to run. Please upgrade your PHP version or contact your host for details on upgrading.', 'it-l10n-backupbuddy' ) . ' ' . __( 'Current PHP version', 'it-l10n-backupbuddy' ) . ': ' . PHP_VERSION );
	return;
}


// No PHP runtime calculated yet. Try to see if test is finished.
if ( 0 == pb_backupbuddy::$options['tested_php_runtime'] ) {
	backupbuddy_core::php_runtime_test_results();
}

$liveDestinationID = false;
foreach( pb_backupbuddy::$options['remote_destinations'] as $destination_id => $destination ) {
	if ( 'live' == $destination['type'] ) {
		$liveDestinationID = $destination_id;
		break;
	}
}


// Handle disconnect.
if ( ( 'disconnect' == pb_backupbuddy::_GET( 'live_action' ) ) && ( false !== $liveDestinationID ) ) { // If disconnecting and not already disconnected.
	pb_backupbuddy::verify_nonce();
	
	// Clear destination settings.
	unset( pb_backupbuddy::$options['remote_destinations'][ $liveDestinationID ] );
	pb_backupbuddy::save();
	
	// Clear cached Live credentials.
	require_once( pb_backupbuddy::plugin_path() . '/destinations/live/init.php' );
	delete_transient( pb_backupbuddy_destination_live::LIVE_ACTION_TRANSIENT_NAME );
	
	pb_backupbuddy::disalert( '', 'You have disconnected from Stash Live.' );
	$liveDestinationID = false;
}



// Show setup screen if not yet set up.
if ( false === $liveDestinationID ) {
	require_once( pb_backupbuddy::plugin_path() . '/destinations/live/_live_setup.php' );
	return;
}



// Load normal manage page.



pb_backupbuddy::$ui->title( __( 'BackupBuddy Stash Live', 'it-l10n-backupbuddy' ) . '&nbsp;&nbsp;<a href="' . pb_backupbuddy::ajax_url( 'live_settings' ) . '&#038;TB_iframe=1&#038;width=640&#038;height=600" class="add-new-h2 thickbox">' . __( 'Settings', 'it-l10n-backupbuddy' ) . '</a>' );

$destination = pb_backupbuddy::$options['remote_destinations'][ $liveDestinationID ];
$destination_id = $liveDestinationID;
require_once( pb_backupbuddy::plugin_path() . '/destinations/live/_manage.php' ); // Expects incoming vars: $destination, $destination_id.



<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

class Mfn_Update extends Mfn_API {

	protected $code = '';

	/**
	 * Mfn_Update constructor
	 */

	public function __construct(){

		$this->code = mfn_get_purchase_code();

		// It runs when wordpress check for updates
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'pre_set_site_transient_update_themes' ) );

		// This action runs when WordPress completes its upgrade process
		add_action( 'upgrader_process_complete', array( $this, 'upgrader_process_complete' ), 10, 2 );

	}

	/**
	 * Filter WP Update transient
	 *
	 * @param unknown $transient
	 * @return unknown
	 */

	public function pre_set_site_transient_update_themes( $transient ) {

		if( ! mfn_is_registered() ){
			return $transient;
		}

		$new_version = $this->remote_get_version();
		$theme_template = get_template();

		if( version_compare( wp_get_theme( $theme_template )->get( 'Version' ), $new_version, '<' ) ) {

			$args = array(
				'action' => 'theme',
				'code' => $this->code,
			);

			if( 2 == mfn_get_api_version() ){
				$url = 'api2';
			} else {
				$url = 'theme_download';
			}

			$transient->response[ $theme_template ] = array(
				'theme' => $theme_template,
				'new_version' => $new_version,
				'url' => $this->get_url( 'changelog' ),
				'package' => add_query_arg( $args, $this->get_url( $url ) ),
			);

		}

		return $transient;
	}

	/**
	 * This function runs when WordPress completes its upgrade process
	 * It iterates through each plugin updated to see if ours is included
	 * @param $upgrader_object Array
	 * @param $options Array
	 */

	function upgrader_process_complete( $upgrader_object, $options ) {

		// print_r( [$upgrader_object, $options] );

		if( $options['action'] == 'update' && $options['type'] == 'theme' && isset( $options['themes'] ) ) {
			foreach( $options['themes'] as $theme ) {

				if( 'betheme' == $theme ){

					$version = MFN_THEME_VERSION;
					$updates = get_site_option( 'betheme_updates_history' );

					if( empty($updates) ){
						$updates = [];
					}

					$updates[] = [
						'time' => time(),
						'version' => $version,
					];

					update_site_option( 'betheme_updates_history', $updates );

				}

			}
		}

	}

}

$mfn_update = new Mfn_Update();

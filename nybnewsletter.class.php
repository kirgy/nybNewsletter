<?php
/**
*	NybNewletter Class
*	==============================================================
*	@author: 	Chris McKirgan @kirgy, @chrismckirgan
*	@date: 		17/04/15
*	
*	@Description: 	Allows users to collect emails addresses from a subscribe
*					form, and store them in a database.
*/


class nybnewlstter {

	public function __construct() {
		$this->aSettings = array(
			'table'	=> array(
				'prefix'	=> 'nybNewletter_'
			),
		);
	}

	public function __destruct() {

	}

	/**
	*	Called on plugin activation
	*
	*	returns void
	*/
	public function activate() {
	  global $wpdb;

	  /*
	   * We'll set the default character set and collation for this table.
	   * If we don't do this, some characters could end up being converted 
	   * to just ?'s when saved in our table.
	   */
	  $charset_collate = '';

	  if ( ! empty( $wpdb->charset ) ) {
		$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	  }

	  if ( ! empty( $wpdb->collate ) ) {
		$charset_collate .= " COLLATE {$wpdb->collate}";
	  }

	  $sPrefix = $this->aSettings['table']['prefix'];

	  $sql = "CREATE TABLE nybNewsletter (
		{$sPrefix}_id mediumint(9) NOT NULL AUTO_INCREMENT,
		{$sPrefix}_firstName varchar(32),
		{$sPrefix}_lastName varchar(32),
		{$sPrefix}_email  varchar(255) NOT NULL,
		{$sPrefix}_IP varchar(255) NOT NULL,
		{$sPrefix}_subscribed bool NOT NULL,
		{$sPrefix}_unsubDate datetime NOT NULL,
		{$sPrefix}_created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		{$sPrefix}_updated timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
		UNIQUE KEY {$sPrefix}_id ({$sPrefix}_id)
	  ) $charset_collate;";

	  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	  dbDelta( $sql );		
		
	}

	/**
	*	called on plugin disactivation
	*
	*	returns void
	*/
	public function disactivate() {

	}

	/**
	* 	called by constructor to check if there is a $_POST set
	*
	*	returns void
	*/
	public function process_form() {
		// @TODO: process form submitted from front-end and perform CREATE crud action on db table.
		if(isset($_POST['nybnewletter']) /*&& $bIsFromServer*/ ) {  //@TODO: set bIsFromServer
			$this->crud('CREATE', $_POST);
		}

	}

	public function crud($sAction) {

		switch ($sAction) {
			case 'CREATE':
				// @TODO: insert data into database
				/*
					1) validate data
					2) insert with sprint_f
					3) return true/false
				*/
				$sPrefix = $this->aSettings['table']['prefix']
				global $wpdb;
				$wpdb->insert( $sTable, array(
					$sPrefix . 'firstName'		=> $_POST[$sPrefix . 'firstName'],
					$sPrefix . 'lastName'		=> $_POST[$sPrefix . 'lastName'],
					$sPrefix . 'email'			=> $_POST[$sPrefix . 'email'],
					$sPrefix . 'IP'				=> $_SERVER['REMOTE_ADDR'],
					$sPrefix . 'subscribed'		=> 1,
					$sPrefix . 'unsubDate'		=> null,
					$sPrefix . 'created'		=> date("Y-m-d H:i:s"),
					$sPrefix . 'updated'		=> date("Y-m-d H:i:s"),
				));

				break;
			
			default:
				//@TODO: report error
				break;
		}
	}

	/**
	*	Retunrs the HTML of the subscribe form
	*
	*	returns String
	*/
	public static function getform() {

		$sForm = '	<div class="subscribe-form-wrap">
						<form action="/" method="post" onclick="return false;">
						<div class="email-input-wrap">
							<input type="text" name="subscribeEmail" maxlength="255">
						</div><!--
					!--><div class="cta-input-wrap">
							<input type="submit" class="primary-form-cta subscribe-email-click" value="subscribe">
							<input type="text" name="email_x" class="noshow">
						</div>
						</form>
					</div>';

		return $sForm;

	}

	/**
	*	Returns the shortcode data used by the Wordpress function
	*
	*	@return String
	*/

	public static function doShortcode() {
		// wrapper fuction for getForm()
		return self::getForm();
	}

}



?>
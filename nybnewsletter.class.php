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


class nybNewletter {

	public function __construct() {
		$this->aSettings = array(
			'table'	=> array(
				'prefix'	=> 'nybNewletter_'
			),
		);
		$this->aError = array();
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

	  $sql = "CREATE TABLE {$wpdb->prefix}nybNewsletter (
		{$sPrefix}id mediumint(9) NOT NULL AUTO_INCREMENT,
		{$sPrefix}firstName varchar(32),
		{$sPrefix}lastName varchar(32),
		{$sPrefix}email  varchar(255) NOT NULL,
		{$sPrefix}IP varchar(255) NOT NULL,
		{$sPrefix}subscribed bool NOT NULL,
		{$sPrefix}unsubDate datetime NOT NULL,
		{$sPrefix}created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		{$sPrefix}updated timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY {$sPrefix}id ({$sPrefix}id),
		UNIQUE KEY {$sPrefix}email ({$sPrefix}email)
	  ) $charset_collate;";

	  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	  dbDelta( $sql );		
	  $this->log('Plugin activated');
		
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

	/**
	*	Performs a CRUD action on the database table.
	*
	*	@return Bool
	*/
	public function crud($sAction) {

		switch ($sAction) {
			case 'CREATE':
				// @TODO: insert data into database
				/*
					1) validate data
					2) insert with sprint_f
					3) return true/false
				*/
				$sPrefix 	= $this->aSettings['table']['prefix'];
				if(isset($_POST[$sPrefix . 'firstName']) && isset($_POST[$sPrefix . 'lastName']) && isset($_POST[$sPrefix . 'email'])) {
					if( strlen($_POST[$sPrefix . 'firstName'])<1 || strlen($_POST[$sPrefix . 'lastName'])<1 || ((bool) (is_email($_POST[$sPrefix . 'email'])))==false ) {
						$this->aError[] = 'Please complete all fields';
						$this->log('Not all fields were set.');
					} else {
						$this->log('Processing CREATE.');
						global $wpdb;
						$sTable		= $wpdb->prefix . 'nybNewsletter';
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
					}
				} else {
					$this->aError[] = 'Please complete all required fields.';
					$this->log('The required fields for creating a new table entry were not met.');
				}

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
	public function getform() {
		$sErrors = '';
		if(count($this->aError) > 0) {
			foreach ($this->aError as $sError) {
				$sErrors .= '<p>' . $sError . '</p>';
			}

			$sErrors = '<div class="nybnewsletter-errors">' . $sErrors . '</div>';
		}

		$sForm = '	<div class="subscribe-form-wrap" id="nybNewletter-form">
						' . $sErrors . '
						<form action="#nybNewletter-form" name="nybNewslettersubscribeform" id="nybNewslettersubscribeform" method="post" onclick="//return false;" target="nybnewsletter_iframe">
						<div class="email-input-wrap">
							<input type="hidden" name="nybnewletter" maxlength="255">
							<input type="text" name="nybNewletter_email" id="nybNewletter_email" maxlength="255">
							<input type="hidden" name="nybNewletter_lastName" id="nybNewletter_lastName" maxlength="255">
							<input type="hidden" name="nybNewletter_firstName" id="nybNewletter_firstName" maxlength="255">
						</div><!--
					!--><div class="cta-input-wrap">
							<button class="primary-form-cta ajax-block" id="nybnewsletter-submit" value="subscribe" dataset="nybnewsletter: :" />Subscribe</button>
							<input type="submit" class="primary-form-cta subscribe-email-click hide-field" value="subscribe">
							<input type="text" name="email_x" class="noshow">
							</form>
							<iframe src="" name="nybnewsletter_iframe" id="nybnewsletter_iframe" style="display: none;"></iframe>
						</div>
					</div>';

		return $sForm;

	}

	/**
	*	Returns the shortcode data used by the Wordpress function
	*
	*	@return void
	*/

	public function doShortcode() {
		// wrapper fuction for getForm()
		echo $this->getForm();
		return null;
	}

	public function log($sLog) {
		error_log(':: [NybNewletter] ::: ' . $sLog);
	}

}



?>
<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSEventsProPlaceholders {
	
	protected static $globals = array(
		'{EventName}' 				=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_NAME',
		'{EventLink}' 				=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_LINK',
		'{EventDescription}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_DESCRIPTION',
		'{EventStartDate}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_START_DATE',
		'{EventStartDateOnly}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_START_DATE_ONLY',
		'{EventStartTime}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_START_TIME',
		'{EventEndDate}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_END_DATE',
		'{EventEndDateOnly}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_END_DATE_ONLY',
		'{EventEndTime}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_END_TIME',
		'{Owner}' 					=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_OWNER',
		'{OwnerUsername}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_OWNER_USERNAME',
		'{OwnerName}' 				=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_OWNER_NAME',
		'{OwnerEmail}' 				=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_OWNER_EMAIL',
		'{EventURL}' 				=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_URL',
		'{EventPhone}' 				=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_PHONE',
		'{EventEmail}' 				=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_EMAIL',
		'{LocationName}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_LOCATION_NAME',
		'{LocationLink}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_LOCATION_LINK',
		'{LocationDescription}' 	=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_LOCATION_DESCRIPTION',
		'{LocationURL}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_LOCATION_URL',
		'{LocationAddress}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_LOCATION_ADDRESS',
		'{EventCategories}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_CATEGORIES',
		'{EventTags}' 				=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_TAGS',
		'{EventIconSmall}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_SMALL_ICON',
		'{EventIconBig}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_BIG_ICON'
	);
	
	public static function get($type) {
		$type = strtolower($type);
		return method_exists('RSEventsProPlaceholders', $type) ? self::$type() : array();
	}
	
	public static function registration() {
		$registration = array(
			'{user}'			=> 'COM_RSEVENTSPRO_PLACEHOLDER_USER',
			'{TicketInfo}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_TICKETS_INFO',
			'{TicketsTotal}' 	=> 'COM_RSEVENTSPRO_PLACEHOLDER_TICKETS_TOTAL',
			'{Discount}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_DISCOUNT',
			'{Tax}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_TAX',
			'{LateFee}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_LATE_FEE',
			'{EarlyDiscount}' 	=> 'COM_RSEVENTSPRO_PLACEHOLDER_EARLY_DISCOUNT',
			'{Gateway}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_GATEWAY',
			'{IP}' 				=> 'COM_RSEVENTSPRO_PLACEHOLDER_IP',
			'{Coupon}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_COUPON',
			'{barcodetext}' 	=> 'COM_RSEVENTSPRO_PLACEHOLDER_BARCODE_TEXT',
			'{PaymentURL}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_PAYMENT_URL'
		);
		
		return array_merge(self::$globals, $registration);
	}
	
	public static function activation() {
		$activation = array(
			'{user}'			=> 'COM_RSEVENTSPRO_PLACEHOLDER_USER',
			'{TicketInfo}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_TICKETS_INFO',
			'{TicketsTotal}' 	=> 'COM_RSEVENTSPRO_PLACEHOLDER_TICKETS_TOTAL',
			'{Discount}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_DISCOUNT',
			'{Tax}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_TAX',
			'{LateFee}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_LATE_FEE',
			'{EarlyDiscount}' 	=> 'COM_RSEVENTSPRO_PLACEHOLDER_EARLY_DISCOUNT',
			'{Gateway}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_GATEWAY',
			'{IP}' 				=> 'COM_RSEVENTSPRO_PLACEHOLDER_IP',
			'{Coupon}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_COUPON',
			'{barcode}'		 	=> 'COM_RSEVENTSPRO_PLACEHOLDER_BARCODE',
			'{barcodetext}' 	=> 'COM_RSEVENTSPRO_PLACEHOLDER_BARCODE_TEXT'
		);
		
		return array_merge(self::$globals, $activation);
	}
	
	public static function unsubscribe() {
		$unsubscribe = array(
			'{user}'			=> 'COM_RSEVENTSPRO_PLACEHOLDER_USER',
			'{barcodetext}' 	=> 'COM_RSEVENTSPRO_PLACEHOLDER_BARCODE_TEXT'
		);
		
		return array_merge(self::$globals, $unsubscribe);
	}
	
	public static function denied() {
		$denied = array(
			'{user}'			=> 'COM_RSEVENTSPRO_PLACEHOLDER_USER',
			'{barcodetext}' 	=> 'COM_RSEVENTSPRO_PLACEHOLDER_BARCODE_TEXT'
		);
		
		return array_merge(self::$globals, $denied);
	}
	
	public static function invite() {
		$invite = array(
			'{user}'			=> 'COM_RSEVENTSPRO_PLACEHOLDER_USER',
			'{from}'			=> 'COM_RSEVENTSPRO_PLACEHOLDER_FROM',
			'{fromname}'		=> 'COM_RSEVENTSPRO_PLACEHOLDER_FROM_NAME',
			'{message}'			=> 'COM_RSEVENTSPRO_PLACEHOLDER_INVITE_MESSAGE',
			'{barcodetext}' 	=> 'COM_RSEVENTSPRO_PLACEHOLDER_BARCODE_TEXT'
		);
		
		return array_merge(self::$globals, $invite);
	}
	
	public static function reminder() {
		$reminder = array(
			'{user}'			=> 'COM_RSEVENTSPRO_PLACEHOLDER_USER'
		);
		
		return array_merge(self::$globals, $reminder);
	}
	
	public static function preminder() {
		$preminder = array(
			'{user}'			=> 'COM_RSEVENTSPRO_PLACEHOLDER_USER'
		);
		
		return array_merge(self::$globals, $preminder);
	}
	
	public static function moderation() {
		$moderation = array(
			'{EventApprove}'	=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_APPROVE'
		);
		
		return array_merge(self::$globals, $moderation);
	}
	
	public static function tag_moderation() {
		$tag_moderation = array(
			'{TagsApprove}'		=> 'COM_RSEVENTSPRO_PLACEHOLDER_TAGS_APPROVE'
		);
		
		return array_merge(self::$globals, $tag_moderation);
	}
	
	public static function notify_me() {
		$notify_me = array(
			'{TicketInfo}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_TICKETS_INFO',
			'{TicketsTotal}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_TICKETS_TOTAL',
			'{Discount}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_DISCOUNT',
			'{Tax}' 				=> 'COM_RSEVENTSPRO_PLACEHOLDER_TAX',
			'{LateFee}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_LATE_FEE',
			'{EarlyDiscount}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_EARLY_DISCOUNT',
			'{Gateway}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_GATEWAY',
			'{IP}' 					=> 'COM_RSEVENTSPRO_PLACEHOLDER_IP',
			'{Coupon}' 				=> 'COM_RSEVENTSPRO_PLACEHOLDER_COUPON',
			'{SubscriberUsername}'	=> 'COM_RSEVENTSPRO_PLACEHOLDER_SUBSCRIBER_USERNAME',
			'{SubscriberName}'		=> 'COM_RSEVENTSPRO_PLACEHOLDER_SUBSCRIBER_NAME',
			'{SubscriberEmail}'		=> 'COM_RSEVENTSPRO_PLACEHOLDER_SUBSCRIBER_EMAIL',
			'{SubscriberIP}'		=> 'COM_RSEVENTSPRO_PLACEHOLDER_SUBSCRIBER_IP',
			'{SubscribeDate}'		=> 'COM_RSEVENTSPRO_PLACEHOLDER_SUBSCRIBE_DATE',
			'{PaymentGateway}'		=> 'COM_RSEVENTSPRO_PLACEHOLDER_GATEWAY',
			'{TicketsDiscount}'		=> 'COM_RSEVENTSPRO_PLACEHOLDER_COUPON'
		);
		
		return array_merge(self::$globals, $notify_me);
	}
	
	public static function report() {
		$report = array(
			'{ReportUser}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_REPORT_USER',
			'{ReportIP}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_REPORT_IP',
			'{ReportMessage}' 	=> 'COM_RSEVENTSPRO_PLACEHOLDER_REPORT_MESSAGE'
		);
		
		return array_merge(self::$globals, $report);
	}
	
	public static function approval() {
		$approval = array(
			'{user}'			=> 'COM_RSEVENTSPRO_PLACEHOLDER_USER'
		);
		
		return array_merge(self::$globals, $approval);
	}
	
	public static function rule() {
		$rule = array(
			'{user}'			=> 'COM_RSEVENTSPRO_PLACEHOLDER_USER',
			'{PaymentURL}'		=> 'COM_RSEVENTSPRO_PLACEHOLDER_PAYMENT_URL',
			'{Status}'			=> 'COM_RSEVENTSPRO_PLACEHOLDER_SUBSCRIBER_STATUS'
		);
		
		return array_merge(self::$globals, $rule);
	}
	
	public static function pdf() {
		$pdf = array(
			'{TicketInfo}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_TICKETS_INFO',
			'{TicketsTotal}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_TICKETS_TOTAL',
			'{Discount}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_DISCOUNT',
			'{Tax}' 				=> 'COM_RSEVENTSPRO_PLACEHOLDER_TAX',
			'{LateFee}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_LATE_FEE',
			'{EarlyDiscount}'	 	=> 'COM_RSEVENTSPRO_PLACEHOLDER_EARLY_DISCOUNT',
			'{Gateway}' 			=> 'COM_RSEVENTSPRO_PLACEHOLDER_GATEWAY',
			'{IP}' 					=> 'COM_RSEVENTSPRO_PLACEHOLDER_IP',
			'{Coupon}' 				=> 'COM_RSEVENTSPRO_PLACEHOLDER_COUPON',
			'{barcode}'		 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_BARCODE',
			'{barcodetext}' 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_BARCODE_TEXT',
			'{useremail}'	 		=> 'COM_RSEVENTSPRO_PLACEHOLDER_USER_EMAIL',
			'{EventIconSmallPdf}'	=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_SMALL_ICON',
			'{EventIconBigPdf}'		=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_BIG_ICON',
			'{EventIconPdf}'		=> 'COM_RSEVENTSPRO_PLACEHOLDER_EVENT_ICON'
		);
		
		unset(self::$globals['{EventIconSmall}']);
		unset(self::$globals['{EventIconBig}']);
		
		return array_merge(self::$globals, $pdf);
	}
	
	public static function payment() {
		return self::$globals;
	}
}
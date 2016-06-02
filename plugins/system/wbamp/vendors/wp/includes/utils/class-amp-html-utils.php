<?php
/**
 * Plugin Name: AMP
 * Description: Add AMP support to your WordPress site.
 * Plugin URI: https://github.com/automattic/amp-wp
 * Author: Automattic
 * Author URI: https://automattic.com
 * Version: 0.3
 * Text Domain: amp
 * Domain Path: /languages/
 * License: GPLv2 or later
 */

defined('_JEXEC') or die();

class AMP_HTML_Utils {
	public static function build_tag( $tag_name, $attributes = array(), $content = '' ) {
		$attr_string = self::build_attributes_string( $attributes );
		return sprintf( '<%1$s %2$s>%3$s</%1$s>', sanitize_key( $tag_name ), $attr_string, $content );
	}

	public static function build_attributes_string( $attributes ) {
		$string = array();
		foreach ( $attributes as $name => $value ) {
			if ( '' === $value ) {
				$string[] = sprintf( '%s', sanitize_key( $name ) );
			} else {
				$string[] = sprintf( '%s="%s"', sanitize_key( $name ), esc_attr( $value ) );
			}
		}
		return implode( ' ', $string );
	}
}

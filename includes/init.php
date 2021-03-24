<?php

// Autoload Custom Fields
$custom_fields = glob( __DIR__ . '/custom-fields/*.php', GLOB_NOSORT );
if ( ! empty( $custom_fields ) ) {
	foreach ( $custom_fields as $custom_field ) {
		include_once( $custom_field );
	}
}

// Autoload Classes
$classes = glob( __DIR__ . '/classes/*/*.php', GLOB_NOSORT );
if ( ! empty( $classes ) ) {
	foreach ( $classes as $class ) {
		include_once( $class );
	}
}
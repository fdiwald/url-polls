<?php
namespace Url_Polls;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function sanitize_base64($input)
{
	return preg_replace( '/[^A-Za-z0-9\+\/=]/', '', $input);
}

function urlencode_base64($input) {
	return strtr($input, '+/=', '._-');
}

function urldecode_base64($input) {
	return strtr($input, '._-', '+/=');
}
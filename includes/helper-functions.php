<?php
namespace Url_Polls;

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
<?php
/**
 * MY_GetQueryStringValue
 *
 * DESCRIPTION
 *
 * This snippet returns a value from a query string
 *
 * USAGE:
 *
 * [[!MY_GetQueryStringValue? &field=``]]
 */
 
return isset($_GET[$field]) ? $_GET[$field] : '';
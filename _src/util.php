<?php

// Escape/handler any bad characters passed through request variables.
function _escape($values)
{
    if(!is_array($values))
    {
        /* Quote if not integer */
        if ( !is_numeric($values) || $values{0} == '0' )
        {
            $values = stripslashes($values);
            $values = mysql_real_escape_string($values);
        }
    }
    return $values;
}

// Processes request vars into local vars dynamically.
function _getvars()
{
    $vars = array();
    //load in GET variables
    foreach($_GET as $n => $v)
    {
        $vars['GET'][$n] = _escape($v);
    }
    //load in POST variables
    foreach($_POST as $n => $v)
    {
        $vars['POST'][$n] = _escape($v);
    }
    return $vars;
}

//Redirect to a page on the current site
function _localRedirect($pageUrl) 
{
    $host = $_SERVER['HTTP_HOST'];
    header("Location: http://".$host.$pageUrl);
    die();
}

// Send an email
function _sendEmail($params) {
    if (!is_array($params)) {
        return false;
    }
    if (!isset($params["to"]) || !isset($params["subject"]) || !isset($params["message"])) {
        return false;
    }
    $to        = $params["to"];
    $subject   = $params["subject"];
    $message   = $params["message"];
    $headers   = array();
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-type: text/plain;charset=utf-8";
    if (isset($params["from"])) {
        $headers[] = "From: " . $params["from"];
        $headers[] = "Reply-To: " . $params["from"];
    } else {
        $headers[] = "From: Explore TNQ <no-reply@exploretnq.com.au>";
        $headers[] = "Reply-To: Explore TNQ <no-reply@exploretnq.com.au>";
    }
    if (isset($params["cc"])) {
        $headers[] = "Cc: " . $params["cc"];
    }   
    if (isset($params["bcc"])) {
        $headers[] = "Bcc: " . $params["bcc"];
    }
    $headers[] = "X-Mailer: PHP/" . phpversion();
    return mail($to, $subject, $message, implode("\r\n", $headers));
}


/****************************************************************************************
 *
 * Validation functions
 *
 *****************************************************************************************/
/**
 * Checks if the given values are a correct gregorian date
 */
function is_date($year, $month, $day) {
    return checkdate($month, $day, $year);
}

/**
 * Checks if $string is a correct e-mail address
 */
function is_email($string) {
    static $tld_array = array(
        'AC', 'AD', 'AE', 'AF', 'AG', 'AI', 'AL', 'AM', 'AN', 'AO', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AW', 'AZ',
        'BA', 'BB', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BM', 'BN', 'BO', 'BR', 'BS', 'BT', 'BV', 'BW', 'BY', 'BZ',
        'CA', 'CC', 'CD', 'CF', 'CG', 'CH', 'CI', 'CK', 'CL', 'CM', 'CN', 'CO', 'CR', 'CU', 'CV', 'CX', 'CY', 'CZ',
        'DE', 'DJ', 'DK', 'DM', 'DO', 'DZ',
        'EC', 'EE', 'EG', 'EH', 'EI', 'ER', 'ES', 'ET', 'EU',
        'FI', 'FJ', 'FK', 'FM', 'FO', 'FR',
        'GA', 'GB', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GL', 'GM', 'GN', 'GP', 'GQ', 'GR', 'GS', 'GT', 'GU', 'GW', 'GY',
        'HK', 'HM', 'HN', 'HR', 'HT', 'HU',
        'ID', 'IE', 'IL', 'IM', 'IN', 'IO', 'IQ', 'IR', 'IS', 'IT',
        'JE', 'JM', 'JO', 'JP',
        'KE', 'KG', 'KH', 'KI', 'KM', 'KN', 'KP', 'KR', 'KW', 'KY', 'KZ',
        'LA', 'LB', 'LC', 'LI', 'LK', 'LR', 'LS', 'LT', 'LU', 'LV', 'LY',
        'MA', 'MC', 'MD', 'MG', 'MH', 'MK', 'ML', 'MM', 'MN', 'MO', 'MP', 'MQ', 'MR', 'MS', 'MT', 'MU', 'MV', 'MW', 'MX', 'MY', 'MZ',
        'NA', 'NC', 'NE', 'NF', 'NG', 'NI', 'NL', 'NO', 'NP', 'NR', 'NU', 'NZ',
        'OM',
        'PA', 'PE', 'PF', 'PG', 'PH', 'PK', 'PL', 'PM', 'PN', 'PR', 'PS', 'PT', 'PW', 'PY',
        'QA',
        'RE', 'RO', 'RU', 'RW',
        'SA', 'SB', 'SC', 'SD', 'SE', 'SG', 'SH', 'SI', 'SJ', 'SK', 'SL', 'SM', 'SN', 'SO', 'SR', 'ST', 'SV', 'SY', 'SZ',
        'TC', 'TD', 'TF', 'TH', 'TJ', 'TG', 'TK', 'TM', 'TN', 'TO', 'TP', 'TR', 'TT', 'TV', 'TW', 'TZ',
        'UA', 'UG', 'UK', 'UM', 'US', 'UY', 'UZ',
        'VA', 'VC', 'VE', 'VG', 'VI', 'VN', 'VU',
        'WF', 'WS',
        'YE', 'YT', 'YU',
        'ZA', 'ZM', 'ZW',
        'AERO', 'ARPA', 'ARTS', 'BIZ', 'COM', 'COOP', 'EDU', 'FIRM', 'GOV', 'INFO', 'INT', 'MIL', 'MUSEUM', 'NAME', 'NET', 'NOM', 'ORG', 'PRO', 'REC', 'STORE', 'WEB'
        );
    $tld_string = join("|", $tld_array);
    $reg_exp = "#^[0-9a-z]+[_\.0-9a-z\-]*\@(.+\.){1,7}(" . $tld_string . "){1}$#si";
    return preg_match($reg_exp, $string);
}

/**
 * Checks if $string is filled or not
 */
function is_filled($var) {
    return trim($var) != '';
}

/**
 * Checks if the given value is a number > 0
 */
function is_id($var) {
    if (!is_int($var) && !is_string($var) && !is_float($var)) {
        return false;
    }
    if (!preg_match("#^([1-9]{1})([0-9]*)$#", (int)$var)) {
        return false;
    }
    return true;
}

/**
 * Checks if $string contains no HTML
 */
function is_no_html($string) {
    return !preg_match("#^([^<>]+)$#", $string);
}

/**
 * Checks if $var is a string that contains only numerical characters
 */
function is_num($var, $min = false, $max = false) {
    if (is_int($var)) {
        return true;
    }
    $number = '+';
    if ($min) {
        $number = '{' . (int)$min . ',}';
    }
    if ($max) {
        $number = '{' . (int)$min . ',' . (int)$max . '}';
    }
    if (!preg_match("#^([0-9]$number)$#", $var)) {
        return false;
    }
    return true;
}

/**
 * Checks if $string contains only non-numerical characters
 */
function is_text($var) {
    return !preg_match("#^([^0-9]+)$#", $var);
}

/**
 * Checks if $url is a valid URL
 */
function is_url($url) {
    return !preg_match('#^([a-z]+?)://([^ \n\r]+)$#', $url);
}


/**
 * Makes a string URL-usable
 *
 * Converts space to '-', rewrites some special chars (e.g. 'ä' to 'ae') and returns a
 * string that can be used for URLs.
 */
function sanitize($string) {
    static $chars;
    if (!isset($chars)) {
        $chars = get_special_chars_matrix();
    }
    $string = strtr($string, $chars);
    $string = strtolower($string);
    $string = preg_replace("#&.+?;#", "", $string);
    $string = preg_replace("#[^a-z0-9 _-]#", "", $string);
    $string = preg_replace("#\s+#", "-", $string);
    $string = preg_replace("#-+#", "-", $string);
    $string = trim($string, '-');
    return $string;
}

/**
 * Like htmlspecialchars except don't double-encode HTML entities
 */
function escape_special_chars($text) {
    $text = preg_replace('#&([^\#])(?![a-z12]{1,8};)#', '&amp;$1', $text);
    $text = str_replace('<', '&lt;', $text);
    $text = str_replace('>', '&gt;', $text);
    $text = str_replace('"', '&quot;', $text);
    $text = str_replace("'", '&apos;', $text);
    return $text;
}


/**
 * Create a human-readable date
 *
 * Converts a timestamp into a preferred format and considers the setting of the current page
 * and/or member for that.
 */
function format_date($date_string, $format = "%d.%m.%Y", $mark_today = false) {
    $timestamp = make_timestamp($date_string);
    if ($mark_today && gmstrftime("%Y%m%d") == gmstrftime("%Y%m%d", $timestamp)) {
        return '<strong>Today</strong>';
    }
    if ($format == 'iso') {
        return gmstrftime("%Y-%m-%dT%H:%M:%SZ", $timestamp);
    }
    return gmstrftime($format, $timestamp);
}


?>
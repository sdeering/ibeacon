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

?>
<?php

/**
* Function to sanitize user input
* to prevent sql injection.
*/

function escape($string) {
    return (htmlentities($string, ENT_QUOTES, 'UTF-8'));
}
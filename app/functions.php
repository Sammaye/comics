<?php

function in_preg_array($needle, array $patterns)
{
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $needle)) {
            return true;
        }
    }

    return false;
}

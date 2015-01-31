<?php

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

class Update
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    // No updates created yet for emon ecdb
}

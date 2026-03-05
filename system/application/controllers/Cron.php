<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends Controller
{
    function Cron()
    {
        parent::Controller();
    }

    function ping()
    {
        header('Content-Type: text/plain; charset=utf-8');
        echo "cron ok";
    }
}
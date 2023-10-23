#!/usr/bin/php
<?php
/**
 * Installs the application as a system daemon.
 */

require_once "System/Daemon.php";
require_once "config.php";

System_Daemon::setOptions($options);
System_Daemon::writeAutoRun();

// For Debian based systems
//exec("update-rc.d $appname defaults");

// For Red Hat based systems
exec("/sbin/chkconfig --add $appname");
exec("/sbin/chkconfig $appname on");
?>
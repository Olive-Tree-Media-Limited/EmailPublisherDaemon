#!/usr/bin/php
<?php
/**
 * Uninstalls the application as a system daemon.
 */

require_once "config.php";

// For Debian based systems
//unlink("/etc/init.d/$appname");
//exec("update-rc.d $appname remove");

// For Red Hat based systems
exec("/sbin/chkconfig --del $appname");
unlink("/etc/init.d/$appname");
?>

This program runs as a UNIX daemon.

NOTE: Uses the pear System Daemon library which must be installed on your
      system.

      pear install system_daemon


INSTALLATION

1. Copy the phpdaemon folder inside the src folder to the location where you
   want the application to run.

    e.g. /usr/local/lib/phpdaemon

2. Make sure the files phpdaemon.php, install.php, uninstall.php are executable.

    chmod a+x requests.php install.php uninstall.php manage_email_requests.sh

3. Run the file install.php as root user. This will create the
   /etc/init.d/phpdaemon start-stop script and also set up the system startup
   and shutdown commands.

    e.g. php install.php or ./install.php if executable

4. Set up the database and log in permissions.

5. Make sure you have updated all the correct values in the file "config.php"

UNINSTALLATION

1. Just run the file uninstall.php

    e.g. php uninstall.php or ./uninstall.php if executable


RUNNING THE PROGRAM

1. Use the start-stop-daemon script

    e.g /etc/init.d/phpdaemon {start | stop | restart}

    chmod a+x batchQueueing.php install.php uninstall.php

    ps aux | grep php
    /etc/init.d/batchpublisher start

    /etc/init.d/batchpublisher stop

    tail -f /var/log/applications/batchPublisher.log

#!/bin/bash

# Find the PID of the running process
PID=$(pgrep -f '/usr/bin/php /var/www/html/phpDaemons/CustomEmailPublisherDaemon/requests.php')

if [ -z "$PID" ]; then
    echo "Process is not running."

    read -p "Do you want to start it? (y/n): " choice
    if [ "$choice" == "y" ]; then
        sudo /etc/init.d/custom_email_requests_app start
        tail -f /var/log/applications/customEmailRequestsApp.log
    else
        echo "Ignoring."
    fi

    exit 1
fi

echo "Stopping the process with PID: $PID"
sudo kill -9 "$PID"
echo "Process stopped successfully."


read -p "Do you want to start it again? (y/n): " choice
if [ "$choice" == "y" ]; then
    sudo /etc/init.d/custom_email_requests_app start
    tail -f /var/log/applications/customEmailRequestsApp.log
else
    echo "Ignoring the start request."
fi




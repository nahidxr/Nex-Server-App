script_name="nex-server-monitor.sh" && (crontab -l 2>/dev/null | grep -v "$script_name";) | crontab - && echo "The toffee monitoring script from 66uptime-demo (https://66uptime.com/demo/) has been uninstalled."
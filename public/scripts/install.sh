script_name="nex-server-monitor.sh" 
&& wget -O "$PWD/$script_name" "http://127.0.0.1/server-monitor-code/{{server_id}}/{{api_key}}" 
&& chmod +x "$PWD/$script_name" 
&& (crontab -l 2>/dev/null | grep -v "$script_name"; echo "*/{{check_interval}} * * * * $PWD/$script_name") | crontab - 
&& echo "The {{server_name}} monitoring script has been installed."
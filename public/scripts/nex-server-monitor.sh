#!/bin/bash

server_id={SERVER_ID}
api_key='{API_KEY}'

# Calculate CPU usage
cpu_usage=$(top -bn2 | grep "Cpu(s)" | tail -n 1 | awk '{printf "%.2f\n", $2 + $4}')

# Get the CPU load averages
uptime_output=$(uptime)

# Use awk to parse the load averages
cpu_load_1=$(echo $uptime_output | awk -F'load average: ' '{ print $2 }' | awk -F', ' '{ print $1 }')
cpu_load_5=$(echo $uptime_output | awk -F'load average: ' '{ print $2 }' | awk -F', ' '{ print $2 }')
cpu_load_15=$(echo $uptime_output | awk -F'load average: ' '{ print $2 }' | awk -F', ' '{ print $3 }')

# Collect RAM statistics
ram_usage=$(free | awk '/Mem:/ {printf "%.2f", $3/$2 * 100.0}')
ram_used=$(free -m | awk '/^Mem:/{print $3}')
ram_total=$(free -m | awk '/^Mem:/{print $2}')

# Collect disk usage
disk_usage=$(df | awk '$NF=="/"{printf "%.2f\n", $3/$2 * 100}')
disk_used=$(df -m | awk '$NF=="/"{print $3}')
disk_total=$(df -m | awk '$NF=="/"{print $2}')

# Extract OS Name and Version from /etc/os-release
os_name=$(grep '^NAME=' /etc/os-release | cut -d '"' -f 2)
os_version=$(grep '^VERSION=' /etc/os-release | cut -d '"' -f 2)

# Collect Kernel data
kernel_name=$(uname -s)
kernel_release=$(uname -r)
kernel_version=$(uname -v)

# Collect CPU Architecture
cpu_architecture=$(uname -m)

# Collect CPU data 
cpu_model=$(cat /proc/cpuinfo | grep 'model name' | awk -F\: '{print $2}' | uniq)
cpu_cores=$(cat /proc/cpuinfo | grep processor | wc -l)
cpu_frequency=$(grep 'cpu MHz' /proc/cpuinfo | awk '{print $4}' | head -n 1)

# Uptime
uptime=$(awk '{print $1}' /proc/uptime)

# Function to read current network stats
read_network_stats() {
    local stats=()
    while read line; do
        # Extract receive and transmit bytes
        local receive_bytes=$(echo $line | awk '{print $2}')
        local transmit_bytes=$(echo $line | awk '{print $10}')
        stats+=($receive_bytes $transmit_bytes)
    done < <(tail -n +3 /proc/net/dev)
    echo "${stats[@]}"
}

# Take the first measurement
initial_stats=($(read_network_stats))

# Define the time interval (in seconds) for measuring
interval=3
sleep $interval

# Take the second measurement
final_stats=($(read_network_stats))

# Initialize variables to store the sum of download and upload rates
network_download=0
network_upload=0

# Calculate the download and upload rate for each interface
for ((i=0; i<${#initial_stats[@]}; i+=2)); do
    # Download rate
    let download_rate=(${final_stats[$i]}-${initial_stats[$i]})/$interval
    network_download=$((network_download + download_rate))

    # Upload rate
    let upload_rate=(${final_stats[$i+1]}-${initial_stats[$i+1]})/$interval
    network_upload=$((network_upload + upload_rate))
done

# Initialize variables for total download and upload
network_total_download=0
network_total_upload=0

# Read network interfaces and statistics from /proc/net/dev
{
    read
    read
    while read line; do
        # Extract interface name and bytes
        RECEIVE_BYTES=$(echo $line | awk '{print $2}')
        TRANSMIT_BYTES=$(echo $line | awk '{print $10}')

        # Add to the total download and upload
        network_total_download=$((network_total_download + RECEIVE_BYTES))
        network_total_upload=$((network_total_upload + TRANSMIT_BYTES))
    done
} < /proc/net/dev

# Create JSON payload
json="{\"network_download\": \"$network_download\", \"network_upload\": \"$network_upload\", \"network_total_download\": \"$network_total_download\", \"network_total_upload\": \"$network_total_upload\",  \"cpu_model\": \"$cpu_model\", \"cpu_cores\": \"$cpu_cores\", \"cpu_frequency\": \"$cpu_frequency\", \"uptime\": \"$uptime\", \"os_name\": \"$os_name\", \"os_version\": \"$os_version\", \"kernel_name\": \"$kernel_name\", \"kernel_release\": \"$kernel_release\", \"kernel_version\": \"$kernel_version\", \"cpu_architecture\": \"$cpu_architecture\", \"cpu_usage\": \"$cpu_usage\", \"ram_usage\": \"$ram_usage\", \"ram_total\": \"$ram_total\", \"ram_used\": \"$ram_used\", \"disk_usage\": \"$disk_usage\", \"disk_total\": \"$disk_total\", \"disk_used\": \"$disk_used\", \"cpu_load_1\": \"$cpu_load_1\", \"cpu_load_5\": \"$cpu_load_5\", \"cpu_load_15\": \"$cpu_load_15\"}"

# Make HTTP POST request to upload data
url="{BASE_URL}/server-monitor-track/$server_id/$api_key" 
curl -X POST -H "Content-Type: application/json" -d "$json" "$url" &


#!/bin/bash

# Database connection details
DB_HOST=103.204.82.150
DB_PORT=3306
DB_DATABASE=nex_ott_monitoring
DB_USERNAME=nahid
DB_PASSWORD=N@h!D@@Db#1

# Path to your GCP service account key JSON file
gcp_key_file="/mnt/t-sports-bucket.json"  # Replace with your actual path
export GOOGLE_APPLICATION_CREDENTIALS="$gcp_key_file"

# Define the GCS bucket directory
bucket_name="content-process-test/uploads"  # GCS bucket name
local_video_dir="/video/"
log_file="/mnt/vod/ffmpeg-status.log"

# Ensure local directory exists
if [ ! -d "$local_video_dir" ]; then
  echo "Local directory $local_video_dir does not exist. Creating it now..."
  mkdir -p "$local_video_dir"
fi


# Function to download the next available file from the GCS bucket
download_next_file() {
  echo "Checking for the next available file to download from the GCS bucket..."

  # Get a list of all files in the GCS bucket
  files=$(gsutil ls "gs://$bucket_name/*.*")

  # Loop through each file in the bucket
  for file in $files; do
    # Extract the base name of the file to be downloaded (without the extension)
    base_name=$(basename "$file" .mp4)

    # Check if the file already exists in the local directory
    if [ -f "$local_video_dir/$base_name.mp4" ]; then
      echo "File already exists in local directory: $base_name.mp4"
      echo "$(date '+%Y-%m-%d %H:%M:%S') - File already exists in local directory: $base_name.mp4" >> "$log_file"
      continue  # Skip to the next file
    fi

    # Check the database if this file has already been encoded (encoder_status=1)
    query="SELECT encoder_status FROM contents WHERE file_id='$base_name' LIMIT 1;"
    encoder_status=$(mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE -e "$query" -s --skip-column-names)

    if [ "$encoder_status" == "1" ]; then
      echo "File has already been encoded: $base_name.mp4"
      echo "$(date '+%Y-%m-%d %H:%M:%S') - File already encoded (encoder_status=1): $base_name.mp4" >> "$log_file"
      continue  # Skip to the next file
    fi

    # Attempt to download the file
    echo "Downloading file: $file"
    gsutil cp "$file" "$local_video_dir"

    if [ $? -eq 0 ]; then
      # If the download was successful
      echo "File download complete: $base_name.mp4"
      echo "$(date '+%Y-%m-%d %H:%M:%S') - Downloaded $base_name.mp4 successfully" >> "$log_file"
      return 0  # Exit the function after successfully downloading one file
    else
      # Error during download
      echo "Error downloading file: $base_name.mp4"
      echo "$(date '+%Y-%m-%d %H:%M:%S') - Error downloading $base_name.mp4" >> "$log_file"
      continue  # Skip to the next file in case of an error
    fi
  done

  # No files were downloaded
  echo "No files found for download in the bucket or all files already exist/encoded."
  echo "$(date '+%Y-%m-%d %H:%M:%S') - No files found for download or all files already encoded" >> "$log_file"
  return 1
}


# Loop to continuously download and process files one at a time
while true; do
  if download_next_file; then
    for file in /video/*.*; do
      echo "Processing file: $file (First come, first served)"
      file_base_name=$(basename "$file" .mp4)
      
      mkdir -p /mnt/desh/$file_base_name/
      # MySQL query to retrieve data from the content table based on file_id (file_base_name)
      query="SELECT profiles FROM contents WHERE file_id='$file_base_name' AND encoder_status=0 LIMIT 1;"
      result=$(mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE -e "$query" -s --skip-column-names)
       
      # Check if the query was successful and process the result
      if [ -n "$result" ]; then
        echo "Raw query result: $result"
        echo "Profiles data found for file_base_name: $file_base_name"
        echo "$result" >> "$log_file"  # Log the data

        # Parse the JSON profiles data
        profiles=$(echo "$result" | jq -c '.[]' 2>> "$log_file")

        # Loop over each profile in the JSON array
        for profile in $profiles; do
          # Extract the profile parameters
          width=$(echo "$profile" | jq -r '.width')
          height=$(echo "$profile" | jq -r '.height')
          video_bitrate=$(echo "$profile" | jq -r '.video_bitrate')
         

          # Define the destination directory based on the height
          destination="/vod/${file_base_name}_${height}"
          mkdir -p "$destination"

          # Log the start of processing
          echo "$(date '+%Y-%m-%d %H:%M:%S') - Processing $file at ${height}p" >> "$log_file"

          # Run FFmpeg with dynamic values for the scale, audio, and video bitrate
          if ffmpeg-bar -i "$file" -f hls -vcodec libx264 -r 25/1 -vf "scale=${width}:${height},setsar=1,setdar=16/9" \
            -acodec aac -ar 44100 -bufsize ${video_bitrate}k -maxrate ${video_bitrate}k -ac 2 -ab 128k -threads 96 -b:v ${video_bitrate}k \
            -start_number 0 -g 300 -hls_time 5 -hls_list_size 0 "$destination/${file_base_name}_index.m3u8" 2>> "$log_file"; then
            echo "$(date '+%Y-%m-%d %H:%M:%S') - $file transcoded successfully at ${height}p" >> "$log_file"
			
          else
            echo "$(date '+%Y-%m-%d %H:%M:%S') - Error transcoding $file at ${height}p" >> "$log_file"
          fi
        done
        
             # Create the master playlist
            master_playlist="/mnt/desh/$file_base_name/${file_base_name}_playlist.m3u8"
            echo "#EXTM3U" > "$master_playlist"

            # Loop over each profile in the JSON array and add them to the playlist
            for profile in $profiles; do
                # Extract the profile parameters
                width=$(echo "$profile" | jq -r '.width')
				height=$(echo "$profile" | jq -r '.height')
				video_bitrate=$(echo "$profile" | jq -r '.video_bitrate')

                # Log the process
                echo "$(date '+%Y-%m-%d %H:%M:%S') - Adding resolution ${width}x${height} with bitrate ${video_bitrate}k to the playlist" >> "$log_file"

                # Add each stream's information to the master playlist
                echo "#EXT-X-STREAM-INF:BANDWIDTH=$((video_bitrate * 1000)),RESOLUTION=${width}x${height}" >> "$master_playlist"
                echo "${file_base_name}_${height}/${file_base_name}_index.m3u8" >> "$master_playlist"
            done
        
        # Update the encode_status in the database
        update_query="UPDATE contents SET encoder_status=1 WHERE file_id='$file_base_name';"
        mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE -e "$update_query"
  
        # Rsync the log file to the remote server
        rsync -avz -e "ssh -p 22" /mnt/vod/ffmpeg-status.log root@178.237.38.181:/mnt/vod/
      else
        echo "No data found in the database for file_base_name: $file_base_name"
        echo "$(date '+%Y-%m-%d %H:%M:%S') - No data found for $file_base_name in the database" >> "$log_file"
        continue  # Skip further processing for this file
      fi
      
      
    
      mv /vod/* /mnt/desh/$file_base_name/

      # Move all files and directories inside the local directory to the GCS bucket
      bucket_name_hls="content-process-test/hls"
      local_dir_hls="/mnt/desh/"
      gsutil -m mv "$local_dir_hls"* "gs://$bucket_name_hls/"

      # Cleanup
      rm -rf /mnt/desh/*
      rm -rf /video/*
    done
  fi
  sleep 10
done

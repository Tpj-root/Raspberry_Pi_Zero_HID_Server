#!/bin/bash

# Get the absolute path of this script.
# readlink -f resolves the full real path, even if called through ./ or bash script.sh
SCRIPT_PATH="$(readlink -f "$0")"

# Extract the directory where this script is located.
# dirname removes the filename from the path.
SCRIPT_DIR="$(dirname "$SCRIPT_PATH")"

# If not running as root (EUID != 0), restart this script with sudo.
# exec replaces the current shell, so no extra process remains.
# "$SCRIPT_PATH" ensures sudo calls the full absolute path.
if [ "$EUID" -ne 0 ]; then
    echo "Restarting with root permissions..."
    exec sudo bash "$SCRIPT_PATH" "$@"
fi

# The folder that contains your website files.
# It must exist inside the same directory as this script.
#SRC_DIR="$SCRIPT_DIR/server"
# php
SRC_DIR="$SCRIPT_DIR/server_php"


# The Apache web server's root folder where files must be copied.
DEST_DIR="/var/www/html"

# Remove ALL existing files inside /var/www/html.
# ${DEST_DIR:?} prevents accidents — script stops if DEST_DIR is empty.
echo "Removing old files..."
rm -rf "${DEST_DIR:?}/"*

# Copy all files from your local server folder to the Apache directory.
# -r ensures folders are copied recursively.
echo "Copying new files..."
cp -r "$SRC_DIR"/* "$DEST_DIR"/

# Final message.
echo "Update complete!"

#!/bin/bash

# Source folder = "server" inside current script directory
SCRIPT_DIR="$(dirname "$0")"
SRC_DIR="$SCRIPT_DIR/server"

DEST_DIR="/var/www/html"

echo "Removing old files..."
sudo rm -rf ${DEST_DIR}/*

echo "Copying new files..."
sudo cp -r ${SRC_DIR}/* ${DEST_DIR}/

echo "Update complete!"

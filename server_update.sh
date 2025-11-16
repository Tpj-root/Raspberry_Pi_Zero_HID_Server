#!/bin/bash

# Check root
if [ "$EUID" -ne 0 ]; then
    echo "Restarting with root permissions..."
    exec sudo "$0" "$@"
fi

SCRIPT_DIR="$(dirname "$0")"
SRC_DIR="$SCRIPT_DIR/server"
DEST_DIR="/var/www/html"

echo "Removing old files..."
rm -rf "${DEST_DIR:?}/"*

echo "Copying new files..."
cp -r "$SRC_DIR"/* "$DEST_DIR"/

echo "Update complete!"

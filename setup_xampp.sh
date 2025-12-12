#!/bin/bash
# FINAL REPAIR SCRIPT
# This script fixes BOTH the Site (403 Error) and phpMyAdmin.
# Strategy: 
# 1. Revert Apache User to 'daemon' (Fixes phpMyAdmin)
# 2. COPY files to htdocs instead of Symlink (Fixes Site Permissions)

PROJECT_PATH="$(pwd)"
XAMPP_HTDOCS="/Applications/XAMPP/xamppfiles/htdocs"
LINK_NAME="EWU-LostFound"
CONFIG_FILE="/Applications/XAMPP/xamppfiles/etc/httpd.conf"

echo "🔧 Starting Final Repair..."

# 1. Revert Apache User to 'daemon' (Fixes phpMyAdmin)
echo "👤 Restoring Apache User to 'daemon'..."
if grep -q "User daemon" "$CONFIG_FILE"; then
    echo "   User is already daemon."
else
    # Replace any 'User <name>' with 'User daemon'
    sudo sed -i.bak 's/^User .*/User daemon/' "$CONFIG_FILE"
    echo "✅ Restored 'User daemon' in httpd.conf"
fi

# 2. Switch to COPY method (Fixes Site 403)
echo "📂 Setting up Project in htdocs..."
DEST_DIR="$XAMPP_HTDOCS/$LINK_NAME"

# Remove existing symlink or folder
if [ -e "$DEST_DIR" ]; then
    echo "   Removing old version..."
    sudo rm -rf "$DEST_DIR"
fi

# Create directory
echo "   Copying files (this may take a second)..."
sudo mkdir -p "$DEST_DIR"
sudo cp -R "$PROJECT_PATH/" "$DEST_DIR/"

# 3. Fix Permissions
echo "🔓 Fixing Permissions..."
sudo chown -R daemon:daemon "$DEST_DIR"
sudo chmod -R 755 "$DEST_DIR"

echo "✅ Project copied successfully."

# 4. Restart Apache (Automated)
echo "🔄 Restarting Apache..."
sudo /Applications/XAMPP/xamppfiles/bin/apachectl restart

# 5. Success Message
echo ""
echo "🎉 REPAIR COMPLETE!"
echo "------------------------------------------------"
echo "✅ phpMyAdmin restored."
echo "✅ Site repaired."
echo "✅ Apache restarted."
echo ""
echo "🚀 SITREP:"
echo "To avoid running this script every time you save a file,"
echo "you should open the XAMPP folder in VS Code:"
echo ""
echo "   /Applications/XAMPP/xamppfiles/htdocs/EWU-LostFound"
echo ""
echo "------------------------------------------------"

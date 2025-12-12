#!/bin/bash
# Setup script for XAMPP on macOS

# 1. Define Paths
PROJECT_PATH="$(pwd)"
XAMPP_HTDOCS="/Applications/XAMPP/xamppfiles/htdocs"
LINK_NAME="EWU-LostFound"
CONFIG_FILE="/Applications/XAMPP/xamppfiles/etc/httpd.conf"

echo "🔧 Starting XAMPP Setup..."

# 2. Fix Local Permissions
echo "🔓 Fixing git/project permissions..."
chmod -R 755 "$PROJECT_PATH"

# 3. Create Symlink
echo "🔗 Checking symlink..."
if [ ! -d "$XAMPP_HTDOCS" ]; then
    echo "❌ XAMPP htdocs directory not found!"
    echo "   Please check if XAMPP is installed at /Applications/XAMPP"
    exit 1
fi

if [ -L "$XAMPP_HTDOCS/$LINK_NAME" ]; then
    echo "   Symlink exists. Refreshing..."
    rm "$XAMPP_HTDOCS/$LINK_NAME"
fi

if ln -s "$PROJECT_PATH" "$XAMPP_HTDOCS/$LINK_NAME"; then
    echo "✅ Symlink created successfully!"
else
    echo "⚠️  Failed to create symlink. Asking for sudo..."
    sudo ln -s "$PROJECT_PATH" "$XAMPP_HTDOCS/$LINK_NAME"
fi

# 4. Critical Apache Configuration Instructions
echo ""
echo "=================================================================="
echo "🚨 CRITICAL FIX FOR 403 FORBIDDEN ERROR 🚨"
echo "=================================================================="
echo "Because your project is on the Desktop, Apache cannot read it by default."
echo "You MUST perform the following steps manually:"
echo ""
echo "1. Open the Apache Config file:"
echo "   $CONFIG_FILE"
echo ""
echo "2. Add this allowed directory block at the very end of the file:"
echo ""
echo "   <Directory \"$PROJECT_PATH\">"
echo "       Options Indexes FollowSymLinks Includes ExecCGI"
echo "       AllowOverride All"
echo "       Require all granted"
echo "   </Directory>"
echo ""
echo "3. (Optional but Recommended) Find 'User daemon' (approx line 170)"
echo "   and change it to your username:"
echo "   User $(whoami)"
echo ""
echo "4. SAVE the file and RESTART Apache in XAMPP Manager."
echo "=================================================================="

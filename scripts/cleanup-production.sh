#!/bin/bash
# Production Cleanup Script - Remove source maps and unnecessary files
# Run on server: bash scripts/cleanup-production.sh

echo "=== Production Cleanup Started ==="

# Remove source maps
echo "Removing source maps..."
find public -name "*.map" -type f -delete
echo "✓ Source maps removed"

# Remove unused Animate.css plugin directory (if not used elsewhere)
if [ -d "public/web/plugins/animate-css" ]; then
    echo "Animate.css directory found but not removed (may be needed for other projects)"
fi

# Verify removal
echo -e "\n=== Verification ==="
echo "Remaining .map files:"
find public -name "*.map" -type f
if [ $? -eq 0 ] && [ -z "$(find public -name "*.map" -type f)" ]; then
    echo "✓ No .map files found - Cleanup successful!"
fi

echo "=== Production Cleanup Completed ==="

#!/bin/bash
# Image Optimization Script
# Converts JPG/PNG to WebP and optimizes sizes
# Usage: bash scripts/optimize-images.sh

set -e

IMAGE_DIR="public/web/images"
BACKUP_DIR="public/web/images/.backup-$(date +%Y%m%d-%H%M%S)"
TOTAL_BEFORE=0
TOTAL_AFTER=0

echo "=== Image Optimization Started ==="
echo "Creating backup directory: $BACKUP_DIR"
mkdir -p "$BACKUP_DIR"

# Function to format bytes
format_bytes() {
    local bytes=$1
    if [ $bytes -lt 1024 ]; then
        echo "${bytes}B"
    elif [ $bytes -lt $((1024*1024)) ]; then
        echo "$((bytes/1024))KB"
    else
        echo "$((bytes/(1024*1024)))MB"
    fi
}

# Function to optimize JPG
optimize_jpg() {
    local file=$1
    local size_before=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file")
    
    # Backup
    cp "$file" "$BACKUP_DIR/"
    
    # Optimize with jpegoptim
    jpegoptim --max=85 --strip-all -q "$file" 2>/dev/null || true
    
    # Convert to WebP
    local webp_file="${file%.jpg}.webp"
    cwebp -q 80 -m 6 "$file" -o "$webp_file" 2>/dev/null || true
    
    local size_after=$(stat -f%z "$webp_file" 2>/dev/null || stat -c%s "$webp_file")
    echo "  JPG: $(format_bytes $size_before) → WebP: $(format_bytes $size_after)"
    
    TOTAL_BEFORE=$((TOTAL_BEFORE + size_before))
    TOTAL_AFTER=$((TOTAL_AFTER + size_after))
}

# Function to optimize PNG
optimize_png() {
    local file=$1
    local size_before=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file")
    
    # Backup
    cp "$file" "$BACKUP_DIR/"
    
    # Optimize with optipng
    optipng -strip all -q "$file" 2>/dev/null || true
    
    # Convert to WebP
    local webp_file="${file%.png}.webp"
    cwebp -q 80 -m 6 "$file" -o "$webp_file" 2>/dev/null || true
    
    local size_after=$(stat -f%z "$webp_file" 2>/dev/null || stat -c%s "$webp_file")
    echo "  PNG: $(format_bytes $size_before) → WebP: $(format_bytes $size_after)"
    
    TOTAL_BEFORE=$((TOTAL_BEFORE + size_before))
    TOTAL_AFTER=$((TOTAL_AFTER + size_after))
}

# Process directories
echo -e "\n=== Processing Images ==="

for category in slider-main banner projects services team news clients; do
    if [ -d "$IMAGE_DIR/$category" ]; then
        echo "Processing $category..."
        
        # Process JPG files
        find "$IMAGE_DIR/$category" -maxdepth 1 -iname "*.jpg" -o -iname "*.jpeg" | while read file; do
            [ -f "$file" ] && optimize_jpg "$file"
        done
        
        # Process PNG files (skip icon-image for now)
        if [ "$category" != "icon-image" ]; then
            find "$IMAGE_DIR/$category" -maxdepth 1 -iname "*.png" | while read file; do
                [ -f "$file" ] && optimize_png "$file"
            done
        fi
    fi
done

echo -e "\n=== Optimization Summary ==="
echo "Total before: $(format_bytes $TOTAL_BEFORE)"
echo "Total after: $(format_bytes $TOTAL_AFTER)"
echo "Reduction: $((100 - (TOTAL_AFTER * 100 / TOTAL_BEFORE)))%"
echo "Backup location: $BACKUP_DIR"
echo -e "\n✓ Image optimization completed!"
echo "Note: Update image tags in templates to include WebP format with jpg/png fallback"

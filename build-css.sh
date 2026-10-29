#!/usr/bin/env bash
# ==============================================================================
# PakAutoParts - Tailwind CSS Build Script for Linux / Mac
# ==============================================================================

echo "Building production CSS with Tailwind CLI..."

if [ -f "./tailwindcss" ]; then
    chmod +x ./tailwindcss
    ./tailwindcss -i ./src/input.css -o ./public/assets/css/app.css --minify
    echo "Build complete: public/assets/css/app.css"
elif command -v npx &> /dev/null; then
    npx tailwindcss -i ./src/input.css -o ./public/assets/css/app.css --minify
    echo "Build complete via npx"
else
    echo "tailwindcss standalone CLI not found. Falling back to default app.css"
fi


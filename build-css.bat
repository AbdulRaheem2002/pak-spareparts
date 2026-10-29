@echo off
REM ==============================================================================
REM PakAutoParts - Tailwind CSS Build Script for Windows
REM Uses standalone Tailwind CLI without requiring Node.js runtime.
REM Download standalone CLI: https://github.com/tailwindlabs/tailwindcss/releases
REM ==============================================================================

echo Building production CSS with Tailwind CLI...

if exist tailwindcss.exe (
    tailwindcss.exe -i ./src/input.css -o ./public/assets/css/app.css --minify
    echo Build complete: public/assets/css/app.css
) else (
    echo Note: tailwindcss.exe standalone binary not found in root.
    echo If npx is available, falling back to npx tailwindcss...
    npx tailwindcss -i ./src/input.css -o ./public/assets/css/app.css --minify
)


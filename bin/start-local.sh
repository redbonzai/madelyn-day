#!/usr/bin/env bash
set -euo pipefail

project_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
required_node="24.19.0"
nvm_directory="${NVM_DIR:-${HOME}/.nvm}"

if [ -s "${nvm_directory}/nvm.sh" ]; then
    # shellcheck source=/dev/null
    source "${nvm_directory}/nvm.sh"
fi

if ! command -v nvm >/dev/null 2>&1; then
    echo "NVM is required but was not found. Install NVM, then run this script again." >&2
    exit 1
fi

if ! nvm version "${required_node}" >/dev/null 2>&1; then
    echo "Installing Node.js ${required_node} with NVM..."
    nvm install "${required_node}"
fi

nvm use "${required_node}"

madelyn_node_bin="$(dirname "$(nvm which "${required_node}")")"
export PATH="${madelyn_node_bin}:${PATH}"
hash -r

active_node="$(node --version)"
active_node_path="$(node -p 'process.execPath')"

if [ "${active_node}" != "v${required_node}" ]; then
    echo "Expected Node.js v${required_node}, but ${active_node} is active at ${active_node_path}." >&2
    exit 1
fi

echo "Using ${active_node} — ${active_node_path}"
cd "${project_dir}"

if [ ! -d node_modules ]; then
    npm install
fi

if ! node -e "require('fs-ext-extra-prebuilt')" >/dev/null 2>&1; then
    echo "Approving and rebuilding the WordPress Playground native dependency..."
    npm install-scripts approve fs-ext-extra-prebuilt
    npm rebuild fs-ext-extra-prebuilt
fi

if command -v lsof >/dev/null 2>&1 && lsof -nP -iTCP:8080 -sTCP:LISTEN >/dev/null 2>&1; then
    echo "Port 8080 is already in use. If Madelyn Day is already running, visit http://localhost:8080"
    echo "Stop the existing process before starting another local runtime."
    exit 0
fi

echo "Starting Madelyn Day at http://localhost:8080"
exec npm run dev

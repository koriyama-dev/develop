#!/bin/bash
SANDBOX=$1
if [ -z "$SANDBOX" ]; then
  echo "Usage: switch-sandbox.sh <sandbox_name>"
  exit 1
fi

# 全sandboxを削除
echo "Removing all sandboxes..."
for dir in /home/developer/develop/environment/sandbox*/; do
  if [ -f "$dir/docker-compose.yaml" ]; then
    cd "$dir" && docker compose down
  fi
done

# devcontainer.jsonを書き換え
sed -i "s|../../[^/]*/docker-compose.yaml|../../${SANDBOX}/docker-compose.yaml|" /home/developer/develop/environment/src/.devcontainer/devcontainer.json
echo "Switched to ${SANDBOX}"

# 新しいsandboxを起動
echo "Starting $SANDBOX..."
cd /home/developer/develop/environment/$SANDBOX && docker compose up -d --build

# vscode-server volumeの所有権を修正
echo "Fixing volume permissions..."
docker volume create ${SANDBOX}_devcontainer-vscode-server 2>/dev/null
docker run --rm \
  -v ${SANDBOX}_devcontainer-vscode-server:/var/www/.vscode-server \
  busybox chown -R 1000:1000 /var/www/.vscode-server

# cline-data volumeの所有権を修正
docker volume create ${SANDBOX}_devcontainer-cline-data 2>/dev/null
docker run --rm \
  -v ${SANDBOX}_devcontainer-cline-data:/var/www/.cline \
  busybox chown -R 1000:1000 /var/www/.cline

# VSCodeを起動
export SANDBOX_NAME=$SANDBOX && code /home/developer/develop/environment/src

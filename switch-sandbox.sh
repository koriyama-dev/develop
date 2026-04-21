#!/bin/bash
SANDBOX=$1

if [ -z "$SANDBOX" ]; then
  echo "Usage: switch-sandbox.sh <sandbox_name>"
  exit 1
fi

# 全sandboxを削除
echo "Removing all sandboxes..."
for dir in /home/developer/develop/sandbox*/; do
  if [ -f "$dir/docker-compose.yaml" ]; then
    cd "$dir" && docker compose down
  fi
done

# devcontainer.jsonを書き換え
sed -i "s|../../[^/]*/docker-compose.yaml|../../${SANDBOX}/docker-compose.yaml|" /home/developer/develop/src/.devcontainer/devcontainer.json
echo "Switched to ${SANDBOX}"

# 新しいsandboxを起動
echo "Starting $SANDBOX..."
cd /home/developer/develop/$SANDBOX && docker compose up -d --build

# VSCodeを起動
export SANDBOX_NAME=$SANDBOX && code /home/developer/develop/src

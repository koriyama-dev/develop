#!/bin/bash

# 全sandboxを削除
echo "Removing all sandboxes..."
for dir in /home/developer/develop/environment/sandbox*/; do
  if [ -f "$dir/docker-compose.yaml" ]; then
    cd "$dir" && docker compose down
  fi
done

echo "All sandboxes removed."

# /home/developer/develop/switch-sandbox.sh
#!/bin/bash
SANDBOX=$1
sed -i "s|../../sandbox[0-9]+/docker-compose.yaml|../../${SANDBOX}/docker-compose.yaml|" /home/developer/develop/src/.devcontainer/devcontainer.json
echo "Switched to ${SANDBOX}"
export SANDBOX_NAME=$SANDBOX && code /home/developer/develop/src

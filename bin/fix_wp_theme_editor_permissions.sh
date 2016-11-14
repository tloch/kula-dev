#!/bin/sh

GITBASEDIR="$(git rev-parse --show-toplevel)";

set -x
cd "$GITBASEDIR";

sudo chown :daemon -R kuladev
sudo chmod g+w -R kuladev


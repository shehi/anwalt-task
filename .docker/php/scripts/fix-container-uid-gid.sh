#!/usr/bin/env bash
set -e

# this script must be called with root permissions
if [[ $(id -g anwalt) != $2 || $(id -u anwalt) != $1 ]]; then
    groupmod -g $2 anwalt
    usermod -u $1 -g $2 anwalt
fi;

cp /etc/profile /home/anwalt/.profile
chown -R anwalt:anwalt /home/anwalt

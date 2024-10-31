#!/bin/bash
CURRENT_VERSION=`cat version.txt`;
NEW_VERSION=$1

sed -i "s/\(\s*\"version\":.*\)$CURRENT_VERSION/\1$NEW_VERSION/" bower.json
sed -i "s/\(\s*\"version\":.*\)$CURRENT_VERSION/\1$NEW_VERSION/" package.json
sed -i "s/\(\s*Stable tag:.*\)$CURRENT_VERSION/\1$NEW_VERSION/" readme.txt
sed -i "s/\(.*Version:.*\)$CURRENT_VERSION/\1$NEW_VERSION/" oboxmedia-ads-plugin.php
echo -n $NEW_VERSION > version.txt

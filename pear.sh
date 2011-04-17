#!/bin/sh
rm -f php2odp*.tgz
mkdir -p tmp/TheSeer/php2odp/odp
cp src/*.php tmp/TheSeer/php2odp
cp src/odp/*.odp tmp/TheSeer/php2odp/odp
cp src/odp/*.xml tmp/TheSeer/php2odp/odp
cp package.xml tmp
cp php2odp.bat tmp
sed -e "s@require __DIR__ . '/src/@require 'TheSeer/php2odp/@" php2odp.php > tmp/php2odp.php
cd tmp
pear package
mv php2odp*.tgz ..
cd ..
rm -rf tmp

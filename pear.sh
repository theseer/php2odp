#!/bin/sh
rm -f php2odp*.tgz
mkdir -p tmp/TheSeer/php2odp/odp
cp src/*.php tmp/TheSeer/php2odp
cp src/odp/*.odp tmp/TheSeer/php2odp/odp
cp src/odp/*.xml tmp/TheSeer/php2odp/odp
cp package.xml tmp
cp php2odp.bat php2odp.php tmp
cd tmp
pear package
mv php2odp*.tgz ..
cd ..
rm -rf tmp

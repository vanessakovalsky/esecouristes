#!/bin/bash
for file in `ls *.php`
#for file in `ls fonctions.php`
do
echo $file
cat $file | sed s/"2010 Nicolas"/"2011 Nicolas"/g | sed s/"version\: 2\.5"/"version\: 2\.6"/g > ${file}_2
mv ${file}_2 ${file}
#unix2dos ${file}
done
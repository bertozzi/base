#!/usr/bin/bash

if [ -e book ]; then
 rm -fr book
fi

mkdir book
cd book

for f in ../slidespdf/*pdf; do
 filename=$(basename -- "$f")
 name="${filename%.*}"
 echo $name
 qpdf --split-pages $f $name%d.pdf
done

#!/usr/bin/bash -x

:'
if [ -e book ]; then
 rm -fr book
fi

mkdir book
cd book

for f in `awk -F\; '{print $1}' < ../elenco-slides.csv`; do
 filename=$(basename -- "$f")
 name="${filename%.*}"
 echo $name
 qpdf --split-pages ../slidespdf/$f $name%d.pdf
done
'
cd book

cp ../head.tex book.tex

for f in `awk -F\; '{print $1}' < ../elenco-slides.csv`; do
  name="${f%.*}"
  echo $name
  par=0
  for i in $name*pdf; do
    par=$((par+1))
    echo "\centerline{\includegraphics[width=\textwidth]{$i}}" >> book.tex
    if [ $par -eq 2 ]; then
      echo "\Linepage" >> book.tex
      echo "\clearpage" >> book.tex
      par=0;
    fi
  done
  if [ $par -ne 0 ]; then
    echo "\Linepage" >> book.tex
    echo '\clearpage' >> book.tex
  fi
done


cat ../foot.tex >> book.tex

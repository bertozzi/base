<?php

$destdir="/home/httpd/main/didattica/informatica/esempi2022/";
if(!file_exists($destdir))
{
  echo "Dir di destinazione non esistente, sicuro di aver montato tutte le share di rete?";
  exit;
}

// creo elenco commentato esercizi
$listfile="$destdir/index.php";
$openlist=false;
$list=fopen($listfile, "w");

$tmppath=sys_get_temp_dir()."/finfo";

if(!file_exists($tmppath))
  mkdir($tmppath);


do{
  $tmpdir=$tmppath."/".substr(md5(mt_rand()), 0, 4);
}while(file_exists($tmpdir));

mkdir($tmpdir);
$tmpdir.="/esempi2022";
mkdir($tmpdir);

chdir("esempi2022"); // orig se su vuole l'altro

$files = array_merge(glob("*.[cs]") , glob("*.txt"));
sort($files);


$start=0;
$html=myhead();
$html.="<table>\n";
foreach($files as $file){
  if(!preg_match("/^[0-9]{4}-([a-z0-9]+).([cs]|txt)$/", $file, $m))
  {
    echo "$file does not match, ignoring it\n";
    continue;
  }
  if(preg_match("/^[0-9]{4}-([a-z0-9]+).([cs])$/", $file)) // sorgente
  {
    $nomefile=str_pad( $start, 4, "0", STR_PAD_LEFT )."-$m[1]";
    // copio file ai fini TAR
    shell_exec("cp -v $file $tmpdir/$nomefile.$m[2]\n");
    $start = $start + 10;
    // creo sorgente HTML
    $destfile=$destdir."/$nomefile.php";
    $gitdestfile=$nomefile.".".$m[2];
    shell_exec("source-highlight -fhtml -sc -n -t2 -i\"$file\" | evidenziaXXX > $destfile");
    $firstline = fgets(fopen($file, 'r'));
    if($firstline[0]=='/') // c'e' commento
      $firstline=substr($firstline,2);
    else
      $firstline="";

    $html.="<tr><td><a href=\"mostra.php?esercizio=$nomefile\">$nomefile</a></td><td style='padding-left: 20px'>$firstline</td></tr>";
  }
  else if(preg_match("/^[0-9]{4}-([a-z0-9]+).(txt)$/", $file))  // .txt
  {
    if($start and ($start-10))
    {
      $start=$start+(100 - (($start)%100));
    }
    $nomefile=str_pad( $start, 4, "0", STR_PAD_LEFT )."-$m[1]";
    $gitdestfile=$nomefile.".".$m[2];
    shell_exec("cp -v $file $tmpdir/$nomefile.$m[2]\n");
    $start = $start + 10;
    $firstline = fgets(fopen($file, 'r'));
    $html.="<tr><th colspan='2' style='padding: 20px; font-size: 150%;'>$firstline</th><tr>\n";
  }
  if($gitdestfile != $file)
    echo "git mv $file $gitdestfile\n";
}
$html.="</table>";

$html.='<?php 
global $localpage;
$localpage->pageclose();
?>';

fwrite($list, $html);
fclose($list);

$here=getcwd();
chdir($tmpdir);
chdir("..");
shell_exec("tar zcvf $destdir/esempi2022.tgz esempi2022");
chdir($here);
shell_exec("cp -v $destdir/esempi2022.tgz ..");


exit;

function myhead()
{
   return '<?php
     include("../header.php");
   
     $localpage=new kheader("Esempi di codice C");
     $localpage->set_descr("Esempi usati durante il corso di Informatica &amp; Laboratorio di Programmazione");
     $localpage->set_keys("informatica,ingegneria,unipr,programmazione,bertozzi,esempi,codice,lezioni");
     $localpage->set_backlink("$myroot");

     $localpage->dump();

     ?>

<img src="book.png" style="margin-left: 100px; float: right;">

Trovate nel seguito gli esempi di codice illustrati a lezione insieme alle relative slide.
<br>
&Egrave; anche disponibile l\'<a href="esempi2022.tgz">archivio</a> che li contiene tutti oppure la relativa <a href="https://github.com/bertozzi/finfo">pagina GitHub</a>.
<p>
<a href="..">Ritorna</a> alla pagina del corso.
<p>

';
}



















?>


<?php

$destdir="/home/httpd/main/didattica/informatica/labs/";
if(!file_exists($destdir))
{
  echo "Dir di destinazione non esistente, sicuro di aver montato tutte le share di rete?";
  exit;
}

chdir("lab"); // orig se su vuole l'altro

$files = glob("*.txt");
$dirs  = glob("*",  GLOB_ONLYDIR);
sort($files);

$titoli=array();

for($i = 0; $i < count($files); ++$i){
  $filecontent = file($files[$i]);
  if(!preg_match("|^\/\/\^.*|", $filecontent[0])) 
  {
    echo "File $files[$i] does not contain a valid header!";
    var_dump($filecontent[0]);
    continue;
  }

  $nomefile = $destdir."/".strip_ext($files[$i]).".php";

  $titolo = trim(str_replace("//^", "", $filecontent[0]));
  $titoli[strip_ext($files[$i])] = $titolo;
  echo "Genero pagina per $titolo\n";
  $content = myhead($titolo, $titolo).'<xmp>';
  for($j=1; $j<count($filecontent); ++$j)
    $content .= ($filecontent[$j]);
  $content .= '
    </xmp>
    <?php 
global $localpage;
$localpage->pageclose();
?>';


file_put_contents($nomefile, $content);
}

$maincontent = myhead("Esercizi di laboratorio", "Testi, soluzioni e codice degli esercizi di informatica in C proposti in laboratorio", "informatica,ingegneria,unipr,programmazione,bertozzi,codice,laboratorio,C");

$maincontent .="\n<ol>\n";
foreach($titoli as $filename=>$titolo)
{
  $maincontent .= " <li><a href=\"$filename.php\">$titolo</a></li>\n";
}
$maincontent .="</ol>\n".
'<?php 
global $localpage;
$localpage->pageclose();
?>';
file_put_contents($destdir."/index.php", $maincontent);





exit;

function myhead($title, $descr, $keys="informatica,ingegneria,unipr,programmazione,bertozzi,esempi,codice,lezioni")
{
   return '<?php
     include("../header.php");
   
     $localpage=new kheader("'.($title).'");
     $localpage->set_descr("'.($descr).'");
     $localpage->set_keys("'.($keys).'");
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


// rimuove l'estensione al nome di un file (o comunque cio' che si trova dopo $dot)
function strip_ext($name,$dot="."){
  $ext = strrchr($name, $dot);
  if($ext !== false) $name = substr($name, 0, -strlen($ext));
  return $name;
} 
















?>


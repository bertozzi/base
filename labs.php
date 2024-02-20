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
  if (file_exists($filename) and is_dir($filename))
  {
    if(!file_exists($destdir.'/'.$filename))
      mkdir($destdir.'/'.$filename);
    $dh = opendir($filename);
    $sorgenti = array();
    while (($file = readdir($dh)) !== false) {
      if(preg_match("/\.c$/", $file))
      {
	echo "filename: $file : filetype: " . filetype($filename . '/'. $file) . "\n";
	$solhead = myhead($file,$file);
	file_put_contents("$destdir/$filename/$file.php", $solhead);
        system("source-highlight -fhtml -sc --no-doc -n -t2 -i\"$filename/$file\" >> $destdir/$filename/$file.php");
	$solfoot = '<?php global $localpage; $localpage->pageclose(); ?>';
	file_put_contents("$destdir/$filename/$file.php", $solhead, FILE_APPEND);
	$sorgenti[$file] = "$filename/$file.php";

      }
    }
    ksort($sorgenti, SORT_NATURAL);
    $maincontent .= "  <ul>\n";
    foreach($sorgenti as $key=>$value)
    {
      $maincontent .= "    <li><a href='$value'>$key</a></li>\n";
    }
    $maincontent .= "  </ul>";


  }
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

<a href="..">Ritorna</a> alla pagina del corso.
<p>
Questa pagina contiene gli esercizi proposti in laboratorio e -alcune- delle soluzioni proposte.
<br />
Si suggerisce di provare a risolvere gli esercizi e solo in un secondo tempo di confrontare quanto fatto con le soluzioni proposte. 


';
}


// rimuove l'estensione al nome di un file (o comunque cio' che si trova dopo $dot)
function strip_ext($name,$dot="."){
  $ext = strrchr($name, $dot);
  if($ext !== false) $name = substr($name, 0, -strlen($ext));
  return $name;
} 



function delTree($dir) {

  $files = array_diff(scandir($dir), array('.','..'));

  foreach ($files as $file) {

    (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");

  }

  return rmdir($dir);

}












?>


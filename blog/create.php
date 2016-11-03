<?php

$template = file_get_contents("template/template.html");
$xml = simplexml_load_file("entradas.xml");
$template = explode("<**TITULO**>",$template);
$nTitulo= $_POST["titulo"];
$nContenido= $_POST["contenido"];
creaPost();

function creaPost(){
	Global $template,$nContenido,$nTitulo;
	$nEntrada = $template[0];
	$nEntrada = $nEntrada.$nTitulo;
	$template = explode("<**ENTRADA**>",$template[1]);
	$nEntrada = $nEntrada.$template[0];
	$nEntrada = $nEntrada.$nContenido."<br>";
	$nEntrada = $nEntrada.$template[1];

	guardaPost($nEntrada);
}

function guardaPost($newEntry){
	Global $xml, $nTitulo;
	setlocale(LC_TIME,"es_ES");
	$entrada = $xml->addChild('entrada');
	$entrada->addChild('titulo',$_POST['titulo']);
	$entrada->addChild('texto',$_POST['contenido']);
	$xml->asXML("entradas.xml");
	chmod("entradas.xml",0777);
	
	$dir = "entradas/".$_POST['titulo'].".html";
	$f = fopen($dir,"w");
	chmod($dir,0777);
	fputs($f,$newEntry);
	fclose($f);

	updateIndex();

	header("Location: entradas/".$_POST['titulo'].".html");
}

function updateIndex(){
	Global $xml;
	$indice = file_get_contents("index.html");
	$indice = explode("<section>",$indice,2);
	$nIndice= $indice[0]."<section>";
  
  	foreach ($xml->entrada as $entrada => $value) {
    	$titulo = $value->titulo;
    	$texto= $value->texto;
    	$tmp = "<article>";
    	$tmp = $tmp."<h3><b>".$titulo."</b></h3><br>";   	
      	$texto = "<i>".substr($texto,0,50)." ... </i><br><br><a href='entradas/".$titulo.".html'>Abrir entrada.</a>";
    	$tmp = $tmp."<p>".$texto."</p></article><hr><br>";
    	$nIndice= $nIndice.$tmp;
  	}
  	$indice= explode("<footer>",$indice[1]);

  	$nIndice = $nIndice."</section><footer>".$indice[1];
  	$dir = "index.html";
	$f = fopen($dir,"w");
	chmod($dir,0777);
	fputs($f,$nIndice);
	fclose($f);
}

?>
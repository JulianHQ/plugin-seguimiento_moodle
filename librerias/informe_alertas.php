<html>
    <head>  
      <meta charset="UTF-8">
      <title>PDF</title>   
</head>	

<body>

<?php

//Datos para el informe
$header = $_POST['header'];
$intro = $_POST['intro'];
$tipo1 = $_POST['tipo1'];
$tipo2 = $_POST['tipo2'];
$tipo3 = $_POST['tipo3'];
$tipo4 = $_POST['tipo4'];
$tipo5 = $_POST['tipo5'];
$tipo6 = $_POST['tipo6'];
$tipo7 = $_POST['tipo7'];
$tipo8 = $_POST['tipo8'];
$tipo9 = $_POST['tipo9'];
$anexo = $_POST['anexo'];


include ('lib/mpdf.php');

$texto = $intro . $tipo1 . $tipo2 . $tipo3 . $tipo4 . $tipo5 . $tipo6 . $tipo7 . $tipo8 . $tipo9;

$pdf = new mPDF(); // Generamos un objeto nuevo html2fpdf  
//$pdf -> AddPage(); // Añadimos una página 
$pdf->SetHeader($header); 
$pdf->SetFooter('{PAGENO}');
$pdf -> WriteHTML($texto); // Indicamos la variable con el contenido que queremos incluir en el pdf  
$pdf -> addPage();
$pdf -> WriteHTML($anexo);

$pdf -> Output('informe.pdf', 'I'); //Generamos el archivo "archivo_pdf.pdf". Ponemos como parametro 'D' para forzar la descarga del archivo.  

?>                  

</body>
</html>
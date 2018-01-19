<?php

	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/estilo1.css\">";

	$email_destinatario = htmlspecialchars($_GET["email_d"]); //email del usuario seleccionado (destino)
	$email_remitente = htmlspecialchars($_GET["email_r"]); //email del usuario actual (remitente)
	//echo $email_destinatario;
	//echo $email_remitente;
	echo "<section id=\"ventana1\" align=\"center\">
    <section id=\"contenido\">
    <h1>Sistema de mensajería interno</h1>

    
    <form action=\"\" method=\"POST\" enctype=\"multipart/form-data\"> 
    <table align=\"center\">	
    	<tr>
	    	<td>Para:</td>
	    	<td><div style=\"position:relative; width:700; overflow:auto\">$email_destinatario</div></td>
    	</tr>
    	<tr>
   			<td>Asunto*:</td>
   			<td><input name=\"asunto\" size=\"98\" type=\"text\" maxlength=\"250\" required/></td>
    	</tr>
    	<tr>
   			<td>Mensaje*:</td>
   			<td><textarea name=\"mensaje\" rows=\"10\" cols=\"100\" required></textarea></td>
    	</tr>
		<br>
      <tr>
      <td>Adjuntar archivo:</td>
      <td><input type='file' name='archivo' id='archivo'></td>
      </tr>
		<tr>
			<td></td>
			<td><input class=\"btn btn-default\" type=\"submit\" value=\"Enviar\" />
        <button type=\"button\" class=\"btn btn-primary\" onclick=\"self.close()\">Salir</button>
				</td>
		</tr>
     </table>  </form>
     
    </section>";
    if(isset($_POST['asunto'])) {

        $bHayFicheros = 0;
        $sCabeceraTexto = "";
        $sAdjuntos = "";
         
        if ($email_remitente)$sCabeceras = "From:".$email_remitente."\n";
        else $sCabeceras = "";
        $sCabeceras .= "MIME-version: 1.0\n";
        
        //Mensaje que se enviará al usuario seleccionado
            $destino= $email_destinatario; //usuario seleccionado
            $asunto = $_POST['asunto'];
            $remitente = $email_remitente; //El usuario actual
            $mensaje= $_POST['mensaje'];
            $mensaje.="\n\n\nEste mensaje ha sido enviado a través de la plataforma moodle";
            $mensaje.= "\nPara responder a este mensaje comuniquese directamente con " . $email_remitente; //el usuario actual
         
        foreach ($_FILES as $vAdjunto)
        {
          if ($bHayFicheros == 0){
            $bHayFicheros = 1;
            $sCabeceras .= "Content-type: multipart/mixed;";
            $sCabeceras .= "boundary=\"--_Separador-de-mensajes_--\"\n";
             
            $sCabeceraTexto = "----_Separador-de-mensajes_--\n";
            $sCabeceraTexto .= "Content-type: text/plain;charset=iso-8859-1\n";
            $sCabeceraTexto .= "Content-transfer-encoding: 7BIT\n";
             
            $mensaje = $sCabeceraTexto.$mensaje;
          }

          if ($vAdjunto["size"] > 0){
            $sAdjuntos .= "\n\n----_Separador-de-mensajes_--\n";
            $sAdjuntos .= "Content-type: ".$vAdjunto["type"].";name=\"".$vAdjunto["name"]."\"\n";
            $sAdjuntos .= "Content-Transfer-Encoding: BASE64\n";
            $sAdjuntos .= "Content-disposition: attachment;filename=\"".$vAdjunto["name"]."\"\n\n";
             
            $oFichero = fopen($vAdjunto["tmp_name"], 'r');
            $sContenido = fread($oFichero, filesize($vAdjunto["tmp_name"]));
            $sAdjuntos .= chunk_split(base64_encode($sContenido));
            fclose($oFichero);
          }
        }
       
        if ($bHayFicheros)
        $mensaje .= $sAdjuntos."\n\n----_Separador-de-mensajes_----\n";
        mail($destino, $asunto, $mensaje, $sCabeceras);
    
        //Copia del mensaje (se envía al autor del mismo)
        $destino= $email_remitente; //el usuario actual
        $asunto = "Copia - " . $_POST['asunto'];
        $remitente = $email_remitente; //El usuario actual
        $mensaje.="\n\n\nEste mensaje fue enviado a $email_destinatario a través de la plataforma moodle"; //usuario seleccionado
        mail($destino,$asunto,$mensaje,$sCabeceras);
        
        echo "<script>
              alert(\"Mensaje enviado satisfactoriamente\");
              window.close();
              </script>";
    }

?>


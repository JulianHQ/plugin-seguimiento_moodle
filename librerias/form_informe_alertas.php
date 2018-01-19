<?php

//Aquí se consultan y envían las variables que se deben mostrar en el pdf de informe de alertas enviadas

//Introducción del pdf
$intro = "<h3 align='center'>Resumen de alertas enviadas</h3><br>";

$header = "Generado por: " . $USER->firstname . " " . $USER->lastname . "       Fecha: " . date('d/m/Y');

$tipo1 = null;
$tipo2 = null;
$tipo3 = null;
$tipo4 = null;
$tipo5 = null;
$tipo6 = null;
$tipo7 = null;
$tipo8 = null;
$tipo9 = null;

$alerta1 = 0;
$alerta2 = 0;
$alerta3 = 0;
$alerta4 = 0;
$alerta5 = 0;
$alerta6 = 0;
$alerta7 = 0;
$alerta8 = 0;
$alerta9 = 0;

$anexo = "<h2 align='center'>Anexos</h2>
		<h4>A continuación se muestra el significado de cada tipo de alerta:</h4>
			<table align='center' style='border-collapse: collapse'>
				<thead>
					<tr style='border:1'>
						<th>
							Tipo
						</th>
						<th>
							Descripción
						</th>
						<th>
							Se envia a
						</th>
					</tr>
				</thead>
				<tbody>
					<tr style='border:1'>
						<td>
							1
						</td>
						<td>
							Cantidad de productos no han sido entregados.
						</td>
						<td>
							Estudiante
						</td>
					</tr>
					<tr style='border:1'>
						<td>
							2
						</td>
						<td>
							Cantidad de días sin que el estudiante ingrese a la plataforma.
						</td>
						<td>
							Estudiante
						</td>
					</tr>
					<tr style='border:1'>
						<td>
							3
						</td>
						<td>
							Cantidad de días sin que el docente ingrese a la plataforma.
						</td>
						<td>
							Docente
						</td>
					</tr>
					<tr style='border:1'>
						<td>
							4
						</td>
						<td>
							Estudiante en riesgo por malas calificaciones.
						</td>
						<td>
							Docente
						</td>
					</tr>
					<tr style='border:1'>
						<td>
							5
						</td>
						<td>
							Cantidad de productos sin ser entregados.
						</td>
						<td>
							Docente
						</td>
					</tr>
					<tr style='border:1'>
						<td>
							6
						</td>
						<td>
							Cantidad de días sin que el docente ingrese a plataforma.
						</td>
						<td>
							Administrador (coordinador de programa)
						</td>
					</tr>
					<tr style='border:1'>
						<td>
							7
						</td>
						<td>
							Cantidad de días sin que el docente ingrese a plataforma.
						</td>
						<td>
							Administrador (coordinador de programa)
						</td>
					</tr>
					<tr style='border:1'>
						<td>
							8
						</td>
						<td>
							Cantidad de días vencidos del tiempo para calificar y retroalimentar.
						</td>
						<td>
							Docente
						</td>
					</tr>
					<tr style='border:1'>
						<td>
							9
						</td>
						<td>
							Cantidad de días vencidos del tiempo para calificar y retroalimentar.
						</td>
						<td>
							Administrador (coordinador de programa)
						</td>
					</tr>
				</tbody>
			</table>

			<br>";

//Consultar tabla: seguimiento_registro
$query_registro = "SELECT id AS id_registro,
					category,
                    course,
                    id_destino,
                    alerta_tipo,
                    alerta_fecha,
                    alerta_mensaje
                FROM {seguimiento_registro}";
    $files_data_registro=$DB->get_records_sql($query_registro,array('.','id_registro','0'));

    foreach ($files_data_registro as $fd_r) {

    	$query_usuario = "SELECT id,
    				username,
    				idnumber,
    				firstname,
    				lastname,
    				email,
    				phone1,
    				phone2
    			FROM {user} WHERE id = $fd_r->id_destino";
    		$files_data_usuario = $DB->get_records_sql($query_usuario,array('.','id','0'));

    		foreach ($files_data_usuario as $fd_u) {
    			$fd_r->id_destino = $fd_u->firstname . " " . $fd_u->lastname;
    		}

    	if ($fd_r->course != null) {
    		$query_curso = "SELECT id,
	    				fullname,
	    				shortname
	    			FROM {course} WHERE id = $fd_r->course";
	    		$files_data_curso = $DB->get_records_sql($query_curso,array('.','id','0'));

	    		foreach ($files_data_curso as $fd_c) {
	    			$fd_r->course = $fd_c->shortname;
	    		}
    	}else{
    		$query_category = "SELECT id,
	    				name,
	    				description
	    			FROM {course_categories} WHERE id = $fd_r->category";
	    		$files_data_category = $DB->get_records_sql($query_category,array('.','id','0'));

	    		foreach ($files_data_category as $fd_c) {
	    			$fd_r->category = $fd_c->name;
	    		}
    	}
    	

    	switch ($fd_r->alerta_tipo) {
    		case 1:
    			if ($tipo1 == null) {
    				$tipo1 = "<b>Alerta tipo 1.</b>
    						<table>
								<thead>
									<tr>
										<th align='left'>Curso</th>
										<th align='left'>Destino</th>
										<th align='left'>Fecha</th>
									</tr>
								</thead>
								<tbody>";
    			}

    			$tipo1 .= "<tr><td>$fd_r->course</td>
    					<td>$fd_r->id_destino</td>
    					<td>".date('d/m/Y', $fd_r->alerta_fecha)."</td></tr>";
    			$alerta1 += $alerta1;
    			break;
    		case 2:
    			if ($tipo2 == null) {
    				$tipo2 = "<b>Alerta tipo 2.</b>
    						<table>
								<thead>
									<tr>
										<th align='left'>Programa</th>
										<th align='left'>Destino</th>
										<th align='left'>Fecha</th>
									</tr>
								</thead>
								<tbody>";
    			}

    			$tipo2 .= "<tr><td>$fd_r->category</td>
    					<td>$fd_r->id_destino</td>
    					<td>".date('d/m/Y', $fd_r->alerta_fecha)."</td></tr>";
    			$alerta2 += $alerta2;
    			break;
    		case 3:
    			if ($tipo3 == null) {
    				$tipo3 = "<b>Alerta tipo 3.</b>
    						<table>
								<thead>
									<tr>
										<th align='left'>Programa</th>
										<th align='left'>Destino</th>
										<th align='left'>Fecha</th>
									</tr>
								</thead>
								<tbody>";
    			}

    			$tipo3 .= "<tr><td>$fd_r->category</td>
    					<td>$fd_r->id_destino</td>
    					<td>".date('d/m/Y', $fd_r->alerta_fecha)."</td></tr>";
    			$alerta3 += $alerta3;
    			break;
    		case 4:
    			if ($tipo4 == null) {
    				$tipo4 = "<b>Alerta tipo 4.</b>
    						<table>
								<thead>
									<tr>
										<th align='left'>Curso</th>
										<th align='left'>Destino</th>
										<th align='left'>Fecha</th>
									</tr>
								</thead>
								<tbody>";
    			}

    			$tipo4 .= "<tr><td>$fd_r->course</td>
    					<td>$fd_r->id_destino</td>
    					<td>".date('d/m/Y', $fd_r->alerta_fecha)."</td></tr>";
    			$alerta4 += $alerta4;
    			break;
    		case 5:
    			if ($tipo5 == null) {
    				$tipo5 = "<b>Alerta tipo 5.</b>
    						<table>
								<thead>
									<tr>
										<th align='left'>Curso</th>
										<th align='left'>Destino</th>
										<th align='left'>Fecha</th>
									</tr>
								</thead>
								<tbody>";
    			}

    			$tipo5 .= "<tr><td>$fd_r->course</td>
    					<td>$fd_r->id_destino</td>
    					<td>".date('d/m/Y', $fd_r->alerta_fecha)."</td></tr>";
    			$alerta5 += $alerta5;
    			break;
    		case 6:
    			if ($tipo6 == null) {
    				$tipo6 = "<b>Alerta tipo 6.</b>
    						<table>
								<thead>
									<tr>
										<th align='left'>Programa</th>
										<th align='left'>Destino</th>
										<th align='left'>Fecha</th>
									</tr>
								</thead>
								<tbody>";
    			}

    			$tipo6 .= "<tr><td>$fd_r->category</td>
    					<td>$fd_r->id_destino</td>
    					<td>".date('d/m/Y', $fd_r->alerta_fecha)."</td></tr>";
    			$alerta6 += $alerta6;
    			break;
    		case 7:
    			if ($tipo7 == null) {
    				$tipo7 = "<b>Alerta tipo 7.</b>
    						<table>
								<thead>
									<tr>
										<th align='left'>Programa</th>
										<th align='left'>Destino</th>
										<th align='left'>Fecha</th>
									</tr>
								</thead>
								<tbody>";
    			}

    			$tipo7 .= "<tr><td>$fd_r->category</td>
    					<td>$fd_r->id_destino</td>
    					<td>".date('d/m/Y', $fd_r->alerta_fecha)."</td></tr>";
    			$alerta7 += $alerta7;
    			break;
    		case 8:
    			if ($tipo8 == null) {
    				$tipo8 = "<b>Alerta tipo 8.</b>
    						<table>
								<thead>
									<tr>
										<th align='left'>Curso</th>
										<th align='left'>Destino</th>
										<th align='left'>Fecha</th>
									</tr>
								</thead>
								<tbody>";
    			}

    			$tipo8 .= "<tr><td>$fd_r->course</td>
    					<td>$fd_r->id_destino</td>
    					<td>".date('d/m/Y', $fd_r->alerta_fecha)."</td></tr>";
    			$alerta8 += $alerta8;
    			break;
    		case 9:
    			if ($tipo9 == null) {
    				$tipo9 = "<b>Alerta tipo 9.</b>
    						<table>
								<thead>
									<tr>
										<th align='left'>Curso</th>
										<th align='left'>Destino</th>
										<th align='left'>Fecha</th>
									</tr>
								</thead>
								<tbody>";
    			}

    			$tipo9 .= "<tr><td>$fd_r->course</td>
    					<td>$fd_r->id_destino</td>
    					<td>".date('d/m/Y', $fd_r->alerta_fecha)."</td></tr>";
    			$alerta9 += $alerta9;
    			break;
    		default:
    			# code...
    			break;
    	}
    }

if ($tipo1 != null) {
	$tipo1 .= "</tbody>
			</table><br><br>";
}
if ($tipo2 != null) {
	$tipo2 .= "</tbody>
			</table><br><br>";
}
if ($tipo3 != null) {
	$tipo3 .= "</tbody>
			</table><br><br>";
}
if ($tipo4 != null) {
	$tipo4 .= "</tbody>
			</table><br><br>";
}
if ($tipo5 != null) {
	$tipo5 .= "</tbody>
			</table><br><br>";
}
if ($tipo6 != null) {
	$tipo6 .= "</tbody>
			</table><br><br>";
}
if ($tipo7 != null) {
	$tipo7 .= "</tbody>
			</table><br><br>";
}
if ($tipo8 != null) {
	$tipo8 .= "</tbody>
			</table><br><br>";
}
if ($tipo9 != null) {
	$tipo9 .= "</tbody>
			</table><br><br>";
}

	echo "
	<div align=\"center\" id=\"\" style=\"display:none\">
    	<form id=\"\" name=\"informe_alertas\" method=\"post\" action=\"librerias/informe_alertas.php\">
    		<input id=\"texto\" name=\"header\" type=\"hidden\" value= \"$header\">            
    		<input id=\"texto\" name=\"intro\" type=\"hidden\" value= \"$intro\">            
    		<input id=\"texto\" name=\"tipo1\" type=\"hidden\" value= \"$tipo1\">            
    		<input id=\"texto\" name=\"tipo2\" type=\"hidden\" value= \"$tipo2\">            
    		<input id=\"texto\" name=\"tipo3\" type=\"hidden\" value= \"$tipo3\">            
    		<input id=\"texto\" name=\"tipo4\" type=\"hidden\" value= \"$tipo4\">            
    		<input id=\"texto\" name=\"tipo5\" type=\"hidden\" value= \"$tipo5\">            
    		<input id=\"texto\" name=\"tipo6\" type=\"hidden\" value= \"$tipo6\">            
    		<input id=\"texto\" name=\"tipo7\" type=\"hidden\" value= \"$tipo7\">            
    		<input id=\"texto\" name=\"tipo8\" type=\"hidden\" value= \"$tipo8\">            
    		<input id=\"texto\" name=\"tipo9\" type=\"hidden\" value= \"$tipo9\">            
    		<input id=\"texto\" name=\"anexo\" type=\"hidden\" value= \"$anexo\">            
    		<button id=\"informe_alertas\" type=\"button\" class=\"\" onclick=\"submit()\">Consultar</button>
    	</form>
    </div>";
?>
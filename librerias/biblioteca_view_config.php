<!--Librerias para mostrar/ocultar secciones-->
<script type="text/javascript" src="js/mostrar.js"></script> 
<script type="text/javascript" src="js/ocultar.js"></script>

<!--PRODUCTOS: Validación de campos configurados anteriormente-->
<script type="text/javascript">
	function checkbox0(checkbox){
		var campo = document.getElementById('riesgo_e_umbral');
		if (checkbox.checked == false) {
			campo.disabled = true;
		}else if(checkbox.checked == true){
			campo.disabled = false;
		};	
	}
	function checkbox1(checkbox){
		var campo = document.getElementById('prod_e_umbral1');
		var campomensaje = document.getElementById('prod_e_mensaje1');
		if (checkbox.checked == false) {
			campo.disabled = true;
			campomensaje.disabled = true;
		}else if(checkbox.checked == true){
			campo.disabled = false;
			campomensaje.disabled = false;
		};	
	}
	function checkbox2(checkbox){
		var campo = document.getElementById('prod_e_umbral2');
		if (checkbox.checked == false) {
			campo.disabled = true;
		}else if(checkbox.checked == true){
			campo.disabled = false;
		};	
	}

	function checkbox3(checkbox){
		var campo = document.getElementById('prod_d_umbral1');
		var campomensaje = document.getElementById('prod_d_mensaje1');
		if (checkbox.checked == false) {
			campo.disabled = true;
			campomensaje.disabled = true;
		}else if(checkbox.checked == true){
			campo.disabled = false;
			campomensaje.disabled = false;
		};	
	}
	function checkbox4(checkbox){
		var campo = document.getElementById('prod_d_umbral2');
		if (checkbox.checked == false) {
			campo.disabled = true;
		}else if(checkbox.checked == true){
			campo.disabled = false;
		};	
	}
	function checkbox5(checkbox){
		var campo = document.getElementById('ingr_e_umbral1');
		var campomensaje = document.getElementById('ingr_e_mensaje1');
		if (checkbox.checked == false) {
			campo.disabled = true;
			campomensaje.disabled = true;
		}else if(checkbox.checked == true){
			campo.disabled = false;
			campomensaje.disabled = false;
		};	
	}
	function checkbox6(checkbox){
		var campo = document.getElementById('ingr_e_umbral2');
		if (checkbox.checked == false) {
			campo.disabled = true;
		}else if(checkbox.checked == true){
			campo.disabled = false;
		};	
	}

	function checkbox7(checkbox){
		var campo = document.getElementById('ingr_d_umbral1');
		var campomensaje = document.getElementById('ingr_d_mensaje1');
		if (checkbox.checked == false) {
			campo.disabled = true;
			campomensaje.disabled = true;
		}else if(checkbox.checked == true){
			campo.disabled = false;
			campomensaje.disabled = false;
		};	
	}
	function checkbox8(checkbox){
		var campo = document.getElementById('ingr_d_umbral2');
		if (checkbox.checked == false) {
			campo.disabled = true;
		}else if(checkbox.checked == true){
			campo.disabled = false;
		};	
	}
</script>

<?php

/*
Biblioteca de configuración de alertas por productos no entregados (estudiante) y no calificados (docente)
*/

$msj_curso = null;
/*Mensajes configurados inicialmente*/
$query_alertas_mensajes = "SELECT id AS id,
                    course,
                    mensaje
                FROM {seguimiento} WHERE course = $COURSE->id";
    $files_data0=$DB->get_records_sql($query_alertas_mensajes,array('.','id','0'));

    foreach ($files_data0 as $fd0) {
        if ($fd0->mensaje != null) {
            $msj_curso = $fd0->mensaje;
        }
    }

$default_ingresos_activo1 = false;
$default_ingresos_activo2 = false;
$default_productos_activo = false;

//Consultar valores definidos para el curso actual
	$query_config = "SELECT id as configid,
                        course_context,
                        course_instance,
                        e_riesgo_umbral,
                        e_productos_umbral1,
                        e_productos_umbral2,
                        e_productos_mensaje,
                        d_productos_umbral1,
                        d_productos_umbral2,
                        d_productos_mensaje
                FROM {seguimiento_config_productos} WHERE course_context = \"$asignatura->id\"";
                $files_data=$DB->get_records_sql($query_config,array('.','configid','0'));

                foreach ($files_data as $fd) {
                	$productos_config->course_context = $fd->course_context;
                	$productos_config->course_instance = $fd->course_instance;
                	$productos_config->e_riesgo_umbral = $fd->e_riesgo_umbral;
                	$productos_config->e_productos_umbral1 = $fd->e_productos_umbral1;
                	$productos_config->e_productos_umbral2 = $fd->e_productos_umbral2;
                	$productos_config->e_productos_mensaje = $fd->e_productos_mensaje;
                	$productos_config->d_productos_umbral1 = $fd->d_productos_umbral1;
                	$productos_config->d_productos_umbral2 = $fd->d_productos_umbral2;
                	$productos_config->d_productos_mensaje = $fd->d_productos_mensaje;

        		};

        if ($productos_config->e_productos_mensaje == null && $productos_config->e_productos_umbral1 != null) {
        	$productos_config->e_productos_mensaje = $msj_curso;
        }



//ventana emergente para configuración de alertas

echo "<div id=\"modal_config\" style=\"display:none\">
		<input id=\"cerrar-modal_config\" name=\"modal_config\" type=\"radio\" /> 
		<label for=\"cerrar-modal_config\" onclick=\"ocultarConfig()\"><img src=\"css/img/cerrar_icon.png\" width=\"30\" height=\"30\"/></label> 
		<br><br>
		<h1 style=\"text-align: center\">Configuración del sistema de alertas tempranas</h1>
		<br>
		<form name=\"config_alertas\" method=\"post\" action=\"$link\">";

                

//Botones mosrtar/ocultar secciones
if ($usuario_permitido_admin == true) {
	echo "<div align=\"center\" style=\"display: inline; width: 100px; float: left\">
	        <br><br>
	        <button type=\"button\" style=\"WIDTH: 190px; HEIGHT: 50px\" onclick=\"mostrar1(); ocultar2()\">Productos</button><br><br>
	        <button type=\"button\" style=\"WIDTH: 190px; HEIGHT: 50px\" onclick=\"mostrar2(); ocultar1()\">Ingreso a plataforma</button><br><br>
	        <button type=\"button\" style=\"WIDTH: 190px; HEIGHT: 50px\" onclick=\"informealertas()\">Informe de alertas</button><br><br>
	      </div>";
}else{
	echo "<div align=\"center\" style=\"display: inline; float: left\">
	        <br><br>
	        <button type=\"button\" style=\"WIDTH: 190px; HEIGHT: 50px\" onclick=\"mostrar1b(); ocultar2b()\">Productos</button><br><br>
	        <button type=\"button\" style=\"WIDTH: 190px; HEIGHT: 50px\" onclick=\"mostrar2b(); ocultar1b()\">Ingreso a plataforma</button><br><br>
	      </div>";
};


/*
ALERTAS POR PRODUCTOS
*/

//Configuración visible para todos los roles
                echo "	<!--Alertas por productos-->
                			<div>
                				<table id=\"Seccion0\" style='display:block'>
                					<tr>"; 
                						if ($productos_config->e_riesgo_umbral != null AND $productos_config->e_riesgo_umbral != 0) {
			            	    			echo "<td><input name=\"e_riesgo_activo1\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox0(this)\" checked/>";
			                				echo "Riesgo académico en el: <input name=\"e_riesgo_umbral\" type=\"number\" id=\"riesgo_e_umbral\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"100\" title=\"Porcentaje del curso calificado\" value=\"$productos_config->e_riesgo_umbral\"/> % del curso.</td>";
			                			}else{
			            	    			echo "<td><input name=\"e_riesgo_activo1\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox0(this)\"/>";
			                				echo "Riesgo académico en el: <input name=\"e_riesgo_umbral\" type=\"number\" id=\"riesgo_e_umbral\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"100\" disabled=\"true\" title=\"Porcentaje del curso calificado\" value=\"$productos_config->e_riesgo_umbral\"/> % del curso.</td>";
			                			}

                						
                						
                						
                				echo"</tr>
                				</table>
                			</div>
                			

                			<div align=\"center\">
		                		<table id=\"Seccion1\" style='display:block'>";

											//Si se encuentra por fuera de los cursos
							                if ($asignatura->category == 0) {
										        global $DB;
										        $query_course = "SELECT id AS id,
										                            fullname,
										                            shortname
										                    FROM {course} WHERE format != \"site\"";
										        $files_data_site=$DB->get_records_sql($query_course,array('.','course','0'));

										        echo "<tr><td colspan=\"5\"><select name=\"asignatura_seleccionada\">";

										        $i = 1;
										        

										        if ($usuario_permitido_admin == true) {
										            foreach ($files_data_site as $fd_site) {
										                $i++;
										                $data = new stdClass();
										                $data->id = $fd_site->id;
										                $data->fullname = $fd_site->fullname;
										                $data->shortname = $fd_site->shortname;
										                
										                echo "<option value=\"$i\">$data->fullname</option>";

										            };
										        }else{
										            //Consultar los cursos permitidos para el usuario actual
										            $query_role_assignments = "SELECT id AS id,
										                                    roleid,
										                                    contextid,
										                                    userid
										                            FROM {role_assignments} WHERE userid = $id_usuario";
										                $files_data=$DB->get_records_sql($query_role_assignments,array('.','contextid','0'));

										                foreach ($files_data as $fd) {
										                    //Obtener instanceid de la tabla "context"
										                    $query_context = "SELECT id AS id,
										                                    contextlevel,
										                                    instanceid
										                            FROM {context} WHERE contextlevel = 50 AND id = $fd->contextid"; 
										                    $files_data1=$DB->get_records_sql($query_context,array('.','context','0'));

										                    foreach ($files_data1 as $fd1) {
										                        $query_course = "SELECT id AS course_id,
										                                    fullname,
										                                    shortname
										                            FROM {course} WHERE id = $fd1->instanceid"; 
										                        $files_data2=$DB->get_records_sql($query_course,array('.','course_id','0'));

										                        foreach ($files_data2 as $fd2) {
										                            echo "<option value=\"$fd2->course_id\">$fd2->fullname</option>";
										                        }
										                        
										                    }
										                            
										                };

										        }
										        
										                        
										        
										        echo"</select></td></tr>
										                <input type=\"hidden\" name=\"listado\" value=$listado/>";
										    }
										    //Fin: si se encuentra por fuera de los cursos

			    echo "         			<tr>";
			                						
			                				if ($productos_config->e_productos_umbral1 != null AND $productos_config->e_productos_umbral1 != 0) {
			            	    				echo "<td colspan=\"2\"><input name=\"e_productos_activo1\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox1(this)\" checked/>";
			                					echo "Umbral de alerta:</td>";
			                					echo "<td><input name=\"e_productos_umbral1\" type=\"number\" id=\"prod_e_umbral1\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"999\" title=\"Valor mínimo 1 - máximo 999\" value=\"$productos_config->e_productos_umbral1\"/></td>";
			                				}else{
			                					echo "<td colspan=\"2\"><input name=\"e_productos_activo1\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox1(this)\"/>";
			                					echo "Umbral de alerta:</td>";
			                					echo "<td><input name=\"e_productos_umbral1\" type=\"number\" id=\"prod_e_umbral1\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"999\" disabled=\"true\" title=\"Valor mínimo 1 - máximo 999\" value=\"$productos_config->e_productos_umbral1\"/></td>";
			                				}

			                				echo "
			                			<td>";

				                			
				                			echo "
			                			</td>
			                			<td rowspan=\"4\">
			                				<h4 align=\"center\">Estudiante: Entrega de productos</h4>
			                				<br>
			                				<p>Se enviará un mensaje personalizado a los estudiantes
			                				 que no entreguen una <b>cantidad determinada de productos</b> 
			                				 (umbral alerta) y un mensaje al docente al cumplirse el 
			                				 umbral de alerta crítico.</p>
			                				
			                			</td>

			                		</tr>
			                		<tr>";

			                				if ($productos_config->e_productos_umbral2 != null AND $productos_config->e_productos_umbral2 != 0) {
			            	    				echo "<td colspan=\"2\"><input name=\"e_productos_activo2\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox2(this)\" checked/>";
			                					echo "Umbral de alerta (crítico):</td>";
			                					echo "<td><input name=\"e_productos_umbral2\" type=\"number\" id=\"prod_e_umbral2\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"999\" title=\"Valor mínimo 1 - máximo 999\" value=\"$productos_config->e_productos_umbral2\"/></td>";
			                				}else{
			                					echo "<td colspan=\"2\"><input name=\"e_productos_activo2\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox2(this)\"/>";
			                					echo "Umbral de alerta (crítico):</td>";
			                					echo "<td><input name=\"e_productos_umbral2\" type=\"number\" id=\"prod_e_umbral2\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"999\" disabled=\"true\" title=\"Valor mínimo 1 - máximo 999\" value=\"$productos_config->e_productos_umbral2\"/></td>";
			                				}

			                				echo "
			                			<td>";
				                			
				                			echo "
			                			</td>
			                		</tr>
			                		<tr>
			                			<td>
			                				Mensaje personalizado:
			                			</td>
			                		</tr>
			                		<tr>";
			                			if ($productos_config->e_productos_umbral1 != null AND $productos_config->e_productos_umbral1 != 0) {
			                					echo "<td colspan=\"3\"><textarea id=\"prod_e_mensaje1\" rows=\"4\" cols=\"50\" name=\"e_productos_mensaje\"/>$productos_config->e_productos_mensaje</textarea></td>";
			                			}else{
			                					echo "<td colspan=\"3\"><textarea id=\"prod_e_mensaje1\" disabled=\"true\" rows=\"4\" cols=\"50\" name=\"e_productos_mensaje\"/>$productos_config->e_productos_mensaje</textarea></td>";
			                			}
			                		echo "</tr>
			                	</table>
		                	</div>";

//Configuración visible sólo para administradores y coordinadores
if ($usuario_permitido_admin == true) {

                echo "	<!--Alertas por no calificación de productos-->
                			<div align=\"center\">
		                		<table id=\"Seccion2\" style='display:block'>
			                		<tr><br>";

			                				if ($productos_config->d_productos_umbral1 != null AND $productos_config->d_productos_umbral1 != 0) {
			            	    				echo "<td colspan=\"2\"><input name=\"d_productos_activo1\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox3(this)\" checked/>";
			                					echo "Umbral de alerta:</td>";
			                					echo "<td><input name=\"d_productos_umbral1\" type=\"number\" id=\"prod_d_umbral1\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"999\" title=\"Valor mínimo 1 - máximo 999\" value=\"$productos_config->d_productos_umbral1\"/></td>";
			                				}else{
			                					echo "<td colspan=\"2\"><input name=\"d_productos_activo1\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox3(this)\"/>";
			                					echo "Umbral de alerta:</td>";
			                					echo "<td><input name=\"d_productos_umbral1\" type=\"number\" id=\"prod_d_umbral1\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"999\" disabled=\"true\" title=\"Valor mínimo 1 - máximo 999\" value=\"$productos_config->d_productos_umbral1\"/></td>";
			                				}

			                				echo "
			                			<td>";

				                			
				                			echo "
			                			</td>
			                			<td rowspan=\"4\">
			                				<h4 align=\"center\">Docente: Calificación/retroalimentación</h4>
			                				<br>
			                				<p>Se enviará un mensaje personalizado a los docentes
			                				que no califiquen/retroalimenten la totalidad de productos
			                				luego de un <b>cantidad de días</b> (umbral alerta) y un 
			                				mensaje a la coordinación al cumplirse el umbral de alerta 
			                				crítico.</p>
			                				
			                			</td>

			                		</tr>
			                		<tr>";

			                				if ($productos_config->d_productos_umbral2 != null AND $productos_config->d_productos_umbral2 != 0) {
			            	    				echo "<td colspan=\"2\"><input name=\"d_productos_activo2\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox4(this)\" checked/>";
			                					echo "Umbral de alerta (crítico):</td>";
			                					echo "<td><input name=\"d_productos_umbral2\" type=\"number\" id=\"prod_d_umbral2\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"999\" title=\"Valor mínimo 1 - máximo 999\" value=\"$productos_config->d_productos_umbral2\"/></td>";
			                				}else{
			                					echo "<td colspan=\"2\"><input name=\"d_productos_activo2\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox4(this)\"/>";
			                					echo "Umbral de alerta (crítico):</td>";
			                					echo "<td><input name=\"d_productos_umbral2\" type=\"number\" id=\"prod_d_umbral2\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"999\" disabled=\"true\" title=\"Valor mínimo 1 - máximo 999\" value=\"$productos_config->d_productos_umbral2\"/></td>";
			                				}
			                				
			                				echo "
			                			<td>";
				                			
				                			echo "
			                			</td>
			                		</tr>
			                		<tr>
			                			<td>
			                				Mensaje personalizado:
			                			</td>
			                		</tr>
			                		<tr>";

			                			if ($productos_config->d_productos_umbral1 != null AND $productos_config->d_productos_umbral1 != 0) {
			                					echo "<td colspan=\"3\"><textarea id=\"prod_d_mensaje1\" rows=\"4\" cols=\"50\" name=\"d_productos_mensaje\"/>$productos_config->d_productos_mensaje</textarea></td>";
			                			}else{
			                					echo "<td colspan=\"3\"><textarea id=\"prod_d_mensaje1\" disabled=\"true\" rows=\"4\" cols=\"50\" name=\"d_productos_mensaje\"/>$productos_config->d_productos_mensaje</textarea></td>";
			                			}
			                			
			                		echo "</tr>
			                	</table>
		                	</div>";
}



/*
ALERTAS POR PRODUCTOS
*/

//Configuración visible sólo para administradores y coordinadores
/*
Biblioteca de configuración de alertas por productos no entregados (estudiante) y no calificados (docente)
*/

$default_ingresos_activo1 = false;
$default_ingresos_activo2 = false;
$default_productos_activo = false;

//Consultar valores definidos para el curso actual
	$query_config = "SELECT id as configid,
                        category,
                        e_ingresos_umbral1,
                        e_ingresos_umbral2,
                        e_ingresos_mensaje,
                        d_ingresos_umbral1,
                        d_ingresos_umbral2,
                        d_ingresos_mensaje
                FROM {seguimiento_config_ingresos} WHERE category = \"$asignatura->category\"";
                $files_data=$DB->get_records_sql($query_config,array('.','configid','0'));

                foreach ($files_data as $fd) {
                	$ingresos_config->category = $fd->category;
                	$ingresos_config->e_ingresos_umbral1 = $fd->e_ingresos_umbral1;
                	$ingresos_config->e_ingresos_umbral2 = $fd->e_ingresos_umbral2;
                	$ingresos_config->e_ingresos_mensaje = $fd->e_ingresos_mensaje;
                	$ingresos_config->d_ingresos_umbral1 = $fd->d_ingresos_umbral1;
                	$ingresos_config->d_ingresos_umbral2 = $fd->d_ingresos_umbral2;
                	$ingresos_config->d_ingresos_mensaje = $fd->d_ingresos_mensaje;

        		};

                

//ALERTAS CONFIGURADAS EN EL PROGRAMA: docente
                echo "	<!--Alertas por no ingreso a la paltaforma-->
                			<div align=\"center\">
		                		<table id=\"Seccion3\" style='display:none'>";

											//Si se encuentra por fuera de los cursos
							                if ($asignatura->category == 0) {
										        global $DB;
										        $query_course = "SELECT id AS id,
										                            name
										                    FROM {course_categories}";
										        $files_data_site=$DB->get_records_sql($query_course,array('.','course','0'));

										        echo "<tr><td colspan=\"5\"><select name=\"programa_seleccionado\">";

										        $i = 1;
										        

										        if ($usuario_permitido_admin == true) {
										            foreach ($files_data_site as $fd_site) {
										                $i++;
										                $data = new stdClass();
										                $data->id = $fd_site->id;
										                $data->name = $fd_site->name;
										                
										                echo "<option value=\"$data->id\">$data->name</option>";

										            };
										        }else{
										            //Consultar los cursos permitidos para el usuario actual
										            $query_role_assignments = "SELECT id AS id,
										                                    roleid,
										                                    contextid,
										                                    userid
										                            FROM {role_assignments} WHERE userid = $id_usuario";
										                $files_data=$DB->get_records_sql($query_role_assignments,array('.','contextid','0'));

										                foreach ($files_data as $fd) {
										                    //Obtener instanceid de la tabla "context"
										                    $query_context = "SELECT id AS id,
										                                    contextlevel,
										                                    instanceid
										                            FROM {context} WHERE contextlevel = 50 AND id = $fd->contextid"; 
										                    $files_data1=$DB->get_records_sql($query_context,array('.','context','0'));

										                    foreach ($files_data1 as $fd1) {
										                        $query_course = "SELECT id AS course_id,
										                        			category,
										                                    fullname,
										                                    shortname
										                            FROM {course} WHERE id = $fd1->instanceid"; 
										                        $files_data2=$DB->get_records_sql($query_course,array('.','course_id','0'));

										                        foreach ($files_data2 as $fd2) {
										                        	$query_course = "SELECT id AS id,
															                            name
															                    FROM {course_categories} WHERE $fd2->category";
															        $files_data_site=$DB->get_records_sql($query_course,array('.','id','0'));
															        foreach ($files_data_site as $fd_site) {
															        	echo "<option value=\"$fd_site->id\">$fd_site->name</option>";
															        }
										                            
										                        }
										                        
										                    }
										                            
										                };

										        }
										        
										                        
										        
										        echo"</select></td></tr>
										                <input type=\"hidden\" name=\"listado\" value=$listado/>";
										    }
										    //Fin: si se encuentra por fuera de los cursos

			    echo "         			<tr>";
			                						
			                				if ($ingresos_config->e_ingresos_umbral1 != null AND $ingresos_config->e_ingresos_umbral1 != 0) {
			            	    				echo "<td colspan=\"2\"><input name=\"e_ingresos_activo1\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox5(this)\" checked/>";
			                					echo "Umbral de alerta</td>";
			                					echo "<td><input name=\"e_ingresos_umbral1\" type=\"number\" id=\"ingr_e_umbral1\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"999\" title=\"Valor mínimo 1 - máximo 999 (días)\" value=\"$ingresos_config->e_ingresos_umbral1\"/></td>";
			                				}else{
			                					echo "<td colspan=\"2\"><input name=\"e_ingresos_activo1\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox5(this)\"/>";
			                					echo "Umbral de alerta</td>";
			                					echo "<td><input name=\"e_ingresos_umbral1\" type=\"number\" id=\"ingr_e_umbral1\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"999\" disabled=\"true\" title=\"Valor mínimo 1 - máximo 999 (días)\" value=\"$ingresos_config->e_ingresos_umbral1\"/></td>";
			                				}

			                				echo "
			                			<td>";

				                			
				                			echo "
			                			</td>
			                			<td rowspan=\"4\">
			                				<h4 align=\"center\">El estudiante no ha ingresado a plataforma:</h4>
			                				<br>
			                				<p>Se enviará un mensaje personalizado a los estudiantes
			                				que no ingresen a plataforma en una <b>cantidad de días</b>
			                				(umbral alerta) y un mensaje al docente al cumplirse el
			                				umbral de alerta crítico.</p>
			                				
			                			</td>

			                		</tr>
			                		<tr>";

			                				if ($ingresos_config->e_ingresos_umbral2 != null AND $ingresos_config->e_ingresos_umbral2 != 0) {
			            	    				echo "<td colspan=\"2\"><input name=\"e_ingresos_activo2\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox6(this)\" checked/>";
			                					echo "Umbral de alerta (crítico):</td>";
			                					echo "<td><input name=\"e_ingresos_umbral2\" type=\"number\" id=\"ingr_e_umbral2\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"999\" title=\"Valor mínimo 1 - máximo 999 (días)\" value=\"$ingresos_config->e_ingresos_umbral2\"/></td>";
			                				}else{
			                					echo "<td colspan=\"2\"><input name=\"e_ingresos_activo2\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox6(this)\"/>";
			                					echo "Umbral de alerta (crítico):</td>";
			                					echo "<td><input name=\"e_ingresos_umbral2\" type=\"number\" id=\"ingr_e_umbral2\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"999\" disabled=\"true\" title=\"Valor mínimo 1 - máximo 999 (días)\" value=\"$ingresos_config->e_ingresos_umbral2\"/></td>";
			                				}

			                				echo "
			                			<td>";
				                			
				                			echo "
			                			</td>
			                		</tr>
			                		<tr>
			                			<td>
			                				Mensaje personalizado:
			                			</td>
			                		</tr>
			                		<tr>";
			                			if ($ingresos_config->e_ingresos_umbral1 != null AND $ingresos_config->e_ingresos_umbral1 != 0) {
			                					echo "<td colspan=\"3\"><textarea id=\"ingr_e_mensaje1\" rows=\"4\" cols=\"50\" name=\"e_ingresos_mensaje\"/>$ingresos_config->e_ingresos_mensaje</textarea></td>";
			                			}else{
			                					echo "<td colspan=\"3\"><textarea id=\"ingr_e_mensaje1\" disabled=\"true\" rows=\"4\" cols=\"50\" name=\"e_ingresos_mensaje\"/>$ingresos_config->e_ingresos_mensaje</textarea></td>";
			                			}
			                		echo "</tr>
			                	</table>
		                	</div>
		                	";

if ($usuario_permitido_admin == true) {
//ALERTAS CONFIGURADAS EN EL PROGRAMA: admin
                echo "	<!--Alertas por no ingreso a la paltaforma-->
                			<div align=\"center\">
		                		<table id=\"Seccion4\" style='display:none'>
			                		<tr><br>";

			                				if ($ingresos_config->d_ingresos_umbral1 != null AND $ingresos_config->d_ingresos_umbral1 != 0) {
			            	    				echo "<td colspan=\"2\"><input name=\"d_ingresos_activo1\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox7(this)\" checked/>";
			                					echo "Umbral de alerta:</td>";
			                					echo "<td><input name=\"d_ingresos_umbral1\" type=\"number\" id=\"ingr_d_umbral1\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"999\" title=\"Valor mínimo 1 - máximo 999 (días)\" value=\"$ingresos_config->d_ingresos_umbral1\"/></td>";
			                				}else{
			                					echo "<td colspan=\"2\"><input name=\"d_ingresos_activo1\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox7(this)\"/>";
			                					echo "Umbral de alerta:</td>";
			                					echo "<td><input name=\"d_ingresos_umbral1\" type=\"number\" id=\"ingr_d_umbral1\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"999\" disabled=\"true\" title=\"Valor mínimo 1 - máximo 999 (días)\" value=\"$ingresos_config->d_ingresos_umbral1\"/></td>";
			                				}

			                				echo "
			                			<td>";

				                			
				                			echo "
			                			</td>
			                			<td rowspan=\"4\">
			                				<h4 align=\"center\">El docente no ha ingresado a plataforma:</h4>
			                				<br>
			                				<p>Se enviará un mensaje personalizado a los docentes
			                				que no ingresen a plataforma en una <b>cantidad de días</b>
			                				(umbral alerta) y un mensaje a la coordinación al
			                				cumplirse el umbral de alerta crítico.</p>
			                				
			                			</td>

			                		</tr>
			                		<tr>";

			                				if ($ingresos_config->d_ingresos_umbral2 != null AND $ingresos_config->d_ingresos_umbral2 != 0) {
			            	    				echo "<td colspan=\"2\"><input name=\"d_ingresos_activo2\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox8(this)\" checked/>";
			                					echo "Umbral de alerta (crítico):</td>";
			                					echo "<td><input name=\"d_ingresos_umbral2\" type=\"number\" id=\"ingr_d_umbral2\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"999\" title=\"Valor mínimo 1 - máximo 999 (días)\" value=\"$ingresos_config->d_ingresos_umbral2\"/></td>";
			                				}else{
			                					echo "<td colspan=\"2\"><input name=\"d_ingresos_activo2\" type=\"checkbox\" id=\"checkbox\" value=\"si\" onchange=\"checkbox8(this)\"/>";
			                					echo "Umbral de alerta (crítico):</td>";
			                					echo "<td><input name=\"d_ingresos_umbral2\" type=\"number\" id=\"ingr_d_umbral2\" size=\"4\" maxlength=\"4\" min=\"1\" max=\"999\" disabled=\"true\" title=\"Valor mínimo 1 - máximo 999 (días)\" value=\"$ingresos_config->d_ingresos_umbral2\"/></td>";
			                				}
			                				
			                				echo "
			                			<td>";
				                			
				                			echo "
			                			</td>
			                		</tr>
			                		<tr>
			                			<td>
			                				Mensaje personalizado:
			                			</td>
			                		</tr>
			                		<tr>";

			                			if ($ingresos_config->d_ingresos_umbral1 != null AND $ingresos_config->d_ingresos_umbral1 != 0) {
			                					echo "<td colspan=\"3\"><textarea id=\"ingr_d_mensaje1\" rows=\"4\" cols=\"50\" name=\"d_ingresos_mensaje\"/>$ingresos_config->d_ingresos_mensaje</textarea></td>";
			                			}else{
			                					echo "<td colspan=\"3\"><textarea id=\"ingr_d_mensaje1\" disabled=\"true\" rows=\"4\" cols=\"50\" name=\"d_ingresos_mensaje\"/>$ingresos_config->d_ingresos_mensaje</textarea></td>";
			                			}
			                			
			                		echo "</tr>
			                	</table><br>
		                	</div>";
}


		                echo"<table style=\"border:none\">
		                		<tr>
		                			<td align=\"center\">
		                				<input type=\"hidden\" value=\"true\" name=\"validador\"/>
		                				<button align=\"center\" type=\"button\" class=\"btn btn-primary\" onclick=\"submit()\">Aceptar</button>
                					</td>
                				</tr>
                			</table>
                		</form>
                	
                	</div>";


//Formulario oculto que enviará los datos al pdf de informe de alertas registradas
echo "";
include 'librerias/form_informe_alertas.php';
//fin

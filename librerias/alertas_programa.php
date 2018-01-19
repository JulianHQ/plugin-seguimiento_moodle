<?php

/*Alertas referentes al programa actual*/

//Consulta de tabla "seguimiento_config_ingresos" para conocer paramétros de envío de correos de los programas
$query_alertas_tempranas = "SELECT id AS alertas_tempranas_id,
                    category,
                    e_ingresos_umbral1,
                    e_ingresos_umbral2,
                    e_ingresos_mensaje,
                    d_ingresos_umbral1,
                    d_ingresos_umbral2,
                    d_ingresos_mensaje
                FROM {seguimiento_config_ingresos}";
    $files_data=$DB->get_records_sql($query_alertas_tempranas,array('.','alertas_tempranas_id','0'));


    foreach ($files_data as $fd) {
    	

    	//1. Alertas por no ingreso a plataforma por parte de los estudiantes
        if ($fd->e_ingresos_umbral1 != null || $fd->e_ingresos_umbral2 != null) {
        	$cursos_instanceid = array();
	    	$cursos_context = array();

	    	$usuario_id = array();
	    	$usuario_codigo = array();
	    	$usuario_firstname = array();
	    	$usuario_lastname = array();
	    	$usuario_email = array();
	    	$usuario_lastaccess = array();
	    	$usuario_rol = array();

	    	$email_admin = array();
	    	$email_docente = array();
	    	$alerta_docente1 = array();


        	# Consultar todos los usuarios que pertenecen al programa

        	//Consultar cursos que pertenecen al programa (categoría)
        	$query_curso_categoria = "SELECT id AS cursos_id,
        			category
        		FROM {course} WHERE category = $fd->category";
        	$files_data_curso_categoria=$DB->get_records_sql($query_curso_categoria,array('.','cursos_id','0'));

        	foreach ($files_data_curso_categoria as $fdcc) {
        		$cursos_instanceid[] = $fdcc->cursos_id;
        	}

        	for ($i=0; $i < count($cursos_instanceid); $i++) { 
        		$query_curso_categoria = "SELECT id AS cursos_id
	        		FROM {context} WHERE contextlevel = 50 AND instanceid = $cursos_instanceid[$i]";
	        	$files_data_curso_categoria=$DB->get_records_sql($query_curso_categoria,array('.','cursos_id','0'));

	        	foreach ($files_data_curso_categoria as $fdcc) {
	        		$cursos_context[] = $fdcc->cursos_id;
	        	}
        	}

        	//Consultar usuarios que pertenecen a los cursos del programa
        	for ($i=0; $i < count($cursos_context); $i++) { 
        		$query_role_assignments = "SELECT id AS id,
	                                roleid,
	                                contextid,
	                                userid
	                        FROM {role_assignments} WHERE contextid = $cursos_context[$i]";
	            $files_data1=$DB->get_records_sql($query_role_assignments,array('.','id','0'));

	            foreach ($files_data1 as $fd1) {
	                //datos del usuario
	                $query_user = "SELECT id AS user_id,
	                                idnumber as codigo,
	                                lastname,
	                                firstname,
	                                email,
	                                lastaccess
	                        FROM {user} WHERE id = $fd1->userid";

	                $files_data_user=$DB->get_records_sql($query_user,array('.','user_id','0'));

	                foreach ($files_data_user as $fd_user) {
	                    $usuario_id[] = $fd_user->user_id;
	                    $usuario_codigo[] = $fd_user->codigo;
	                    $usuario_firstname[] = $fd_user->firstname;
	                    $usuario_lastname[] = $fd_user->lastname;
	                    $usuario_email[] = $fd_user->email;
	                    $usuario_lastaccess[] = $fd_user->lastaccess;
	                    $usuario_rol[] = $fd1->roleid;
	                }
        		}
        	}

        		//Quitar usuarios repetidos
        		$usuario_id1 = array_unique($usuario_id);
				
				/*
		    	//Re-indexación del array
                $usuario_id = array_values($usuario_id1); 
                $usuario_codigo = array_values($usuario_codigo1); 
                $usuario_firstname = array_values($usuario_firstname1); 
                $usuario_lastname = array_values($usuario_lastname1); 
                $usuario_email = array_values($usuario_email1); 
                $usuario_lastaccess = array_values($usuario_lastaccess1);
                $usuario_rol = array_values($usuario_rol1); 
				*/

			#Comparar días de no ingreso con umbral de alerta
            for ($i=0; $i < count($usuario_id); $i++) { 
            	if (!empty($usuario_id1[$i])) {
            		if ($usuario_rol[$i] == 3 || $usuario_rol[$i] == 4) { //docente
            			$email_docente[] = $usuario_email[$i];
            			$id_docente = $usuario_id[$i];
            		}elseif($usuario_rol[$i] == 1){ // admin
            			$email_admin = $usuario_email[$i];
            			$id_admin = $usuario_id[$i];
            		}elseif ($usuario_rol[$i] == 5) { // estudiante
            			$dias = floor(abs(((mktime() - $usuario_lastaccess[$i])/86400)));

	                    //Si días es igual a umbral de alerta: enviar mensaje al estudiante
	                    if($fd->e_ingresos_umbral1 != null AND $fd->e_ingresos_umbral1 == $dias){
							
							$destino= $usuario_email[$i]; //usuario seleccionado
	                        $asunto = "Recordatorio de ingreso a plataforma - moodle"; //Asunto
	                        $mensaje= $fd->e_ingresos_mensaje;
	                        $mensaje.="\n\n\nEste mensaje ha sido enviado automáticamente por la plataforma moodle";
	                        $mensaje.= "\nPor favor no responda a este mensaje.";
	                        mail($destino,$asunto,$mensaje);                    	

	                        //Registrar alerta enviada
		                    $record = new object();
		                    $record->category = $fd->category;
		                    $record->course = null;
		                    $record->id_destino = $usuario_id[$i];
		                    $record->alerta_tipo = 2;
		                    $record->alerta_fecha = mktime();
		                    $record->alerta_mensaje = $mensaje;
		                    $DB->insert_record('seguimiento_registro', $record);

	                    }elseif($fd->e_ingresos_umbral2 != null AND $fd->e_ingresos_umbral2 == $dias){
	                    	$alerta = array();
	                    	$alerta[] = $usuario_codigo[$i];
	                    	$alerta[] = $usuario_firstname[$i];
	                    	$alerta[] = $usuario_lastname[$i];
	                    	$alerta[] = $usuario_email[$i];
	                    	$alerta_docente1[] = $alerta;
	                    }
            		}
                                        
            	}
            }

            #Enviar alerta a docente
            if ($alerta_docente1 != null) {
            	//Mensaje que se enviará al docente
            	for ($i=0; $i < count($email_admin); $i++) { 

            		$query_categoria = "SELECT id,
            					name
            				FROM {course_categories} WHERE id = $fd->category";
            		$files_data_categoria=$DB->get_records_sql($query_categoria,array('.','id','0'));

            		foreach ($files_data_categoria as $fdc) {
            			$category_name = $fdc->name;
            		}

            		$destino= $email_admin; //email del admin
	                $asunto = "Estudiantes que no han ingresado a plataforma - moodle"; //Asunto
	                $mensaje= "Estimado tutor, \nEste correo es enviado por el módulo de seguimiento de la plataforma moodle, en busqueda de la excelencia académica. \n\nA continuación le compartimos los datos de estudiantes del programa ". $category_name ." que no han ingresado en ". $fd->e_ingresos_umbral2 ." día(s): \n\n";
	                foreach ($alerta_docente1 as $ad1) {
	                    $mensaje.= "- " . $ad1[0] . ": " . $ad1[1] . " " . $ad1[2] . " (" . $ad1[3] . "). \n";
	                }

	                $mensaje.="\n\n\nEste mensaje ha sido enviado automáticamente por la plataforma moodle";
	                $mensaje.= "\nPor favor no responda a este mensaje.";
	                mail($destino,$asunto,$mensaje);

	                		//Registrar alerta enviada
		                    $record = new object();
		                    $record->category = $fd->category;
		                    $record->course = null;
		                    $record->id_destino = $id_admin;
		                    $record->alerta_tipo = 3;
		                    $record->alerta_fecha = mktime();
		                    $record->alerta_mensaje = $mensaje;
		                    $DB->insert_record('seguimiento_registro', $record);

            	}
                
            }
           
        }

        //2. Alertas por no ingreso a plataforma por parte de los docentes
        if ($fd->d_ingresos_umbral1 != null || $fd->d_ingresos_umbral2 != null) {
        	$cursos_instanceid = array();
	    	$cursos_context = array();

	    	$usuario_id = array();
	    	$usuario_codigo = array();
	    	$usuario_firstname = array();
	    	$usuario_lastname = array();
	    	$usuario_email = array();
	    	$usuario_lastaccess = array();
	    	$usuario_rol = array();

	    	$email_admin = array();
	    	$email_docente = array();
	    	$alerta_docente1 = array();

        	# Consultar todos los usuarios que pertenecen al programa

        	//Consultar cursos que pertenecen al programa (categoría)
        	$query_curso_categoria = "SELECT id AS cursos_id,
        			category
        		FROM {course} WHERE category = $fd->category";
        	$files_data_curso_categoria=$DB->get_records_sql($query_curso_categoria,array('.','cursos_id','0'));

        	foreach ($files_data_curso_categoria as $fdcc) {
        		$cursos_instanceid[] = $fdcc->cursos_id;
        	}

        	for ($i=0; $i < count($cursos_instanceid); $i++) { 
        		$query_curso_categoria = "SELECT id AS cursos_id
	        		FROM {context} WHERE contextlevel = 50 AND instanceid = $cursos_instanceid[$i]";
	        	$files_data_curso_categoria=$DB->get_records_sql($query_curso_categoria,array('.','cursos_id','0'));

	        	foreach ($files_data_curso_categoria as $fdcc) {
	        		$cursos_context[] = $fdcc->cursos_id;
	        	}
        	}

        	//Consultar usuarios que pertenecen a los cursos del programa
        	for ($i=0; $i < count($cursos_context); $i++) { 
        		$query_role_assignments = "SELECT id AS id,
	                                roleid,
	                                contextid,
	                                userid
	                        FROM {role_assignments} WHERE contextid = $cursos_context[$i]";
	            $files_data1=$DB->get_records_sql($query_role_assignments,array('.','id','0'));

	            foreach ($files_data1 as $fd1) {
	                //datos del usuario
	                $query_user = "SELECT id AS user_id,
	                                idnumber as codigo,
	                                lastname,
	                                firstname,
	                                email,
	                                lastaccess
	                        FROM {user} WHERE id = $fd1->userid";

	                $files_data_user=$DB->get_records_sql($query_user,array('.','user_id','0'));

	                foreach ($files_data_user as $fd_user) {
	                    $usuario_id[] = $fd_user->user_id;
	                    $usuario_codigo[] = $fd_user->codigo;
	                    $usuario_firstname[] = $fd_user->firstname;
	                    $usuario_lastname[] = $fd_user->lastname;
	                    $usuario_email[] = $fd_user->email;
	                    $usuario_lastaccess[] = $fd_user->lastaccess;
	                    $usuario_rol[] = $fd1->roleid;
	                }
        		}
        	}

        		//Quitar usuarios repetidos
        		$usuario_id1 = array_unique($usuario_id);
				
				/*
		    	//Re-indexación del array
                $usuario_id = array_values($usuario_id1); 
                $usuario_codigo = array_values($usuario_codigo1); 
                $usuario_firstname = array_values($usuario_firstname1); 
                $usuario_lastname = array_values($usuario_lastname1); 
                $usuario_email = array_values($usuario_email1); 
                $usuario_lastaccess = array_values($usuario_lastaccess1);
                $usuario_rol = array_values($usuario_rol1); 
				*/

			#Comparar días de no ingreso con umbral de alerta
            for ($i=0; $i < count($usuario_id); $i++) { 
            	if (!empty($usuario_id1[$i])) {
            		if ($usuario_rol[$i] == 1){ // admin
            			$email_admin = $usuario_email[$i];
            			$id_admin = $usuario_id[$i];
            		}elseif ($usuario_rol[$i] == 3 || $usuario_rol[$i] == 4) { // docente
            			$dias = floor(abs(((mktime() - $usuario_lastaccess[$i])/86400)));

	                    //Si días es igual a umbral de alerta: enviar mensaje al docente
	                    if($fd->d_ingresos_umbral1 != null AND $fd->d_ingresos_umbral1 == $dias){
							
							$destino= $usuario_email[$i]; //usuario seleccionado
	                        $asunto = "Recordatorio de ingreso a plataforma - moodle"; //Asunto
	                        $mensaje= $fd->d_ingresos_mensaje;
	                        $mensaje.="\n\n\nEste mensaje ha sido enviado automáticamente por la plataforma moodle";
	                        $mensaje.= "\nPor favor no responda a este mensaje.";
	                        mail($destino,$asunto,$mensaje);                    	

	                        //Registrar alerta enviada
		                    $record = new object();
		                    $record->category = $fd->category;
		                    $record->course = null;
		                    $record->id_destino = $usuario_id[$i];
		                    $record->alerta_tipo = 6;
		                    $record->alerta_fecha = mktime();
		                    $record->alerta_mensaje = $mensaje;
		                    $DB->insert_record('seguimiento_registro', $record);

	                    }elseif($fd->d_ingresos_umbral2 != null AND $fd->d_ingresos_umbral2 == $dias){
	                    	$alerta = array();
	                    	$alerta[] = $usuario_codigo[$i];
	                    	$alerta[] = $usuario_firstname[$i];
	                    	$alerta[] = $usuario_lastname[$i];
	                    	$alerta[] = $usuario_email[$i];
	                    	$alerta_docente1[] = $alerta;
	                    }
            		}
                                        
            	}
            }

            #Enviar alerta a admin (o coordinaciones)
            if ($alerta_docente1 != null) {
            	//Mensaje que se enviará al admin
            	for ($i=0; $i < count($email_admin); $i++) { 

            		$query_categoria = "SELECT id,
            					name
            				FROM {course_categories} WHERE id = $fd->category";
            		$files_data_categoria=$DB->get_records_sql($query_categoria,array('.','id','0'));

            		foreach ($files_data_categoria as $fdc) {
            			$category_name = $fdc->name;
            		}

            		$destino= $email_admin; //email del admin
	                $asunto = "Docentes que no han ingresado a plataforma - moodle"; //Asunto
	                $mensaje= "Estimado administrador, \nEste correo es enviado por el módulo de seguimiento de la plataforma moodle, en busqueda de la excelencia académica. \n\nA continuación le compartimos los datos de docentes del programa ". $category_name ." que no han ingresado en ". $fd->d_ingresos_umbral2 ." día(s): \n\n";
	                foreach ($alerta_docente1 as $ad1) {
	                    $mensaje.= "- " . $ad1[0] . ": " . $ad1[1] . " " . $ad1[2] . " (" . $ad1[3] . "). \n";
	                }

	                $mensaje.="\n\n\nEste mensaje ha sido enviado automáticamente por la plataforma moodle";
	                $mensaje.= "\nPor favor no responda a este mensaje.";
	                mail($destino,$asunto,$mensaje);

	                		//Registrar alerta enviada
		                    $record = new object();
		                    $record->category = $fd->category;
		                    $record->course = null;
		                    $record->id_destino = $id_admin;
		                    $record->alerta_tipo = 7;
		                    $record->alerta_fecha = mktime();
		                    $record->alerta_mensaje = $mensaje;
		                    $DB->insert_record('seguimiento_registro', $record);

            	}
                
            }	

        }
    }

?>
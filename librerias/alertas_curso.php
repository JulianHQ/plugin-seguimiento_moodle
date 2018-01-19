<?php

$no_envios = 0;
$email_docente = "mail_nulo";

/*Alertas referentes al curso actual*/

//Consulta de tabla "seguimiento_config_productos" para conocer paramétros de envío de correos de los cursos
$query_alertas_tempranas = "SELECT id AS alertas_tempranas_id,
                    course_context,
                    course_instance,
                    e_riesgo_umbral,
                    e_productos_umbral1,
                    e_productos_umbral2,
                    e_productos_mensaje,
                    d_productos_umbral1,
                    d_productos_umbral2,
                    d_productos_mensaje
                FROM {seguimiento_config_productos}";
    $files_data=$DB->get_records_sql($query_alertas_tempranas,array('.','alertas_tempranas_id','0'));


    foreach ($files_data as $fd) {

        //1. Alertas por no entrega de productos por parte de los estudiantes
        if ($fd->e_productos_umbral1 != null || $fd->e_productos_umbral2 != null) {
            # Consultar todos los productos que pertenecen a este curso
            $query_grade_items = "SELECT id AS productosid,
                    courseid,
                    itemname,
                    itemtype,
                    itemmodule,
                    iteminstance
                FROM {grade_items} WHERE itemtype != \"course\" AND itemmodule != \"seguimiento\" AND courseid = $fd->course_instance";
            $files_data_productos=$DB->get_records_sql($query_grade_items,array('.','productosid','0'));

            $productos_id = array();
            $productos_nombre = array();
            $productos_tipo = array();
            $producto_item_instance = array();
            $producto_courseid = array();

            foreach ($files_data_productos as $fd_productos) {
                $productos_id[] = $fd_productos->productosid;  
                $productos_nombre[] = $fd_productos->itemname; 
                $productos_tipo[] = $fd_productos->itemmodule;
                $producto_item_instance[] = $fd_productos->iteminstance;  
                $producto_courseid[] = $fd_productos->courseid;                
            };

            # Consultar todos los usuarios que están inscritos al curso
            $query_role_assignments = "SELECT id AS id,
                                roleid,
                                contextid,
                                userid
                        FROM {role_assignments} WHERE contextid = $fd->course_context";
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
                    $user_data->codigo = $fd_user->codigo;
                    $user_data->firstname = $fd_user->firstname;
                    $user_data->lastname = $fd_user->lastname;
                    $user_data->email = $fd_user->email;
                    $user_data->id = $fd_user->user_id;
                }

                //Si es docente: guardar la dirección de email
                if ($fd1->roleid == 3 || $fd1->roleid == 4) { //docente
                    $email_docente = $user_data->email;
                    $id_docente = $user_data->id;
                }elseif($fd1->roleid == 5){ //estudiante
                    $no_envios = 0;
                    for ($j=0; $j < count($productos_id); $j++) { 
                        $query_grade_grades = "SELECT id AS notas_id,
                                        rawgrade,
                                        aggregationstatus,
                                        itemid
                            FROM {grade_grades} WHERE itemid = $productos_id[$j] AND userid = $fd1->userid";
                        $files_data_grade=$DB->get_records_sql($query_grade_grades,array('.','notas_id','0'));
                                
                                
                            foreach ($files_data_grade as $fd_grade) {
                                if (($fd_grade->aggregationstatus) == "novalue") {
                                    //Consultar si es un foro, quiz, o assign
                                    if ($productos_tipo[$j] == "forum") {
                                        
                                        //Consultar si el foro fue enviado o no
                                        $query_forum_discussion_subs = "SELECT id as foros_id
                                            FROM {forum_discussion_subs} WHERE forum = $producto_item_instance[$j] AND userid = $fd1->userid";
                                        $files_data_alerta_forum=$DB->get_records_sql($query_forum_discussion_subs,array('.','foros_id','0'));
                                        if ($files_data_alerta_forum == null) {
                                            //Si no fue entregado mirar fecha limite
                                            $query_forum = "SELECT id as foros_id,
                                                        assesstimefinish
                                                    FROM {forum} WHERE id = $producto_item_instance[$j]";
                                                $files_data_alerta_forum1 = $DB->get_records_sql($query_forum, array('.','foros_id','0'));
                                                foreach ($files_data_alerta_forum1 as $fdaf1) {
                                                    if (mktime() > ($fdaf1->assesstimefinish) AND ($fdaf1->assesstimefinish) != 0) {
                                                        $no_envios += 1; 
                                                    };
                                                }
                                        };

                                    }elseif ($productos_tipo[$j] == "assign") {
                                        
                                        //Consultar si el assign fue enviado o no
                                        $query_assign_submission = "SELECT id as assign_id,
                                                                status
                                            FROM {assign_submission} WHERE assignment = $producto_item_instance[$j] AND userid = $fd1->userid";
                                        $files_data_alerta_assign=$DB->get_records_sql($query_assign_submission,array('.','assign_id','0'));
                                        if ($files_data_alerta_assign == null) {
                                            //Si no fue entregado mirar fecha limite (duedate: fecha de entrega, cutoffdate: fecha límite)
                                            $query_assign = "SELECT id as assign_id,
                                                        duedate,
                                                        cutoffdate
                                                    FROM {assign} WHERE id = $producto_item_instance[$j]";
                                                $files_data_alerta_assign1 = $DB->get_records_sql($query_assign, array('.','assign_id','0'));
                                                foreach ($files_data_alerta_assign1 as $fdaa1) {
                                                    if (($fdaa1->cutoffdate) == 0) {
                                                        if (mktime() > ($fdaa1->duedate) AND ($fdaa1->duedate) != 0) {
                                                            $no_envios += 1; 
                                                        };
                                                    }else{
                                                        if (mktime() > ($fdaa1->cutoffdate)) {
                                                            $no_envios += 1; 
                                                        };
                                                    }
                                                    
                                                } 
                                        };
                                        
                                    
                                    }elseif ($productos_tipo[$j] == "quiz") {

                                        //Consulta si el quiz fue enviado o no (quiz_attempts)
                                        $query_quiz_attempts = "SELECT id as quiz_id,
                                                                sumgrades
                                            FROM {quiz_attempts} WHERE quiz = $producto_item_instance[$j] AND userid = $fd1->userid";
                                        $files_data_alerta_quiz=$DB->get_records_sql($query_quiz_attempts,array('.','quiz_id','0'));
                                        if ($files_data_alerta_quiz == null) {
                                            //Si no fue entregado mirar fecha limite
                                            $query_quiz = "SELECT id as quiz_id,
                                                        timeclose
                                                    FROM {quiz} WHERE id = $producto_item_instance[$j]";
                                                $files_data_alerta_quiz1 = $DB->get_records_sql($query_quiz, array('.','quiz_id','0'));
                                                foreach ($files_data_alerta_quiz1 as $fdaq1) {
                                                    if (mktime() > ($fdaq1->timeclose) AND ($fdaq1->timeclose) != 0) {
                                                        $no_envios += 1; 
                                                    };
                                                }   
                                        };
                                    };
                                };
                            };

                    }

                        
                    //Validar si la cantidad de "no envios" es igual al umbral de alerta
                    if ($fd->e_productos_umbral1 != null AND $no_envios == $fd->e_productos_umbral1) {
                        //Obtener nombre del curso
                        $query_course = "SELECT id AS courseid,
                                        category,
                                        fullname,
                                        shortname
                                FROM {course} WHERE id = $fd->course_instance";
                        $files_data_course=$DB->get_records_sql($query_course,array('.','courseid','0'));

                        foreach ($files_data_course as $fd_course) {
                            $course_name = $fd_course->fullname;
                            $category = $fd_course->category;                    
                        };
                        
                        //Validar si la alerta ya fue enviada o no
                        $repetido = false;
                        $query_validation = "SELECT id,
                                            category,
                                            course,
                                            id_destino,
                                            alerta_tipo,
                                            alerta_fecha
                                        FROM {seguimiento_registro} WHERE alerta_tipo = 1";
                                    $files_data_validation = $DB->get_records_sql($query_validation, array('.','id','0'));
                                    foreach ($files_data_validation as $fdv) {
                                        if ($fdv->course == $fd->course_instance && $fdv->id_destino == $user_data->id && ($fdv->alerta_fecha + 864000)>mktime()) {
                                            $repetido = true;
                                        }
                                    }

                        if ($repetido == false) {
                            #Enviar correo ($fd->e_productos_mensaje) al estudiante ($user_data->email)
                            $destino= $user_data->email; //usuario seleccionado
                            $asunto = "Recordatorio de entrega de productos - moodle"; //Asunto
                            $mensaje= $fd->e_productos_mensaje;
                            $mensaje.="\n\n\nEste mensaje ha sido enviado automáticamente por la plataforma moodle.";
                            $mensaje.= "\nPor favor no responda a este mensaje.";
                            mail($destino,$asunto,$mensaje);

                            //Registrar alerta enviada
                            $record = new object();
                            $record->category = $category;
                            $record->course = $fd->course_instance;
                            $record->id_destino = $user_data->id;
                            $record->alerta_tipo = 1;
                            $record->alerta_fecha = mktime();
                            $record->alerta_mensaje = $mensaje;
                            $DB->insert_record('seguimiento_registro', $record);
                        }

                    }elseif ($no_envios == $fd->e_productos_umbral2) {
                        
                        //Agregar datos del estudiante a un array para posterior envío al docente
                        $datos_estudiante = array();
                        $datos_estudiante[] = $user_data->codigo;
                        $datos_estudiante[] = $user_data->firstname;
                        $datos_estudiante[] = $user_data->lastname;
                        $datos_estudiante[] = $user_data->email;
                        
                        $alerta_docente2[] = $datos_estudiante;

                    };
                }
            }

            //Envío de correo al docente de acuerdo al umbral de alerta crítico
            if ($fd->e_productos_umbral2 != null AND $alerta_docente2 != null) {
                //Obtener nombre del curso
                $query_course = "SELECT id AS courseid,
                                category,
                                fullname,
                                shortname
                        FROM {course} WHERE id = $fd->course_instance";
                $files_data_course=$DB->get_records_sql($query_course,array('.','courseid','0'));

                foreach ($files_data_course as $fd_course) {
                    $course_name = $fd_course->fullname;
                    $category = $fd_course->category;                    
                };

                //Validar si la alerta ya fue enviada o no
                $repetido = false;
                $query_validation = "SELECT id,
                                    category,
                                    course,
                                    id_destino,
                                    alerta_tipo,
                                    alerta_fecha
                                FROM {seguimiento_registro} WHERE alerta_tipo = 5";
                            $files_data_validation = $DB->get_records_sql($query_validation, array('.','id','0'));
                            foreach ($files_data_validation as $fdv) {
                                if ($fdv->course == $fd->course_instance && $fdv->id_destino == $id_docente && ($fdv->alerta_fecha + 864000)>mktime()) {
                                    $repetido = true;
                                }
                            }

                if ($repetido == false) {
                    #Enviar correo automático al docente ($email_docente)
                    //Mensaje que se enviará al docente
                    $destino= $email_docente; //email del docente
                    $asunto = "Estudiantes que no han entregado productos - moodle"; //Asunto
                    $mensaje= "Estimado tutor, \nEste correo es enviado por el módulo de seguimiento de la plataforma moodle, en busqueda de la excelencia académica. \n\nA continuación le compartimos los datos de estudiantes del curso ". $course_name ." que no han entregado ". $fd->e_productos_umbral2 ." productos: \n\n";
                    foreach ($alerta_docente2 as $ad) {
                        $mensaje.= "- " . $ad[1] . " " . $ad[2] . " (" . $ad[3] . "). \n";
                    }

                    $mensaje.="\n\n\nEste mensaje ha sido enviado automáticamente por la plataforma moodle.";
                    $mensaje.= "\nPor favor no responda a este mensaje.";
                    mail($destino,$asunto,$mensaje);

                    //Registrar alerta enviada
                    $record = new object();
                    $record->category = $category;
                    $record->course = $fd->course_instance;
                    $record->id_destino = $id_docente;
                    $record->alerta_tipo = 5;
                    $record->alerta_fecha = mktime();
                    $record->alerta_mensaje = $mensaje;
                    $DB->insert_record('seguimiento_registro', $record);
                }
            }

        } //fin if $fd->e_productos_umbral1 != null || $fd->e_productos_umbral2 != null


        //2. Alertas por no calificación/retroalimentación de productos por parte de los docentes
        if ($fd->d_productos_umbral1 != null || $fd->d_productos_umbral2 != null) {
            
            $alerta_docente = array();
            $alerta_docente2 = array();

            # Consultar todos los productos que pertenecen a este curso
            $query_grade_items = "SELECT id AS productosid,
                    courseid,
                    itemname,
                    itemtype,
                    itemmodule,
                    iteminstance
                FROM {grade_items} WHERE itemtype != \"course\" AND itemmodule != \"seguimiento\" AND courseid = $fd->course_instance";
            $files_data_productos=$DB->get_records_sql($query_grade_items,array('.','productosid','0'));

            $productos_id = array();
            $productos_nombre = array();
            $productos_tipo = array();
            $producto_item_instance = array();
            $producto_courseid = array();

            foreach ($files_data_productos as $fd_productos) {
                $productos_id[] = $fd_productos->productosid;  
                $productos_nombre[] = $fd_productos->itemname; 
                $productos_tipo[] = $fd_productos->itemmodule;
                $producto_item_instance[] = $fd_productos->iteminstance;  
                $producto_courseid[] = $fd_productos->courseid;                
            };

            # Consultar todos los usuarios que están inscritos al curso (estudiantes, docentes y administradores)
            $query_role_assignments = "SELECT id AS id,
                                roleid,
                                contextid,
                                userid
                        FROM {role_assignments} WHERE contextid = $fd->course_context";
            $files_data_ra=$DB->get_records_sql($query_role_assignments,array('.','id','0'));

            $codigo_user = array();
            $firstname_user = array();
            $lastname_user = array();
            $email_user = array();
            $lastaccess_user = array();
            $rol_user = array();
            $id_user = array();

            foreach ($files_data_ra as $fdra) {
                //Obtener los datos de cada usuario encontrado
                $query_user = "SELECT id AS user_id,
                                idnumber as codigo,
                                lastname,
                                firstname,
                                email,
                                lastaccess
                        FROM {user} WHERE id = $fdra->userid";

                $files_data2=$DB->get_records_sql($query_user,array('.','user_id','0'));

                foreach ($files_data2 as $fd2) {
                    $codigo_user[] = $fd2->codigo;
                    $firstname_user[] = $fd2->firstname;
                    $lastname_user[] = $fd2->lastname;
                    $email_user[] = $fd2->email;
                    $lastaccess_user[] = $fd2->lastaccess;
                    $rol_user[] = $fdra->roleid;
                    $id_user[] = $fd2->user_id;
                }
            }

            /*Para cada producto se revisan las entregas de todos los estudiantes, si fue entregado y no 
            calificado y/o retroalimentado luego del umbral de alerta: 
            se envía un recordatorio al docente, si alcanza el umbral crítico de días: se envía una alerta
            al administrador.*/
            for ($j=0; $j < count($productos_id); $j++) { 
                $activar_alerta = false;
                $cantidad_calificados = 0;
                $cantidad_entregados = 0;
                $cantidad_retroalimentados = 0;

                for ($k=0; $k < count($rol_user); $k++) { 
                    //Si es administrador: guardar la dirección de email
                    if ($rol_user[$k] == 1) { //administrador
                        $email_administrador = $email_user[$k];
                        $id_administrador = $id_user[$k];
                    }elseif($rol_user[$k] == 3 || $rol_user[$k] == 4){ //docente
                        $email_docente = $email_user[$k];
                        $id_docente = $id_user[$k];
                    }elseif($rol_user[$k] == 5){ //Estudiante

                        //Determinar si se debe enviar alerta al docente o no
                        $query_grade_grades = "SELECT id AS notas_id,
                                rawgrade,
                                aggregationstatus,
                                itemid,
                                feedback
                            FROM {grade_grades} WHERE itemid = $productos_id[$j] AND userid = $id_user[$k]";

                        $files_data_notas=$DB->get_records_sql($query_grade_grades,array('.','notas_id','0'));

                        foreach ($files_data_notas as $fd_notas) {
                            if ($fd_notas->aggregationstatus == "used") {
                                $cantidad_calificados += 1;
                                $cantidad_entregados += 1;
                                if ($fd_notas->feedback != null) {
                                    $cantidad_retroalimentados += 1;
                                }
                            }elseif ($fd_notas->aggregationstatus == "novalue") {
                                #Consultar si es un foro, quiz, o assign
                                if ($productos_tipo[$j] == "forum") {
                                    # Consultar si el foro fue enviado o no
                                    $query_forum_discussion_subs = "SELECT id AS foros_id
                                        FROM {forum_discussion_subs} WHERE forum = $producto_item_instance[$j] AND userid = $id_user[$k]";
                                    $files_data_foro=$DB->get_records_sql($query_forum_discussion_subs,array('.','foros_id','0'));
                                    if ($files_data_foro != null) {
                                        $cantidad_entregados += 1;
                                        # Si si fue entregado: mirar fecha limite de calificación
                                        $query_forum = "SELECT id AS foros_id,
                                                assesstimefinish
                                            FROM {forum} WHERE id = $producto_item_instance[$j]";
                                        $files_data_alerta_forum1=$DB->get_records_sql($query_forum,array('.','foros_id','0'));
                                        foreach ($files_data_alerta_forum1 as $fdaf1) {
                                            if (mktime() > ($fdaf1->assesstimefinish) AND ($fdaf1->assesstimefinish)!=0) { //fecha limite mas 8 días
                                                $activar_alerta = true;
                                                $tiempo = floor(abs(((mktime() - $fdaf1->assesstimefinish)/86400)));
                                                # code...
                                            }
                                        }
                                    }
                                }elseif ($productos_tipo[$j] == "assign") {
                                    # Consultar si el assign fue enviado o no
                                    $query_assign_submission = "SELECT id AS assign_id,
                                            status
                                        FROM {assign_submission} WHERE assignment = $producto_item_instance[$j] AND userid = $id_user[$k]";
                                    $files_data_assign=$DB->get_records_sql($query_assign_submission,array('.','assign_id','0'));
                                    if ($files_data_assign != null) {
                                        $cantidad_entregados += 1;
                                        # Si si fue entregado: mirar fecha límite (duedate: fecha de entrega, cutoffdate: fecha límite)
                                        $query_assign = "SELECT id AS assign_id,
                                                duedate,
                                                cutoffdate
                                            FROM {assign} WHERE id = $producto_item_instance[$j]";
                                        $files_data_alerta_assign1=$DB->get_records_sql($query_assign, array('.','assign_id','0'));
                                        foreach ($files_data_alerta_assign1 as $fdaa1) {
                                            if (($fdaa1->cutoffdate)==0) {
                                                if (mktime()>(($fdaa1->duedate)) AND ($fdaa1->duedate) != 0) { //fecha de entrega mas 8 días
                                                    $activar_alerta = true;
                                                    $tiempo = floor(abs(((mktime() - $fdaa1->duedate)/86400)));
                                                }
                                            }else{
                                                if (mktime() > (($fdaa1->cutoffdate))) { //fecha limite mas 8 días
                                                    $activar_alerta = true; 
                                                    $tiempo = floor(abs(((mktime() - $fdaa1->cutoffdate)/86400)));
                                                }
                                            }
                                        }
                                    }
                                }elseif ($productos_tipo[$j] == "quiz") {
                                    # Consulta si el quiz fue enviado o no (quiz_attempts)
                                    $query_quiz_attempts = "SELECT id,
                                            sumgrades
                                        FROM {quiz_attempts} WHERE quiz = $producto_item_instance[$j] AND userid = $id_user[$k]";
                                    $files_data_quiz=$DB->get_records_sql($query_quiz_attempts,array('.','quiz_id','0'));
                                    if ($files_data_quiz != null) {
                                        $cantidad_entregados += 1;
                                        # Si si fue entregado mirar fecha limite
                                        $query_quiz = "SELECT id as quiz_id,
                                                timeclose
                                            FROM {quiz} WHERE id = $producto_item_instance[$j]";
                                        $files_data_alerta_quiz1 = $DB->get_records_sql($query_quiz, array('.','quiz_id','0'));
                                        foreach ($files_data_alerta_quiz1 as $fdaq1) {
                                            if (mktime() > (($fdaq1->timeclose )) AND ($fdaq1->timeclose) != 0) {
                                                $activar_alerta = true; 
                                                $tiempo = floor(abs(((mktime() - $fdaq1->timeclose)/86400)));
                                            }
                                        }  
                                    }
                                }
                            }
                        }

                    }
                }

                //Calcular porcentaje de calificados y retroalimentados
                $p_calificados = ($cantidad_calificados * 100) / $cantidad_entregados;
                $p_retroalimentados = ($cantidad_retroalimentados * 100) / $cantidad_entregados;

                //Si se activó la alerta y calificados o retroalimentados no están en 100%
                if ($activar_alerta == true AND ($p_calificados != 100 || $p_retroalimentados != 100)) {
                    if ($tiempo == ($fd->d_productos_umbral1 + 8)) {
                        // Agregar datos del producto a un array para posterior envío al docente
                        $datos_producto = array();
                        $datos_producto[] = $productos_nombre[$j];
                        $datos_producto[] = $p_calificados;
                        $datos_producto[] = $p_retroalimentados;

                        $alerta_docente[] = $datos_producto;
                    }elseif ($tiempo == ($fd->d_productos_umbral2 + 8)) {
                        // Agregar datos del producto a un array para posterior envío al administrador
                        $datos_producto = array();
                        $datos_producto[] = $productos_nombre[$j];
                        $datos_producto[] = $p_calificados;
                        $datos_producto[] = $p_retroalimentados;
                        
                        $alerta_docente2[] = $datos_producto; 
                    }
                }

            }

            if ($fd->d_productos_umbral1 != null AND $alerta_docente != null) {
                # Enviar correo al docente con el listado de productos por calificar/retroalimentar
                
                //Obtener nombre del curso
                $query_course = "SELECT id AS courseid,
                                category,
                                fullname,
                                shortname
                        FROM {course} WHERE id = $fd->course_instance";
                $files_data_course=$DB->get_records_sql($query_course,array('.','courseid','0'));

                foreach ($files_data_course as $fd_course) {
                    $course_name = $fd_course->fullname;  
                    $category = $fd_course->category;                   
                };

                //Validar si la alerta ya fue enviada o no
                $repetido = false;
                $query_validation = "SELECT id,
                                    category,
                                    course,
                                    id_destino,
                                    alerta_tipo,
                                    alerta_fecha
                                FROM {seguimiento_registro} WHERE alerta_tipo = 8";
                            $files_data_validation = $DB->get_records_sql($query_validation, array('.','id','0'));
                            foreach ($files_data_validation as $fdv) {
                                if ($fdv->course == $fd->course_instance && $fdv->id_destino == $id_docente && ($fdv->alerta_fecha + 864000)>mktime()) {
                                    $repetido = true;
                                }
                            }

                if ($repetido == false) {
                    //Mensaje que se enviará al docente:
                    $destino = $email_docente; //email del docente
                    $asunto = "Productos no calificados - moodle"; //Asunto
                    $mensaje = $fd->d_productos_mensaje . "\n";
                    $mensaje.= "A continuación, un listado de los productos para el curso ". $course_name .", que no han sido calificados o retroalimentados al 100% luego de ". $fd->d_productos_umbral1 ." días de la fecha máxima para calificación: \n\n";
                    foreach ($alerta_docente as $ad) {
                        $mensaje.= "- " . $ad[0] . " (" . $ad[1] . "% calificado y " . $ad[2] . "% retroalimentado). \n";
                    }

                    $mensaje.="\n\n\nEste mensaje ha sido enviado automáticamente por la plataforma moodle.";
                    $mensaje.= "\nPor favor no responda a este mensaje.";
                    mail($destino,$asunto,$mensaje);

                    //Registrar alerta enviada
                    $record = new object();
                    $record->category = $category;
                    $record->course = $fd->course_instance;
                    $record->id_destino = $id_docente;
                    $record->alerta_tipo = 8;
                    $record->alerta_fecha = mktime();
                    $record->alerta_mensaje = $mensaje;
                    $DB->insert_record('seguimiento_registro', $record);
                }
            }

            if ($fd->d_productos_umbral2 != null AND $alerta_docente2 != null) {
                # Enviar correo al admin del curso con el listado de productos por calificar/retroalimentar

                //Obtener nombre del curso
                $query_course = "SELECT id AS courseid,
                                category,
                                fullname,
                                shortname
                        FROM {course} WHERE id = $fd->course_instance";
                $files_data_course=$DB->get_records_sql($query_course,array('.','courseid','0'));

                foreach ($files_data_course as $fd_course) {
                    $course_name = $fd_course->fullname;   
                    $category = $fd_course->category;                 
                };

                //Validar si la alerta ya fue enviada o no
                $repetido = false;
                $query_validation = "SELECT id,
                                    category,
                                    course,
                                    id_destino,
                                    alerta_tipo,
                                    alerta_fecha
                                FROM {seguimiento_registro} WHERE alerta_tipo = 9";
                            $files_data_validation = $DB->get_records_sql($query_validation, array('.','id','0'));
                            foreach ($files_data_validation as $fdv) {
                                if ($fdv->course == $fd->course_instance && $fdv->id_destino == $id_administrador && ($fdv->alerta_fecha + 864000)>mktime()) {
                                    $repetido = true;
                                }
                            }

                if ($repetido == false) {
                    //Mensaje que se enviará al admin:
                    $destino = $email_administrador; //email del admin
                    $asunto = "Productos no calificados - moodle"; //Asunto
                    $mensaje = "Estimado administrador moodle, \nEste correo es enviado por el módulo de seguimiento de la plataforma moodle, en busqueda de la excelencia académica. \n\n";
                    $mensaje.= "A continuación, un listado de los productos para el curso ". $course_name .", que no han sido calificados o retroalimentados al 100% luego de ". $fd->d_productos_umbral2 ." días de la fecha máxima para calificación: \n\n";
                    foreach ($alerta_docente2 as $ad) {
                        $mensaje.= "- " . $ad[0] . " (" . $ad[1] . "% calificado y " . $ad[2] . "% retroalimentado). \n";
                    }

                    $mensaje.="\n\n\nEste mensaje ha sido enviado automáticamente por la plataforma moodle.";
                    $mensaje.= "\nPor favor no responda a este mensaje.";
                    mail($destino,$asunto,$mensaje);

                        //Registrar alerta enviada
                        $record = new object();
                        $record->category = $category;
                        $record->course = $fd->course_instance;
                        $record->id_destino = $id_administrador;
                        $record->alerta_tipo = 9;
                        $record->alerta_fecha = mktime();
                        $record->alerta_mensaje = $mensaje;
                        $DB->insert_record('seguimiento_registro', $record);
                }
            }

        }


        //3. Alertas a estudiantes en riesgo académico luego de un porcentaje del curso calificado
        if ($fd->e_riesgo_umbral != null) {
            $alerta_docente = array();
            # Consultar todos los productos que pertenecen a este curso
            $query_grade_items = "SELECT id AS productosid,
                    courseid,
                    itemname,
                    itemtype,
                    itemmodule,
                    iteminstance,
                    aggregationcoef2
                FROM {grade_items} WHERE itemtype != \"course\" AND itemmodule != \"seguimiento\" AND courseid = $fd->course_instance";
            $files_data_productos=$DB->get_records_sql($query_grade_items,array('.','productosid','0'));

            $productos_id = array();
            $productos_nombre = array();
            $productos_tipo = array();
            $producto_item_instance = array();
            $producto_courseid = array();
            $producto_aggregationcoef2 = array();

            foreach ($files_data_productos as $fd_productos) {
                $productos_id[] = $fd_productos->productosid;  
                $productos_nombre[] = $fd_productos->itemname; 
                $productos_tipo[] = $fd_productos->itemmodule;
                $producto_item_instance[] = $fd_productos->iteminstance;  
                $producto_courseid[] = $fd_productos->courseid; 
                $producto_aggregationcoef2[] = $fd_productos->aggregationcoef2;               
            };

            # Consultar todos los usuarios que están inscritos al curso
            $query_role_assignments = "SELECT id AS id,
                                roleid,
                                contextid,
                                userid
                        FROM {role_assignments} WHERE contextid = $fd->course_context";
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
                    $user_data->codigo = $fd_user->codigo;
                    $user_data->firstname = $fd_user->firstname;
                    $user_data->lastname = $fd_user->lastname;
                    $user_data->email = $fd_user->email;
                    $user_data->id = $fd_user->user_id;
                }

                //Por cada usuario, consultar si ya tiene calificado el porcentaje de la nota (umbral) y obtener notas
                //Si es docente: guardar la dirección de email
                if ($fd1->roleid == 3 || $fd1->roleid == 4) { //docente
                    $email_docente = $user_data->email;
                    $id_docente = $user_data->id;
                }elseif($fd1->roleid == 5){ //estudiante
                    $nota = 0;
                    $nota_minima = 0;
                    $cantidad_notas = 0;
                    $porcentaje = 0;

                    for ($j=0; $j < count($productos_id); $j++) { 
                        $query_grade_grades = "SELECT id AS notas_id,
                                        rawgrade,
                                        finalgrade,
                                        aggregationstatus,
                                        itemid
                            FROM {grade_grades} WHERE itemid = $productos_id[$j] AND userid = $fd1->userid";
                            $files_data_grade=$DB->get_records_sql($query_grade_grades,array('.','notas_id','0'));
                                    
                            foreach ($files_data_grade as $fd_grade) {
                                if (($fd_grade->finalgrade) != null) {
                                    $nota = $nota + ($fd_grade->finalgrade * $producto_aggregationcoef2[$j]);
                                    $nota_minima = $nota_minima + (60 * $producto_aggregationcoef2[$j]);
                                    $cantidad_notas += 1;
                                    $porcentaje = $porcentaje + $producto_aggregationcoef2[$j];
                                }
                            }
                    }

                    //Comparar porcentaje contra umbral de riesgo
                    $porcentaje = $porcentaje * 100;
                    if ($nota < $nota_minima AND $porcentaje != 0 AND $porcentaje >= $fd->e_riesgo_umbral) {
                        //Obtener nombre del curso
                        $query_course = "SELECT id AS courseid,
                                        category,
                                        fullname,
                                        shortname
                                FROM {course} WHERE id = $fd->course_instance";
                        $files_data_course=$DB->get_records_sql($query_course,array('.','courseid','0'));

                        foreach ($files_data_course as $fd_course) {
                            $course_name = $fd_course->fullname;
                            $category = $fd_course->category;                    
                        };

                //Validar si la alerta ya fue enviada o no
                $repetido = false;
                $query_validation = "SELECT id,
                                    category,
                                    course,
                                    id_destino,
                                    alerta_tipo,
                                    alerta_fecha
                                FROM {seguimiento_registro} WHERE alerta_tipo = 4";
                            $files_data_validation = $DB->get_records_sql($query_validation, array('.','id','0'));
                            foreach ($files_data_validation as $fdv) {
                                if ($fdv->course == $fd->course_instance && $fdv->id_destino == $user_data->id && ($fdv->alerta_fecha + 864000)>mktime()) {
                                    $repetido = true;
                                }
                            }

                if ($repetido == false) {
                        #Enviar mensaje al estudiante y añadir a lista para enviar mensaje al docente
                        $destino= $user_data->email; //usuario seleccionado
                        $asunto = "Alerta de bajas calificaciones - Moodle"; //Asunto
                        $mensaje = "Estimado estudiante,";
                        $mensaje.="\n\nHa sido calificado el " . $porcentaje . "% del curso " . $course_name . " y ha obtenido bajas calificaciones, por lo cual lo invitamos a seguir mejorando y así evitar ver afectado el promedio académico de su semestre.";
                        $mensaje.="\n\n\nEste mensaje ha sido enviado automáticamente por la plataforma moodle.";
                        $mensaje.= "\nPor favor no responda a este mensaje.";
                        mail($destino,$asunto,$mensaje);

                        //Registrar alerta enviada
                        $record = new object();
                        $record->category = $category;
                        $record->course = $fd->course_instance;
                        $record->id_destino = $user_data->id;
                        $record->alerta_tipo = 4;
                        $record->alerta_fecha = mktime();
                        $record->alerta_mensaje = $mensaje;
                        $DB->insert_record('seguimiento_registro', $record);

                        //Almacenar para posterior envío al docente
                        $temp = array();
                        $temp[] = $user_data->firstname;
                        $temp[] = $user_data->lastname;
                        $temp[] = $user_data->email;

                        $alerta_docente[] = $temp;
                }
                    }

                }
            }

            # Enviar correo al docente
            if ($alerta_docente != null) {

                $destino= $email_docente; //usuario seleccionado
                $asunto = "Alerta de bajas calificaciones - Moodle"; //Asunto
                $mensaje= "Estimado tutor,";
                $mensaje.="\n\nA continuación un listado de estudiantes en riesgo académico, del curso " . $course_name . ".\n";
                
                foreach ($alerta_docente as $ad) {
                    $mensaje.= "- " . $ad[0] . " " . $ad[1] . " (" . $ad[2] . "). \n";
                }

                $mensaje.="\n\n\nEste mensaje ha sido enviado automáticamente por la plataforma moodle.";
                $mensaje.= "\nPor favor no responda a este mensaje.";
                mail($destino,$asunto,$mensaje);

                //Registrar alerta enviada
                $record = new object();
                $record->category = $category;
                $record->course = $fd->course_instance;
                $record->id_destino = $id_docente;
                $record->alerta_tipo = 4;
                $record->alerta_fecha = mktime();
                $record->alerta_mensaje = $mensaje;
                $DB->insert_record('seguimiento_registro', $record);
            }
        }

    }


        
?>
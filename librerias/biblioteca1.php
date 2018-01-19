<?php


//Consulta de las asignaturas y selección de una
//Consulta del listado de cursos
        $asignatura_seleccionada = $_POST['asignatura_seleccionada'];
        $tipo_informe_seleccionado = $_POST['tipo_informe'];
        $filtro_estudiantes_1 = $_POST['filtro_estudiantes_1'];
            $filtro_estudiantes_1_tipo = $_POST['filtro_estudiantes_1_tipo'];
            $filtro_estudiantes_1_valor = $_POST['filtro_estudiantes_1_valor'];
        $filtro_estudiantes_2 = $_POST['filtro_estudiantes_2'];
        $filtro_estudiantes_3 = $_POST['filtro_estudiantes_3'];
        $filtro_estudiantes_4 = $_POST['filtro_estudiantes_4'];
        $filtro_estudiantes_5 = $_POST['filtro_estudiantes_5'];
        //$listado = array();
        //$asignatura_consultada = $_POST['asignatura_consultada'];
        //$user = $DB->get_record_sql('SELECT username FROM {user} WHERE id=?', array("id"=>$consultar));
        
        global $DB;
        $query_course = "SELECT id AS id,
                            fullname,
                            shortname
                    FROM {course} WHERE format != \"site\"";
        $files_data=$DB->get_records_sql($query_course,array('.','course','0'));

        echo "<form name=\"lista_asignaturas\" method=\"post\" action=\"$link\">";
                
        if ($usuario_permitido_admin == true) {
            echo "<input type=\"radio\" name=\"tipo_informe\" value=\"inf_estudiantes\" checked> Informe de estudiantes (<a href=\"#ventana\">Más opciones</a>) <br>                
                <input type=\"radio\" name=\"tipo_informe\" value=\"inf_docentes\"> Informe de docentes<br><br>";
            
        }else{
            echo "(<a href=\"#ventana\">Más opciones</a>) </br>";
        };

            //listado de filtros
                echo "<section id=\"ventana\" align=\"center\">
                        <section id=\"contenido\" align=\"left\">
                            <!--<a id=\"salir\" type=\"button\" class=\"btn btn-primary\" href=\"#closed\">X</a>-->
                            <h2>Seleccione las opciones de consulta</h2>
                            <input type=\"checkbox\" name=\"filtro_estudiantes_1\" value=\"filtro_estudiantes_1\"> Estudiantes con notas: 
                                <select name=\"filtro_estudiantes_1_tipo\">
                                    <option value=\"menor\">Menor que</option>
                                    <option value=\"mayor\">Mayor que</option>
                                    <option value=\"igual\">igual que</option>
                                    <option value=\"entre\">entre</option>
                                </select>
                                    <input type=\"text\" name=\"filtro_estudiantes_1_valor\" pattern=\"[0-9-]{1,7}\" title=\"Para la opción 'entre' utilice el formato #-# Ej: 10-30\"/>
                                    <img src=\"css/img/iconoinfo.png\" width=\"25px\" height=\"25px\" title=\"Para la opción 'entre' utilice el formato #-# Ej: 10-30\"/></br><br>
                            <input type=\"checkbox\" name=\"filtro_estudiantes_2\" value=\"filtro_estudiantes_2\"> Estudiantes que no han entregado todos los productos<br>
                            <input type=\"checkbox\" name=\"filtro_estudiantes_3\" value=\"true\"> Estudiantes que han entregado todos los productos<br><br>
                            <input type=\"checkbox\" name=\"filtro_estudiantes_4\" value=\"filtro_estudiantes_4\"> Estudiantes sin todas las calificaciones<br>
                            <input type=\"checkbox\" name=\"filtro_estudiantes_5\" value=\"filtro_estudiantes_5\"> Estudiantes con todas las calificaciones<br><br>
                            <a type=\"button\" class=\"btn btn-primary\" href=\"#close\" align=\"center\">Aceptar</a>
                        </section>
                    </section>";
            //fin listado de filtros

        echo "<select name=\"asignatura_seleccionada\">";

        $i = 1;
        

        if ($usuario_permitido_admin == true) {
            foreach ($files_data as $fd) {
                $i++;
                $data = new stdClass();
                $data->id = $fd->id;
                $data->fullname = $fd->fullname;
                $data->shortname = $fd->shortname;
                
                echo "<option value=\"$i\">$data->shortname</option>";

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
                            echo "<option value=\"$fd2->course_id\">$fd2->shortname</option>";
                        }
                        
                    }
                            
                };

        }
        
                        
        
        echo"</select><button type=\"button\" class=\"btn btn-primary\" onclick=\"submit()\">Consultar</button>
                <input type=\"hidden\" name=\"listado\" value=$listado/>
                <!--<input name=\"enviar\" type=\"button\" value=\"Enviar\" onclick=\"submit()\"/>-->
        </form>";


?>
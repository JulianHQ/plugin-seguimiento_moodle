<script type="text/javascript">
    function imprSelec(area){
        var ficha = document.getElementById(area);
        var ventimp = window.open(' ','popimpr');
        ventimp.document.write(ficha.innerHTML);
        ventimp.document.close();
        ventimp.print();ventimp.close();
    }

    function checkbox_mail(checkbox){
        var str1 = "todo_mail";
        for (i = 1; i < 100; i++) { 
            var str2 = str1.concat(i);
            var campo = document.getElementById(str2);
            if (checkbox.checked == true) {
                campo.checked = true;
                document.getElementById('mail_docente').checked=true;
            }else if(checkbox.checked == false){
                campo.checked = false;
                document.getElementById('mail_docente').checked=false;
            };  
        }
        
    }

    function enviar_mail(){
        var listado_mails = new Array();
        var str1 = "todo_mail";
        var email_r = "<?php echo $USER->email;?>";

        for (var i = 1; i < 20; i++) {
            var str2 = str1.concat(i);
            var campo = document.getElementById(str2);
            if (campo != null) {
                if (campo.checked) {
                    listado_mails.push(campo.value);
                };
            };
        };
        if (document.getElementById('mail_docente').checked) {
                listado_mails.push(document.getElementById('mail_docente').value)
        };

        javascript:window.open("email.php?email_d="+listado_mails+"&email_r="+email_r,'','width=1000, height=480, toolbar=no, scrollbars=no, resizable=no, top=20, left=200')
    }

    function mostrar_filtros(){
        document.getElementById('listfil').click();
    }

    function mostrar_graficos(){
        document.getElementById('listgraf1').click();
        document.getElementById('listgraf2').click();
    }

    function bloqueo_filtro1(checkbox){
        if (checkbox.checked == false) {
            document.getElementById('dato_filtro1a').disabled = true;
            document.getElementById('dato_filtro1b').disabled = true;
        }else if(checkbox.checked == true){
            document.getElementById('dato_filtro1a').disabled = false;
            document.getElementById('dato_filtro1b').disabled = false;
        }; 
    }

    function bloqueo_filtro2(checkbox){
        if (checkbox.checked == false) {
            document.getElementById('dato_filtro2').disabled = true;
        }else if(checkbox.checked == true){
            document.getElementById('dato_filtro2').disabled = false;
        }; 
    }

    function bloqueo_filtro3(checkbox){
        if (checkbox.checked == false) {
            document.getElementById('dato_filtro3').disabled = true;
        }else if(checkbox.checked == true){
            document.getElementById('dato_filtro3').disabled = false;
        }; 
    }
</script>

<?php
    $indice = 1;
	if ($asignatura->category == 0) {
		$validador = false;
		$asignatura_seleccionada = $_POST['asignatura_seleccionada'];
		//Listado de cursos
		global $DB;
        $query_course = "SELECT id AS id,
                            fullname,
                            shortname
                    FROM {course} WHERE format != \"site\"";
        $files_data=$DB->get_records_sql($query_course,array('.','course','0'));

        echo "<form name=\"lista_asignaturas\" method=\"post\" action=\"$link\">
        		<select name=\"asignatura_seleccionada\">";
 
            foreach ($files_data as $fd) {
                $data = new stdClass();
                $data->id = $fd->id;
                $data->fullname = $fd->fullname;
                $data->shortname = $fd->shortname;
                
                echo "<option value=\"$data->id\">$data->fullname</option>";
            }
        echo 	"</select><button type=\"button\" class=\"btn btn-primary\" onclick=\"submit()\">Consultar</button>
        	  </form>";



        if ($asignatura_seleccionada != null) {
        	$asignatura->instanceid = $asignatura_seleccionada;
        	$validador = true;
        	$query_context = "SELECT id AS id,
                            contextlevel,
                            instanceid
                    FROM {context} WHERE contextlevel = 50 AND instanceid = $asignatura_seleccionada";
            $files_data1=$DB->get_records_sql($query_context,array('.','context','0'));

            foreach ($files_data1 as $fd1) {
            	$asignatura->id = $fd1->id;
                $asignatura->instanceid = $fd->instanceid; 
            }
        }else{
        	echo "Por favor seleccione la asignatura a consultar";
        }
	}

    
    //Ver estadística del curso
    echo "<button style=\"float:right\" target=\"_blank\" onclick=\"mostrar_graficos()\"><img src=\"css/img/presentation.png\" width=\"20\" height=\"20\" alt=\"Ver estadística del curso\"/> Estadística</button>";
    
    //Enviar correos
    echo "<button style=\"float:right\" target=\"_blank\" onclick=\"enviar_mail()\"><img src=\"css/img/message.png\" width=\"20\" height=\"20\" alt=\"Envíar mensaje\"/> Enviar correo</button> ";

    //Ver lista de filtros
    echo "<button style=\"float:right\" target=\"_blank\" onclick=\"mostrar_filtros()\"><img src=\"css/img/filter.png\" width=\"20\" height=\"20\" alt=\"Ver opciones de filtrado\"/> Filtrar</button><br><br><br><br>";


	if ($asignatura->category != 0 || $validador == true){
        //recibir datos de filtros
        $filtro1 = $_POST['filtro_estudiantes_1_tipo'];        
        $filtro1_valor = $_POST['filtro1_valor'];
        $filtro2_valor = $_POST['filtro2_valor'];
        $filtro3_valor = $_POST['filtro3_valor'];

		//Abrir ventana emergente de filtros
		/*echo "(<a href=\"#ventana\">Más opciones</a>) </br>";*/
        echo "<div id=\"area\">";
        //Listado de filtros
            echo "<details>
                <summary id=\"listfil\" style=\"display: none\">FILTRAR RESULTADOS</summary>
                    <form name=\"listado_filtros\" method=\"post\" action=\"$link\">
                        <table align=\"center\">
                        <thead>
                            <td colspan=\"3\" align=\"center\"><h4><img src=\"css/img/filter.png\" width=\"20\" height=\"20\" alt=\"Ver opciones de filtrado\"/> Opciones de filtrado</h4><br></td>
                        </thead>
                        <tr>
                            <td><input type=\"checkbox\" onclick=\"bloqueo_filtro1(this)\" ";
                                if($filtro1=="menor" || $filtro1=="mayor" || $filtro1=="igual" || $filtro1=="entre"){
                                    echo "checked";
                                } 
                                echo ">Por nota:</td>
                            <td><select id=\"dato_filtro1a\" ";
                                if($filtro1!="menor" && $filtro1!="mayor" && $filtro1!="igual" && $filtro1!="entre"){
                                    echo "disabled=\"true\"";
                                }
                                echo "name=\"filtro_estudiantes_1_tipo\">
                                        <option value=\"menor\" ";
                                if ($filtro1 == "menor") {
                                    echo "selected";
                                }
                                echo ">Menor que</option>
                                        <option value=\"mayor\" ";
                                if ($filtro1 == "mayor") {
                                    echo "selected";
                                }
                                echo ">Mayor que</option>
                                        <option value=\"igual\" ";
                                if ($filtro1 == "igual") {
                                    echo "selected";
                                }
                                echo ">igual que</option>
                                        <option value=\"entre\" ";
                                if ($filtro1 == "entre") {
                                    echo "selected";
                                }
                                echo ">entre</option>
                                    </select></td>
                            <td><input id=\"dato_filtro1b\" ";
                                if($filtro1!="menor" && $filtro1!="mayor" && $filtro1!="igual" && $filtro1!="entre"){
                                    echo "disabled=\"true\"";
                                }
                                echo " type=\"text\" name=\"filtro1_valor\" value=\"$filtro1_valor\" pattern=\"[0-9-]{1,7}\" title=\"Para la opción 'entre' utilice el formato #-# Ej: 10-30\"/><td>
                             <td><img src=\"css/img/iconoinfo.png\" width=\"25px\" height=\"25px\" title=\"Para la opción 'entre' utilice el formato #-# Ej: 10-30\"/></br><br></td>
                        </tr>
                        <tr>
                            <td><input type=\"checkbox\" onclick=\"bloqueo_filtro2(this)\" ";
                                if($filtro2_valor != null){
                                    echo "checked";
                                } 
                                echo ">Productos entregados:</td>
                            <td></td>
                            <td><input type=\"number\" id=\"dato_filtro2\"";
                                if($filtro2_valor == null){
                                    echo "disabled=\"true\"";
                                }
                                echo "name=\"filtro2_valor\" value=\"$filtro2_valor\"/></td>
                        </tr>
                        <tr>
                            <td><input type=\"checkbox\" onclick=\"bloqueo_filtro3(this)\" ";
                                if($filtro3_valor != null){
                                    echo "checked";
                                } 
                                echo ">Productos calificados:</td>
                            <td></td>
                            <td><input type=\"number\" id=\"dato_filtro3\" ";
                                if($filtro3_valor == null){
                                    echo "disabled=\"true\"";
                                }
                                echo "name=\"filtro3_valor\" value=\"$filtro3_valor\"/></td>
                        </tr>
                        <tr>
                            <td align=\"center\" colspan=\"3\"><button type=\"button\" class=\"\" onclick=\"submit()\">Consultar</button></td>
                        </tr>
                        </table>
                    </form>
                    </details></br>";
   	

		//Consulta role_assignments: Obtenemos el id de "estudiantes(roleid = 5)" de determinado curso
        $query_role_assignments = "SELECT id AS id,
                            roleid,
                            contextid,
                            userid
                    FROM {role_assignments} WHERE roleid = 5 AND contextid = $asignatura->id";
        $files_data=$DB->get_records_sql($query_role_assignments,array('.','contextid','0'));

        $i = 0;
        $student_id = array();
        foreach ($files_data as $fd) {
	        $i++;
	        $data_student = new stdClass();
	        $data_student->id = $fd->id;
	        $data_student->userid = $fd->userid;

	        $student_id[] = $fd->userid;
	            
	    }

	    //Consulta grade_items: Productos que pertenecen a determinado curso (el curso seleccionado)
        $query_grade_items = "SELECT id AS productosid,
                        itemname,
                        itemmodule,
                        iteminstance,
                        courseid
                    FROM {grade_items} WHERE courseid = $asignatura->instanceid AND itemtype != \"course\" AND itemmodule != \"seguimiento\"";
        $files_data=$DB->get_records_sql($query_grade_items,array('.','productosid','0'));


        $productos_id = array();
        $productos_nombre = array();
        $productos_tipo = array();
        $producto_item_instance = array();
        $producto_courseid = array();
        $productos_fecha = array();
        foreach ($files_data as $fd) {
                $productos_id[] = $fd->productosid;  
                $productos_nombre[] = $fd->itemname; 
                $productos_tipo[] = $fd->itemmodule;
                $producto_item_instance[] = $fd->iteminstance;  
                $producto_courseid[] = $fd->courseid;                
        }

        //Consulta grade_grades: Notas de los productos seleccionados (de acuerdo al estudiante)
        $tamano_productos = count($productos_id); 
        $tamano_estudiantes = count($student_id);

        $encabezado_productos = array();
        $encabezado_productos[] = "<input type=\"checkbox\" name=\"mail\" onchange=\"checkbox_mail(this)\">";
        $encabezado_productos[] = "Estudiante";

        //Consulta de datos de los estudiantes y notas
        $listado = array();
        $codigos_usuario = array();
        $nombre_usuario = array();
        $listado_mails = array();

        for ($i=0; $i < $tamano_estudiantes; $i++) { 
            $almacen = array();
            $query = "SELECT id AS id,
                                idnumber as codigo,
                                lastname,
                                firstname,
                                phone1,
                                email
                        FROM {user} WHERE id = $student_id[$i]";
            $files_data=$DB->get_records_sql($query,array('.','user','0'));

            foreach ($files_data as $fd) {
                $data = new stdClass();
                //$almacen[] = $fd->codigo;
                $indice1 = "todo_mail".$indice;
                $email_d = $fd->email;
                $almacen[] = "<input type=\"checkbox\" name=\"mail\" value=\"$email_d\" id=".$indice1.">";
                $listado_mails[] = $email_d;
                $indice = $indice + 1;
                $url = new moodle_url('/user/profile.php?id=');
                $url_id = "=" . $fd->id;
                $almacen[] = "<a href=\"".$url . $url_id ."\" target=\"_blank\">".$fd->firstname . " " . $fd->lastname . "</a> (" . $fd->codigo . ")" . "<br>" . $fd->phone1 . "<br>" . $fd->email;
                //$almacen[] = $fd->firstname;
                //$almacen[] = $fd->phone1;
                //$almacen[] = $fd->email;
                
                $email_r = $USER->email;
                $phone = $fd->phone1;
                $codigos_usuario[] = $fd->codigo;
                $nombre_usuario[] = $fd->firstname . " " . $fd->lastname;

                    
                    for ($j=0; $j < $tamano_productos; $j++) { 
                        $query_grade_grades = "SELECT id AS notas_id,
                                            rawgrade,
                                            finalgrade,
                                            aggregationstatus,
                                            itemid
                                FROM {grade_grades} WHERE itemid = $productos_id[$j] AND userid = $student_id[$i]";
                                $files_data=$DB->get_records_sql($query_grade_grades,array('.','notas_id','0'));
                                
                                if ($files_data == null) {
                                    $almacen[] = "-";  
                                };
                                
                                foreach ($files_data as $fd) {
                                    $data->notas_id = $fd->notas_id;
                                    if ($fd->aggregationstatus == "used" && $fd->finalgrade != null) {
                                        $almacen[] = round($fd->finalgrade,2); 
                                    }elseif ($fd->aggregationstatus == "novalue" || $fd->finalgrade == null) {
                                        //Consultamos si es un foro, quiz, o assign
                                        if ($productos_tipo[$j] == "forum") {
                                            //Consulta si el foro fue enviado o no
                                            $query_forum_discussion_subs = "SELECT id
                                                FROM {forum_discussion_subs} WHERE forum = $producto_item_instance[$j] AND userid = $student_id[$i]";
                                            $files_data1=$DB->get_records_sql($query_forum_discussion_subs,array('.','foros_id','0'));
                                            if ($files_data1 == null) {
                                                $almacen[] = "-";  
                                            }else{
                                                $almacen[] = "no calificado";
                                            };
                                        }elseif ($productos_tipo[$j] == "assign") {
                                            //Consulta si el assign fue enviado o no
                                            $query_assign_submission = "SELECT id,
                                                                    status
                                                FROM {assign_submission} WHERE assignment = $producto_item_instance[$j] AND userid = $student_id[$i]";
                                            $files_data1=$DB->get_records_sql($query_assign_submission,array('.','assign_id','0'));
                                            if ($files_data1 == null) {
                                                $almacen[] = "-";  
                                            }else{
                                                $almacen[] = "no calificado";
                                            };
                                        }elseif ($productos_tipo[$j] == "quiz") {
                                            //Consulta si el quiz fue enviado o no (quiz_attempts)
                                            $query_quiz_attempts = "SELECT id,
                                                                    sumgrades
                                                FROM {quiz_attempts} WHERE quiz = $producto_item_instance[$j] AND userid = $student_id[$i]";
                                            $files_data1=$DB->get_records_sql($query_quiz_attempts,array('.','quiz_id','0'));
                                            if ($files_data1 == null) {
                                                $almacen[] = "-";  
                                            }else{
                                                $almacen[] = "no calificado";
                                            };
                                            //$almacen[] = "/";
                                        }
                                    }else{
                                        $almacen[] = "error";
                                    };
                                };


                    	//Obtener fecha de entrega de los productos
                        if ($productos_tipo[$j] == "forum") {
                        	$query_forum = "SELECT id AS foros_id,
                                    assesstimefinish
                                FROM {forum} WHERE id = $producto_item_instance[$j]";
                            $files_data_alerta_forum1=$DB->get_records_sql($query_forum,array('.','foros_id','0'));
                            foreach ($files_data_alerta_forum1 as $fdaf1) {
                                $fecha = date('d/m/Y', $fdaf1->assesstimefinish);
                                if ($fecha == "01/01/1970") {
                                	$fecha = "No registra";
                                }
                            }
                        }elseif ($productos_tipo[$j] == "assign"){
                        	$query_assign = "SELECT id AS assign_id,
                                    duedate,
                                    cutoffdate
                                FROM {assign} WHERE id = $producto_item_instance[$j]";
                            $files_data_alerta_assign1=$DB->get_records_sql($query_assign, array('.','assign_id','0'));
                            foreach ($files_data_alerta_assign1 as $fdaa1) {
                                if (($fdaa1->cutoffdate)==0) {
                                    $fecha = date('d/m/Y', $fdaa1->duedate);
                                }else{
                                    $fecha = date('d/m/Y', $fdaa1->cutoffdate);
                                }
                                if ($fecha == "01/01/1970") {
                                	$fecha = "No registra";
                                }
                            }
                        }elseif ($productos_tipo[$j] == "quiz") {
                        	$query_quiz = "SELECT id as quiz_id,
                                    timeclose
                                FROM {quiz} WHERE id = $producto_item_instance[$j]";
                            $files_data_alerta_quiz1 = $DB->get_records_sql($query_quiz, array('.','quiz_id','0'));
                            foreach ($files_data_alerta_quiz1 as $fdaq1) {
                                $fecha = date('d/m/Y', $fdaq1->timeclose);
                                if ($fecha == "01/01/1970") {
                                	$fecha = "No registra";
                                }
                            }
                        }

                        $productos_fecha[] = $fecha;

                    }
                $almacen[] = "<a target=\"_blank\" href=\"javascript:window.open('email.php?email_d=$email_d&email_r=$email_r','','width=1000, height=480, toolbar=no, scrollbars=no, resizable=no, top=20, left=200')\"><img src=\"css/img/message.png\" alt=\"Envíar mensaje\"/><a/>";    
                
                $listado[] = $almacen;


            };



        }

        
        //Calculo de filtros

        $tamano_listado = count($listado);
        
        //Filtro 1: Estudiantes con notas específicas (menor, mayor, igual, entre, ...)
        switch ($filtro1) {
            case 'menor':
                for ($i=0; $i<$tamano_listado; $i++) { 
                    $contador = false;
                    for ($j=0; $j <$tamano_productos ; $j++) { 
                        if ($listado[$i][2+$j] != "-" and $listado[$i][2+$j] != "no calificado" and $listado[$i][2+$j] > intval($filtro1_valor)){
                            $contador = true;
                        }
                    }
                    if ($contador == true) {
                        unset($listado[$i]);
                        unset($codigos_usuario[$i]);
                        unset($nombre_usuario[$i]);
                        unset($productos_fecha[$i]);
                    }
                };
                break;
            case 'mayor':
                for ($i=0; $i<$tamano_listado; $i++) { 
                    $contador = false;
                    for ($j=0; $j <$tamano_productos ; $j++) { 
                        
                        if ($listado[$i][2+$j] != "-" and $listado[$i][2+$j] != "no calificado" and $listado[$i][2+$j] < intval($filtro1_valor)){
                            $contador = true;
                        }
                    }
                    if ($contador == true) {
                        unset($listado[$i]);
                        unset($codigos_usuario[$i]);
                        unset($nombre_usuario[$i]);
                        unset($productos_fecha[$i]);
                    }
                };
                break;
            case 'igual':
                for ($i=0; $i<$tamano_listado; $i++) { 
                    $contador = false;
                    for ($j=0; $j <$tamano_productos ; $j++) { 
                        
                        if ($listado[$i][2+$j] != "-" and $listado[$i][2+$j] != "no calificado" and $listado[$i][2+$j] == intval($filtro1_valor)){
                            $contador = true;
                        }
                    }
                    if ($contador != true) {
                        unset($listado[$i]);
                        unset($codigos_usuario[$i]);
                        unset($nombre_usuario[$i]);
                        unset($productos_fecha[$i]);
                    }
                };
                break;
            case 'entre':
                $corte = strpos($filtro1_valor, "-");
                $valor1 = substr($filtro1_valor, 0, $corte);
                $valor2 = substr($filtro1_valor, $corte+1);
                if ($valor1>$valor2) {
                    for ($i=0; $i<$tamano_listado; $i++) { 
                        $contador = false;
                        for ($j=0; $j <$tamano_productos ; $j++) { 
                            
                            if ($listado[$i][2+$j] != "-" and $listado[$i][2+$j] != "no calificado" and $listado[$i][2+$j] > $valor2 and $listado[$i][2+$j] < $valor1){
                                $contador = true;
                            }
                        }
                        if ($contador != true) {
                            unset($listado[$i]);
                            unset($codigos_usuario[$i]);
                        	unset($nombre_usuario[$i]);
                        	unset($productos_fecha[$i]);
                        }
                    };
                }else{
                    for ($i=0; $i<$tamano_listado; $i++) { 
                        $contador = false;
                        for ($j=0; $j <$tamano_productos ; $j++) { 
                            
                            if ($listado[$i][2+$j] != "-" and $listado[$i][2+$j] != "no calificado" and $listado[$i][2+$j] > $valor1 and $listado[$i][2+$j] < $valor2){
                                $contador = true;
                            }
                        }
                        if ($contador != true) {
                            unset($listado[$i]);
                            unset($codigos_usuario[$i]);
                        	unset($nombre_usuario[$i]);
                        	unset($productos_fecha[$i]);
                        }
                    };
                };
                break;
            
            default:
                break;
        }
        
        //Filtro 2: Cantidad de productos entregados
        if ($filtro2_valor == null) {
            //no hacer nada
        }else{
            for ($i=0; $i<$tamano_listado; $i++) { 
                $entregado = 0;
                for ($j=0; $j <$tamano_productos ; $j++) { 
                    if ($listado[$i][2+$j] != "-"){
                        $entregado += 1;
                    }
                }
                if ($entregado != $filtro2_valor) {
                    unset($listado[$i]);
                    unset($codigos_usuario[$i]);
                    unset($nombre_usuario[$i]);
                    unset($productos_fecha[$i]);
                }
            };
        }

        //Filtro 3: Cantidad de productos calificados
        if ($filtro3_valor == null) {
            //no hacer nada
        }else{
            for ($i=0; $i<$tamano_listado; $i++) { 
                $calificado = 0;
                for ($j=0; $j <$tamano_productos ; $j++) { 
                    if ($listado[$i][2+$j] != "no calificado" && $listado[$i][2+$j] != "-"){
                        $calificado += 1;
                    }
                }
                if ($calificado != $filtro3_valor) {
                    unset($listado[$i]);
                    unset($codigos_usuario[$i]);
                    unset($nombre_usuario[$i]);
                    unset($productos_fecha[$i]);
                }
            };
        }



        //Impresión del listado		
        $listado=array_values($listado); //Re-indexación del array $listado
        $codigos_usuario=array_values($codigos_usuario);
        $nombre_usuario=array_values($nombre_usuario);
        $productos_fecha=array_values($productos_fecha);
        $tamano_listado = count($listado);

        for ($k=0; $k < $tamano_productos; $k++) { 
            $encabezado_productos[] = "<span style=\"font-size:14px\">".$productos_nombre[$k] . "</span><br><span style=\"font-size:13px\">" . $productos_fecha[$k]."</span>";
        };

        $table = new html_table();
        $table->width = '80%';
$table->align = array( 'left','left','center');
$table->valign = array( 'top','top','top');
        $table->head = $encabezado_productos;
$table->size = array( '0%','150%');
        for ($i=0; $i<$tamano_listado; $i++) { 
            $table->data[] = $listado[$i];
        };

        //echo html_writer::table($table);

        //Tabla de prueba con datos de estudiantes
        echo "<details open><summary style=\"cursor: pointer\"><img src=\"css/img/student.png\" width=\"20\" height=\"20\" alt=\"Ver resumen de estudiantes\"/> Resumen de estudiantes</summary><br>";
        echo "<table style=\"width:100%\" class=\"table-hover table-striped table-bordered \">
                <thead>
                    <tr>";
                    echo "<th align=\"right\">". $encabezado_productos[0] ."</th>";
                    for ($i=1; $i <count($encabezado_productos) ; $i++) { 
                        echo "<th style=\"vertical-align:top\">". $encabezado_productos[$i] ."</th>";
                    }
                    echo"
                    </tr>
                </thead>
                <tbody>";

                    foreach ($listado as $lt) { 
                        echo "<tr>";
                            echo "<td align=\"right\">". $lt[0] ."</td>";
                            echo "<td style=\"text-align:left; font-size:14px\">". $lt[1] ."</td>";
                        for ($j=2; $j <count($encabezado_productos) ; $j++) { 
                            echo "<td style=\"text-align:center; font-size:16px\">". $lt[$j] ."</td>";
                        }
                        echo "</tr>";
                    }
                echo "</tbody>
                </table><br>";
        echo "</details>";
            


        //GRÁFICAS
        /*Espacio para añadir gráficas estadísticas del listado consultado
                    para INFORME DE ESTUDIANTES*/

        echo "<details>
                <summary id=\"listgraf1\" style=\"display: none\">FILTRAR RESULTADOS</summary>";
	        //1. ¿Cuándo fue el último ingreso de mis estudiantes?

	        $hoy = 0;
	        $ayer = 0;
	        $dos_dias = 0;
	        $tres_dias = 0;
	        $cuatro_dias = 0;
	        $cinco_mas = 0;
	        for ($k=0; $k <count($codigos_usuario); $k++) {
	            $query_user = "SELECT id AS userid,
	                            username,
	                            idnumber,
	                            firstname,
	                            lastname,
	                            lastaccess
	                        FROM {user} WHERE idnumber = '$codigos_usuario[$k]' AND idnumber != ''";
	            $files_data=$DB->get_records_sql($query_user,array('.','userid','0'));

	            foreach ($files_data as $fd) {
	                $ultimo_acceso = floor(abs(((mktime() - $fd->lastaccess)/86400)));

	                switch (intval($ultimo_acceso)) {
	                    case 0:
	                        $hoy += 1;
	                        break;
	                    case 1:
	                        $ayer += 1;
	                        break;
	                    case 2:
	                        $dos_dias += 1;
	                        break;
	                    case 3:
	                        $tres_dias += 1;
	                        break;
	                    case 4:
	                        $cuatro_dias += 1;
	                        break; 
	                    default:
	                        $cinco_mas += 1;
	                        break;
	                }

	                                
	            }
	        }

	        echo "
            <!--Load the AJAX API-->
	                <script type=\"text/javascript\" src=\"https://www.gstatic.com/charts/loader.js\"></script>
	                <script type=\"text/javascript\">

	                  // Load the Visualization API and the corechart package.
	                  google.charts.load('current', {'packages':['corechart']});

	                  // Set a callback to run when the Google Visualization API is loaded.
	                  google.charts.setOnLoadCallback(drawChart);

	                  // Callback that creates and populates a data table,
	                  // instantiates the pie chart, passes in the data and
	                  // draws it.
	                  function drawChart() {

	                    // Create the data table.
	                    var data = new google.visualization.DataTable({
                            legend:{position:'bottom', alignment:'start'}
                        });
	                    data.addColumn('string', 'Topping');
	                    data.addColumn('number', 'Slices');
	                    data.addRows([";
	                      

	                echo "['Hoy',". $hoy ."],
	                      ['Ayer',". $ayer ."],
	                      ['Hace 2 días',". $dos_dias ."],
	                      ['Hace 3 días',". $tres_dias ."],
	                      ['Hace 4 días',". $cuatro_dias ."],
	                      ['5 o más días',". $cinco_mas ."]
	                    ]);

	                    // Set chart options
	                    var options = {'legend':{position:'left', alignment:'start'},
                                       'title':'ÚLTIMO INGRESO DE LOS ESTUDIANTES',
	                                   'width':700,
	                                   'height':400,
                                       'is3D': true,
                                       slices: {
                                        0: { color: '#0099C6' },
                                        1: { color: '#109618' },
                                        2: { color: '#F1CA3A' },
                                        3: { color: '#FF9900' },
                                        4: { color: '#DC3912' },
                                        5: { color: '#990099' }
                                      }};

	                    // Instantiate and draw our chart, passing in some options.
	                    var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
	                    


                        chart.draw(data, options);
	                  }



    google.visualization.events.addListener(chart, 'ready', function () {
      my_div.innerHTML = '<img src=\"' + chart.getImageURI() + '\">';
    });

    chart.draw(data);
	                </script>

	                <!--Div that will hold the pie chart-->
	                <table style=\"width:100%\" class=\"table-bordered \"> <tr>
                    <td colspan=\"\"><div id=\"chart_div\"></div></td>
                    <td style=\"width: 40%; height:90\"><p style=\"background-color:#fff\"><ul><li>Hoy: $hoy</li><li>Ayer: $ayer</li><li>Hace 2 días: $dos_dias</li><li>Hace 3 días: $tres_dias</li><li>Hace 4 días: $cuatro_dias</li><li>5 o más días: $cinco_mas</li></ul></p>
                    </td>
                    </tr></table><br>";

                
            //2. Productos: todos, entregados, calificados
            $datos_grafica = array();
                $ec = 0; //Entregados y calificados
                $enc = 0; //Entregados y no calificados
                $ne = 0; //No entregados
            for ($i=0; $i <$tamano_productos; $i++) { 
                $calificados = 0;
                $sin_calificar = 0;
                $no_entregados = 0;

                foreach ($listado as $ld) {
                    switch ($ld[2+$i]) {
                        case 'no calificado':
                            $sin_calificar += 1;
                            $enc += 1;
                            break;
                        case '-':
                            $no_entregados += 1;
                            $ne += 1;
                            break;
                        default:
                            $calificados += 1;
                            $ec += 1;
                            break;
                    }
                }
                $datos_grafica[$i][0] = $calificados;
                $datos_grafica[$i][1] = $sin_calificar;
                $datos_grafica[$i][2] = $no_entregados;
            }

            echo "
            
            <script type=\"text/javascript\" src=\"https://www.gstatic.com/charts/loader.js\"></script>
            <script type=\"text/javascript\">

              // Load the Visualization API and the corechart package.
              google.charts.load('current', {'packages':['corechart']});

              // Set a callback to run when the Google Visualization API is loaded.
              google.charts.setOnLoadCallback(drawChart);

              // Callback that creates and populates a data table,
              // instantiates the pie chart, passes in the data and
              // draws it.
              function drawChart() {
                // Create the data table.
                var data = google.visualization.arrayToDataTable([
                    ['Genre', 'Calificados', 'Sin calificar', 'No entregados' ],";
                
                    for ($i=0; $i <$tamano_productos ; $i++) {
                        echo "['P".$i."',".$datos_grafica[$i][0].",".$datos_grafica[$i][1] .",".$datos_grafica[$i][2]."],";
                    }
                
                echo"]);

                // Set chart options
                var options = {
                    'title':'ENTREGA Y CALIFICACIÓN DE PRODUCTOS',
                    width: 700,
                    height: 420,
                    legend: { position: 'right', maxLines: 3 },
                    bar: { groupWidth: '45%' },
                    isStacked: true,
                    colors: ['#109618', '#FF9900', '#DC3912'],
                  };

                // Instantiate and draw our chart, passing in some options.
                var chart = new google.visualization.ColumnChart(document.getElementById('chart_div2'));
                chart.draw(data, options);
              }
            </script>
<table style=\"width:100%\" class=\"table-bordered \"> <tr>
                    <td colspan=\"\"><div id=\"chart_div2\" style=\"width: 60%; align:center\"></div></td>
                    <td style=\"width: 40%; height:90\"><p style=\"background-color:#fff\"><ul><li>Entregados y calificados: $ec</li><li>Entregados y no calificados: $enc</li><li>No entregados: $ne</li></ul></p>
                    </td>
                    </tr></table><br>";

           

            //3. Nota promedio de cada producto
            #Consultar valores máximo y mínimo
            $query_seguimiento = "SELECT id, course, minscore, maxscore
                    FROM {seguimiento} WHERE course = $asignatura->instanceid";
                $files_data_seguimiento = $DB->get_records_sql($query_seguimiento, array('.','id','0'));
                foreach ($files_data_seguimiento as $fd_s) {
                    $minscore = $fd_s->minscore;
                    $maxscore = $fd_s->maxscore;
                }

            #Gráficar
            $datos_grafica = array();
            for ($i=0; $i <$tamano_productos; $i++) { 

                $cantidad = 0;
                $suma = 0;
                foreach ($listado as $ld) {
                    if ($ld[2+$i] != "no calificado" AND $ld[2+$i] != "-") {
                        $suma += $ld[2+$i];
                        $cantidad += 1;
                    }
                }
                if ($cantidad != 0) {
                    $valor = $suma/$cantidad;
                }else{
                    $valor = 0;
                }
                    array_push($datos_grafica, $valor);
            }

            echo "
            <script type=\"text/javascript\" src=\"https://www.gstatic.com/charts/loader.js\"></script>
            <script type=\"text/javascript\">

              // Load the Visualization API and the corechart package.
              google.charts.load('current', {'packages':['corechart']});

              // Set a callback to run when the Google Visualization API is loaded.
              google.charts.setOnLoadCallback(drawChart);

              // Callback that creates and populates a data table,
              // instantiates the pie chart, passes in the data and
              // draws it.
              function drawChart() {
                // Create the data table.
                var data = google.visualization.arrayToDataTable([
                    ['Producto', 'Promedio', { role: 'style' }, { role: 'annotation' } ],";
            
                     for ($i=0; $i <$tamano_productos ; $i++) {
                        if ($datos_grafica[$i]>($maxscore-(($maxscore-$minscore)/4))) {
                            $color = "#109618";
                        }elseif ($datos_grafica[$i]>($maxscore-2*(($maxscore-$minscore)/4))) {
                            $color = "#FF9900";
                        }elseif ($datos_grafica[$i]>($maxscore-3*(($maxscore-$minscore)/4))) {
                            $color = "#DC3912";
                        }else{
                            $color = "black";
                        };
                        echo "['P".$i."',".floatval($datos_grafica[$i]).", '$color', '".round($datos_grafica[$i],2)."'],";
                    }

            echo "   
                     
                  ]);

                // Set chart options
                var options = {
                    'title':'PROMEDIO DE NOTAS',
                    width: 700,
                    height: 400,
                    legend: { position: 'none' },
                    bar: { groupWidth: '65%' },
                    isStacked: false,
                  };

                // Instantiate and draw our chart, passing in some options.
                var chart = new google.visualization.BarChart(document.getElementById('chart_div3'));
                chart.draw(data, options);
              }
            </script>

            <table style=\"width:100%\" class=\"table-bordered \"> <tr>
                    <td colspan=\"\"><div id=\"chart_div3\" style=\"align:center\"></div></td>
                    <td style=\"width: 40%; height:90\">
                        <p align=\"justify\" style=\"background-color:#fff\">Promedio de notas obtenidas para cada producto.</p></td>
                    </tr></table><br>";



            //4. Top 5: Estudiantes con mejores calificaciones (promedio)
            $datos_grafica = array();
            $k = 0;
            foreach ($listado as $ld) {
                $suma = 0;
                $cantidad = 0;
                for ($i=0; $i <$tamano_productos ; $i++) { 
                    if ($ld[2+$i] != "no calificado" AND $ld[2+$i] != "-") {
                        $suma += $ld[2+$i];
                        $cantidad += 1;
                    }
                }
                if ($cantidad != 0) {
                    $valor = $suma/$cantidad;
                }else{
                    $valor = 0;
                }
                $datos_grafica[] = array($nombre_usuario[$k], $valor);
                $k++;
            }

            foreach ($datos_grafica as $key => $dg) {
                $aux[$key] = $dg[1];
            }

            array_multisort($aux, SORT_DESC, $datos_grafica);


            echo "<div><br>
                    <table align=\"center\" style=\"\" class=\"table-hover table-striped table-bordered \">
                    <tr>
                        <th colspan=\"2\">Top 5: Estudiantes con mejores calificaciones</th>
                    </tr>
                    <tr>
                        <th>Estudiante</th>
                        <th>Promedio</th>
                    </tr>";

                        for ($i=0; $i <5 ; $i++) { 
                            echo "<tr><td><br>".$datos_grafica[$i][0] ."</td>";
                            echo "<td align=\"center\">".round($datos_grafica[$i][1],2) ."</td></tr>";
                        }

            echo "</table><br><br><br><br><br><br><br>
            </div>";

            echo "</details>";
        
        //INFORME DEL DOCENTE
            
        //Consulta role_assignments: Obtenemos el id de "docente(roleid = 4) o docente editor(roleid=3)" de determinado curso
        $query_role_assignments = "SELECT id AS id,
                            roleid,
                            contextid,
                            userid
                    FROM {role_assignments} WHERE (roleid = 4 || roleid = 3) AND contextid = $asignatura->id";
        $files_data=$DB->get_records_sql($query_role_assignments,array('.','id','0'));

        //$teacher_id = array();
        foreach ($files_data as $fd) {
	        $data_teacher = new stdClass();
	        $data_teacher->id = $fd->id;
	        $data_teacher->userid = $fd->userid;

	        //$teacher_id[] = $fd->userid;
	    }

	    //Conocer la cantidad de estudiantes que han enviado cada producto
	    $query_role_assignments = "SELECT id AS id,
                            roleid,
                            contextid,
                            userid
                    FROM {role_assignments} WHERE roleid = 5 AND contextid = $asignatura->id";
        $files_data=$DB->get_records_sql($query_role_assignments,array('.','contextid','0'));


        $student_id = array();
        foreach ($files_data as $fd) {
            $data_student = new stdClass();
            $data_student->id = $fd->id;
            $data_student->userid = $fd->userid;

            $student_id[] = $fd->userid;    
        }

        //Obtener los productos del curso
        $query_grade_items = "SELECT id AS productosid,
                            itemname,
                            itemmodule,
                            iteminstance,
                            courseid
                        FROM {grade_items} WHERE courseid = $asignatura->instanceid AND itemtype != \"course\" AND itemmodule != \"seguimiento\"";
            $files_data=$DB->get_records_sql($query_grade_items,array('.','productosid','0'));


        $productos_id = array();
        $productos_nombre = array();
        $productos_tipo = array();
        $producto_item_instance = array();
        $producto_courseid = array();
        foreach ($files_data as $fd) {
                $productos_id[] = $fd->productosid;  
                $productos_nombre[] = $fd->itemname; 
                $productos_tipo[] = $fd->itemmodule;
                $producto_item_instance[] = $fd->iteminstance;  
                $producto_courseid[] = $fd->courseid;                
        }

        //Encabezado de la tabla a mostrar
        $tamano_productos = count($productos_id); 
        $tamano_estudiantes = count($student_id);

        $encabezado_productos = array();
        $encabezado_productos[] = "";
        $encabezado_productos[] = "Docente";


        for ($k=0; $k < $tamano_productos; $k++) { 
            $encabezado_productos[] = $productos_nombre[$k];
        }

        $table = new html_table();
        $table->head = $encabezado_productos;

        //Obtener datos del docente
        $listado = array();
        $total_entregados = array();
        $total_calificados = array();
        $total_retroalimentados = array();
        $almacen = array();

        $query_user_docente = "SELECT id AS id,
                            lastname,
                            firstname,
                            phone1,
                            email
                    FROM {user} WHERE id = $data_teacher->userid";
        $files_data=$DB->get_records_sql($query_user_docente,array('.','docenteid','0'));

        foreach ($files_data as $fd) {
                $data_docente = new stdClass();
                $data_docente->id = $fd->id;
                $data_docente->userid = $fd->userid;
                $email_d = $fd->email;
                $email_r = $USER->email;    
                $almacen[] = "<input type=\"checkbox\" name=\"mail\" value=\"$email_d\" id=mail_docente>";
                $url = new moodle_url('/user/profile.php?id=');
                $url_id = "=" . $fd->id;
                //$almacen[] = "<a href=\"".$url . $url_id ."\" target=\"_blank\">".$fd->firstname . " " . $fd->lastname . "</a> (" . $fd->codigo . ")" . "<br>" . $fd->phone1 . "<br>" . $fd->email;
                $almacen[] = "<a href=\"".$url . $url_id ."\" target=\"_blank\">".$fd->firstname . " " . $fd->lastname . "</a><br>" . $fd->phone1 . "<br>" . $fd->email;
                    
        }

        //Porcentajes calificados y retroalimentados de cada producto
        for ($j=0; $j < $tamano_productos; $j++) { 
                $cantidad_calificados = 0;
                $cantidad_entregados = 0;
                $cantidad_retroalimentados = 0;
                for ($i=0; $i < $tamano_estudiantes; $i++) { 
                    $query = "SELECT id AS id,
                                idnumber as codigo,
                                lastname,
                                firstname,
                                phone1,
                                email
                        FROM {user} WHERE id = $student_id[$i]";

                    $files_data=$DB->get_records_sql($query,array('.','user','0'));

                    foreach ($files_data as $fd) {
                        $data = new stdClass();

                                $query_grade_grades = "SELECT id AS notas_id,
                                                    rawgrade,
                                                    finalgrade,
                                                    aggregationstatus,
                                                    itemid,
                                                    feedback
                                        FROM {grade_grades} WHERE itemid = $productos_id[$j] AND userid = $student_id[$i]";
                                        $files_data1=$DB->get_records_sql($query_grade_grades,array('.','notas_id','0'));
                                        
                                        if ($files_data1 == null) {
                                            //$almacen[] = "-";  
                                        };
                                        
                                        foreach ($files_data1 as $fd) {
                                            $data->notas_id = $fd->notas_id;
                                            if ($fd->aggregationstatus == "used" && $fd->finalgrade != null){
                                                $cantidad_calificados +=1;
                                                $cantidad_entregados +=1;
                                                if ($productos_tipo[$j] == "quiz") {
                                                    $nota_quiz = $fd->finalgrade;
                                                }
                                            }elseif ($fd->aggregationstatus == "novalue" || $fd->finalgrade == null){
                                                //Consultamos si es un foro, quiz, o assign
                                                if ($productos_tipo[$j] == "forum"){
                                                    //Consulta si el foro fue enviado o no
                                                    $query_forum_discussion_subs = "SELECT id
                                                        FROM {forum_discussion_subs} WHERE forum = $producto_item_instance[$j] AND userid = $student_id[$i]";
                                                    $files_data1=$DB->get_records_sql($query_forum_discussion_subs,array('.','foros_id','0'));
                                                    if ($files_data1 != null){
                                                        $cantidad_entregados +=1; 
                                                    };
                                                }elseif ($productos_tipo[$j] == "assign") {
                                                    //Consulta si el assign fue enviado o no
                                                    $query_assign_submission = "SELECT id,
                                                                            status
                                                        FROM {assign_submission} WHERE assignment = $producto_item_instance[$j] AND userid = $student_id[$i]";
                                                    $files_data1=$DB->get_records_sql($query_assign_submission,array('.','assign_id','0'));
                                                    if ($files_data1 != null) {
                                                        $cantidad_entregados +=1;  
                                                    };
                                                }elseif ($productos_tipo[$j] == "quiz") {
                                                    //Consulta si el quiz fue enviado o no (quiz_attempts)
                                                    $query_quiz_attempts = "SELECT id,
                                                                            sumgrades
                                                        FROM {quiz_attempts} WHERE quiz = $producto_item_instance[$j] AND userid = $student_id[$i]";
                                                    $files_data1=$DB->get_records_sql($query_quiz_attempts,array('.','quiz_id','0'));
                                                    if ($files_data1 != null) {
                                                        $cantidad_entregados +=1;  
                                                    };
                                                    
                                                };
                                            };

                                            #Cantidad de retroalimentados
                                            if ($fd->feedback != null) {
                                                $cantidad_retroalimentados +=1;
                                            }else{
                                                if ($productos_tipo[$j] == "assign") {
                                                    $query_feedback1 = "SELECT id, courseid, itemname, iteminstance 
                                                        FROM {grade_items} WHERE id = $fd->itemid";
                                                        $files_data_feedback1=$DB->get_records_sql($query_feedback1, array('.','id','0'));
                                                        foreach ($files_data_feedback1 as $fd_f1) {
                                                            $query_feedback2 = "SELECT id, assignment, userid, grade 
                                                            FROM {assign_grades} WHERE assignment = $fd_f1->iteminstance AND userid = $student_id[$i]";
                                                            $files_data_feedback2=$DB->get_records_sql($query_feedback2, array('.','id','0'));
                                                            foreach ($files_data_feedback2 as $fd_f2) {
                                                                $query_feedback3 = "SELECT id, assignment, grade, commenttext 
                                                                FROM {assignfeedback_comments} WHERE grade = $fd_f2->id";
                                                                $files_data_feedback3=$DB->get_records_sql($query_feedback3, array('.','id','0'));
                                                                foreach ($files_data_feedback3 as $fd_f3) {
                                                                    if ($fd_f3->commenttext != "" and $fd_f3->commenttext != null) {
                                                                        $cantidad_retroalimentados += 1;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                }elseif ($productos_tipo[$j] == "forum") {
                                                    $query_feedback1 = "SELECT id, courseid, itemname, iteminstance 
                                                        FROM {grade_items} WHERE id = $fd->itemid";
                                                        $files_data_feedback1=$DB->get_records_sql($query_feedback1, array('.','id','0'));
                                                        foreach ($files_data_feedback1 as $fd_f1) {
                                                            $query_feedback2 = "SELECT id, course, forum, name, userid 
                                                            FROM {forum_discussions} WHERE forum = $fd_f1->iteminstance AND userid = $student_id[$i]";
                                                            $files_data_feedback2=$DB->get_records_sql($query_feedback2, array('.','id','0'));
                                                            foreach ($files_data_feedback2 as $fd_f2) {
                                                                $query_feedback3 = "SELECT id, discussion, userid, subject 
                                                                FROM {forum_posts} WHERE discussion = $fd_f2->id";
                                                                $files_data_feedback3=$DB->get_records_sql($query_feedback3, array('.','id','0'));
                                                                $bandera = false;
                                                                foreach ($files_data_feedback3 as $fd_f3) {
                                                                    if ($fd_f3->userid == $data_teacher->userid and $bandera == false) {
                                                                        $resp = substr($fd_f3->subject, 0, 3);
                                                                        if ($resp == "Re:") {
                                                                            $cantidad_retroalimentados += 1;
                                                                            $bandera = true;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                }elseif ($productos_tipo[$j] == "quiz") {
                                                    $query_feedback1 = "SELECT id, courseid, itemname, iteminstance 
                                                        FROM {grade_items} WHERE id = $fd->itemid";
                                                        $files_data_feedback1=$DB->get_records_sql($query_feedback1, array('.','id','0'));
                                                        foreach ($files_data_feedback1 as $fd_f1) {
                                                            $query_feedback2 = "SELECT id, quizid, feedbacktext, mingrade, maxgrade 
                                                            FROM {quiz_feedback} WHERE quizid = $fd_f1->iteminstance";
                                                            $files_data_feedback2=$DB->get_records_sql($query_feedback2, array('.','id','0'));
                                                            $bandera = false;
                                                            foreach ($files_data_feedback2 as $fd_f2) {
                                                                if ($fd_f2->feedbacktext != "" && $bandera == false && $nota_quiz >= $fd_f2->mingrade && $nota_quiz <= $fd_f2->maxgrade) {
                                                                    $cantidad_retroalimentados += 1;
                                                                    $bandera = true;
                                                                }
                                                            }
                                                        }
                                                }
                                            }
                                        };
                    };
                            
                        
                };
                
                $almacen[] = round(($cantidad_calificados * 100) / $cantidad_entregados,2) . "% <br>" . "Feedback: " .round((($cantidad_retroalimentados * 100) / $cantidad_entregados),2) . "%";    
                $total_entregados[] = $cantidad_entregados;
                $total_calificados[] = $cantidad_calificados;
                $total_retroalimentados[] = $cantidad_retroalimentados;
            };
            $almacen[] = "<a target=\"_blank\" href=\"javascript:window.open('email.php?email_d=$email_d&email_r=$email_r','','width=1000, height=480, toolbar=no, scrollbars=no, resizable=no, top=20, left=200')\"><img src=\"css/img/message.png\" alt=\"Envíar mensaje\"/><a/>";
            $listado[] = $almacen;
            
        

        //Impresión del listado
        $tamano_listado = count($listado);
        for ($i=0; $i<$tamano_listado; $i++) { 
            $table->data[] = $listado[$i];
        };
            //echo html_writer::table($table);


        //Tabla de prueba con datos de docente
        echo "<details open><summary style=\"cursor: pointer\"><img src=\"css/img/teacher.png\" width=\"20\" height=\"20\" alt=\"Ver resumen del docente\"/> Resumen del docente</summary><br>";
        echo "<table style=\"width:100%\" class=\"table-hover table-striped table-bordered \">
                <thead>
                    <tr>";
                    echo "<th align=\"right\">". $encabezado_productos[0] ."</th>";
                    echo "<th align=\"center\">". $encabezado_productos[1] ."</th>";
                    for ($i=2; $i <count($encabezado_productos) ; $i++) { 
                        echo "<th style=\"vertical-align:top; font-size:14px\">". $encabezado_productos[$i] ."</th>";
                    }
                    echo"
                    </tr>
                </thead>
                <tbody>";

                    foreach ($listado as $lt) { 
                        echo "<tr>";
                            echo "<td align=\"right\">". $lt[0] ."</td>";
                            echo "<td style=\"text-align:left; font-size:14px\">". $lt[1] ."</td>";
                        for ($j=2; $j <count($encabezado_productos) ; $j++) { 
                            echo "<td style=\"text-align:center; font-size:16px\">". $lt[$j] ."</td>";
                        }
                        echo "</tr>";
                    }
                echo "</tbody>
                </table><br><br>";
            echo "</details>";
            echo "<details>
                <summary id=\"listgraf2\" style=\"display: none\">FILTRAR RESULTADOS</summary>";
        //Gráficas de docente
            /*//1. ¿Cuándo fue el último ingreso de mis estudiantes?

                $hoy = 0;
                $ayer = 0;
                $dos_dias = 0;
                $tres_dias = 0;
                $cuatro_dias = 0;
                $cinco_mas = 0;
                    $query_user = "SELECT id AS userid,
                                    username,
                                    idnumber,
                                    firstname,
                                    lastname,
                                    lastaccess
                                FROM {user} WHERE id = $data_teacher->userid";
                    $files_data=$DB->get_records_sql($query_user,array('.','userid','0'));

                    foreach ($files_data as $fd) {
                        $ultimo_acceso = floor(abs(((mktime() - $fd->lastaccess)/86400)));
                        switch ($ultimo_acceso) {
                            case 0:
                                $hoy += 1;
                                $texto = "Hoy";
                                break;
                            case 1:
                                $ayer += 1;
                                $texto = "Ayer";
                                break;
                            default:
                                $cinco_mas += 0;
                                break;
                        }

                        if ($ultimo_acceso > 1) {
                            $texto = "Hace " . $ultimo_acceso . " días";
                        }                 
                    };

                    echo "<div>
                            <table align=\"center\">
                                <tr>
                                    <th colspan=\"2\">¿Cuándo fue el último ingreso del docente?</th>
                                </tr>
                                <tr>
                                    <td align=\"center\">".$texto."</td>
                                </tr>
                            </table>
                        </div>";*/



                //2. Productos: Calificados, retroalimentados, total.
                
                $tamano1 = count($total_entregados);
                $datos_grafica = array();
                for ($i=0; $i <$tamano_productos; $i++) { 

                    $datos_grafica[$i][0] = $total_entregados[$i];
                    $datos_grafica[$i][1] = $total_calificados[$i];
                    $datos_grafica[$i][2] = $total_retroalimentados[$i];
                }

                echo "
                <script type=\"text/javascript\" src=\"https://www.gstatic.com/charts/loader.js\"></script>
                <script type=\"text/javascript\">

                  // Load the Visualization API and the corechart package.
                  google.charts.load('current', {'packages':['corechart']});

                  // Set a callback to run when the Google Visualization API is loaded.
                  google.charts.setOnLoadCallback(drawChart);

                  // Callback that creates and populates a data table,
                  // instantiates the pie chart, passes in the data and
                  // draws it.
                  function drawChart() {
                    // Create the data table.
                    var data = google.visualization.arrayToDataTable([
                        ['Genre', 'Entregados', 'Calificados', 'Retroalimentados' ],";
                    
                        for ($i=0; $i <$tamano_productos ; $i++) {
                            echo "['P".$i."',".$datos_grafica[$i][0].",".$datos_grafica[$i][1] .",".$datos_grafica[$i][2]."],";
                        }
                    
                    echo"]);

                    // Set chart options
                    var options = {
                        'title':'CALIFICACIÓN Y RETROALIMENTACIÓN DE PRODUCTOS',
                        width: 700,
                        height: 400,
                        legend: { position: 'right', maxLines: 3 },
                        bar: { groupWidth: '65%' },
                        isStacked: false,
                        colors: ['#0099C6', '#109618', '#DC3912'],
                      };

                    // Instantiate and draw our chart, passing in some options.
                    var chart = new google.visualization.ColumnChart(document.getElementById('chart_div6'));
                    chart.draw(data, options);
                  }
                </script>

                <table style=\"width:100%\" class=\"table-bordered \"> <tr>
                    <td colspan=\"2\"><div id=\"chart_div6\"></div></td></tr>
                </table><br>";


        echo "</details>";

        echo "</div>";
        echo "<div align=\"center\"><button type=\"button\" class=\"\" onclick=\"javascript:imprSelec('area')\"><img src=\"css/img/printer.png\" width=\"20\" height=\"20\" alt=\"Imprimir\"/> Imprimir</button></div>";

	}
	

	
?>
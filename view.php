<script type="text/javascript">
  function verConfig() {
    document.getElementById("modal_config").style.display = 'block';
    document.getElementById("mostrar-modal_config").click();
  }
  function ocultarConfig() {
    document.getElementById("modal_config").style.display = 'none';
  }
</script>

<?php

/* 
 * @package    mod_seguimiento
 * @copyright  2018 Jeyson Vega <jeysonvegaromero@gmail.com> - Julian Hernandez <juliher.094@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//Instancias principales para el modulo
include 'biblioteca_principal_view.php';

//Encabezado de sitio moodle
echo $OUTPUT->header('encabezado');

//Título
echo $OUTPUT->heading($seguimiento->name);
//echo $OUTPUT->heading('Módulo de seguimiento y alertas tempranas');

// Conditions to show the intro can change to look for own settings or whatever.
/*if ($seguimiento->intro) {
    echo $OUTPUT->box(format_module_intro('seguimiento', $seguimiento, $cm->id), 'generalbox mod_introbox', 'seguimientointro');
}*/



//Obtener el rol del usuario actual (Ej: admin, estudiante, ...)
    $user_rol = array(); //Array para almacenar todos los roles permitidos del usuario actual
    $docente = false;
    $admin = false;
    global $DB;

        $query_course = "SELECT id AS id,
                            roleid
                    FROM {role_assignments} WHERE userid = $USER->id";
        $files_data = $DB->get_records_sql($query_course,array('.','id','0'));

        foreach ($files_data as $fd) {
                $user_rol[] = $fd->roleid;
        };

//Conocer roles del usuario
    for ($i=0; $i<count($user_rol); $i++) { 
        if ($user_rol[$i] == 3 || $user_rol[$i] == 4) { //teacher or editing teacher
            $docente = true;
        }elseif ($user_rol[$i] == 1) { //admin
            $admin = true;
        }
    }

//Botón de configuración de alertas
echo "<input align=\"right\" id=\"mostrar-modal_config\" name=\"modal_config\" type=\"radio\" /></br>"; 
echo "<button style=\"float:right\" target=\"_blank\" onclick=\"verConfig()\"><img src=\"css/img/settings.png\" width=\"20\" height=\"20\" alt=\"Configuración de alertas\"/> Configuración</button> ";





/*CODIGO DESARROLLADO POR JEYSON VEGA y JULIAN HERNÁNDEZ
A partir de este punto se encuentra el código agregado a la plantilla module2.zip para la consulta
de informes de acuerdo a las necesidades de la institución.
*/

//Obtener url actual (dominio, puerto, url)
$link = "http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];

//obtener id del curso actual
 global $COURSE;
 //Obtener valor real de asignatura
//Consulta context: Obtenemos el id del "curso(50)" seleccionado (instanceid) 
        $query_context = "SELECT id AS id,
                            contextlevel,
                            instanceid
                    FROM {context} WHERE contextlevel = 50 AND instanceid = $COURSE->id";
            $files_data=$DB->get_records_sql($query_context,array('.','context','0'));

            foreach ($files_data as $fd) {
                $asignatura->id = $fd->id;                    
                $asignatura->instanceid = $fd->instanceid; 

                $query_course = "SELECT id AS id,
                                category,
                                fullname,
                                shortname
                        FROM {course} WHERE id = $fd->instanceid";
                $files_data1=$DB->get_records_sql($query_course,array('.','id','0'));
                    
                foreach ($files_data1 as $fd1) {
                    $asignatura->shortname = $fd1->fullname;
                    $asignatura->category = $fd1->category;
                }
                                    
            }

        $query_category = "SELECT id,
                        name
                    FROM {course_categories} WHERE id = $asignatura->category";
            $files_data_category=$DB->get_records_sql($query_category,array('.','id','0'));

            foreach ($files_data_category as $fd_c) {
                $asignatura->category_name = $fd_c->name;
            }

            

//Consulta del rol del usuario actual
    global $DB;
    $id_usuario = $USER->id;

            $query_course = "SELECT id AS id,
                                roleid
                        FROM {role_assignments} WHERE userid = $id_usuario";
            $files_data=$DB->get_records_sql($query_course,array('.','role_assignments','0'));

            $usuario_permitido_docente = false;
            $usuario_permitido_admin = false;
            //Cambiar: Para admin debe mostrar los dos informes, para docente sólo el de estudiante
            //Por ahora sólo mostraré el informe de docentes al admin, luego debe permitir seleccionar
            //cuál de los dos informes quiere mostrar

            foreach ($files_data as $fd) {
                    $data = new stdClass();
                    $data->id = $fd->id;
                    $data->roleid = $fd->roleid;   

                    if ($data->roleid == 3 || $data->roleid == 4) { //permisos para manager(1), editingteacher(2) y teacher(3)
                        $usuario_permitido_docente = true;
                    }elseif ($data->roleid == 1) {
                        $usuario_permitido_admin = true;
                    }else{
                        echo "Usted no tiene autorización para realizar esta consulta";
                        break;
                    };
            };


   
            
//Cargar estilos y bibliotecas
include 'librerias/biblioteca_informe_curso.php';
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/estilo2.css\">"; //estilos ventana modal
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/estilo3.css\">"; //estilos ventana modal
include 'librerias/biblioteca_view_config.php';


//Envío de configuración de alertas tempranas a la base de datos

//enviar datos de configuración a la base de datos (configuración 1)
$validador = false;
$validador = $_POST['validador'];

if ($validador == true) {

    global $DB;
        if ($asignatura->category == 0){

            $temp = $_POST['asignatura_seleccionada'];
            $query_course = "SELECT id
                        FROM {context} WHERE contextlevel = 50 AND instanceid = $temp";
                $files_data_course=$DB->get_records_sql($query_course,array('.','id','0'));

                foreach ($files_data_course as $fdc) {
                    $record->course_context = $fdc->id;
                    $asignatura->id = $fdc->id;
                }

            $record->course_instance = $_POST['asignatura_seleccionada'];

        }else{
            $record->course_context = $asignatura->id;
            $record->course_instance = $asignatura->instanceid;
        }
        

        //estudiante

        if ($_POST['e_riesgo_umbral']<1 || $_POST['e_riesgo_umbral']>100) {
            $record->e_riesgo_umbral = null;
        }else{
            $record->e_riesgo_umbral = $_POST['e_riesgo_umbral'];
        }        

        if ($_POST['e_productos_umbral1']<1 || $_POST['e_productos_umbral1']>999) {
            $record->e_productos_umbral1 = null;
        }else{
            $record->e_productos_umbral1 = $_POST['e_productos_umbral1'];
        }

        if ($_POST['e_productos_umbral2']<1 || $_POST['e_productos_umbral2']>999) {
            $record->e_productos_umbral2 = null;
        }else{
            $record->e_productos_umbral2 = $_POST['e_productos_umbral2'];
        }

        if ($_POST['e_productos_mensaje'] == "") {
            if ($record->e_productos_umbral1 == null) {
                $record->e_productos_mensaje = null;
            }else{
                /*$record->e_productos_mensaje = "Estimado estudiante:

Este mensaje ha sido enviado por el sistema de seguimiento académico por la no entrega de $record->e_productos_umbral1 producto(s) en el curso $asignatura->shortname .
Lo invitamos a participar activamente de todas las actividades para evitar bajas calificaciones.

    Cordialmente,

    Sistema de seguimiento y apoyo académico.";*/
                $record->e_productos_mensaje = $msj_curso ."

Este mensaje ha sido enviado por el sistema de seguimiento académico por la no entrega de $record->e_productos_umbral1 producto(s) en el curso $asignatura->shortname .
Lo invitamos a participar activamente de todas las actividades para evitar bajas calificaciones.

    Cordialmente,

    Sistema de seguimiento y apoyo académico";
            }
            
        }else{
            $record->e_productos_mensaje = $_POST['e_productos_mensaje'];
        }

        //docente

        if ($_POST['d_productos_umbral1']<1 || $_POST['d_productos_umbral1']>999) {
            $record->d_productos_umbral1 = null;
        }else{
            $record->d_productos_umbral1 = $_POST['d_productos_umbral1'];
        }

        if ($_POST['d_productos_umbral2']<1 || $_POST['d_productos_umbral2']>999) {
            $record->d_productos_umbral2 = null;
        }else{
            $record->d_productos_umbral2 = $_POST['d_productos_umbral2'];
        }

        if ($_POST['d_productos_mensaje'] == "") {
            if ($record->d_productos_umbral1 == null) {
                $record->d_productos_mensaje = null;
            }else{
                $record->d_productos_mensaje = "Estimado docente:

Este mensaje ha sido enviado por el sistema de seguimiento académico por la no calificación/retroalimentación de $record->e_productos_umbral1 producto(s) en el curso $asignatura->shortname .
Lo invitamos a calificar y retroalimentar a tiempo en pro del bienestar académico de los estudiantes.

    Cordialmente,

    Sistema de seguimiento y apoyo académico.";
            }
            
        }else{
            $record->d_productos_mensaje = $_POST['d_productos_mensaje'];
        }
        

        $query_config1 = "SELECT id as configid,
                                    course_context
                            FROM {seguimiento_config_productos} WHERE course_context = \"$asignatura->id\"";
            $files_data=$DB->get_records_sql($query_config1,array('.','configid','0'));

            if ($files_data != null) {
                foreach ($files_data as $fd) {
                        $record->id = $fd->configid;
                };
                $DB->update_record('seguimiento_config_productos', $record);              
            }else{
                $DB->insert_record('seguimiento_config_productos', $record);
            }
}


//enviar datos de configuración a la base de datos (configuración 1)

if ($validador == true) {
    global $DB;
    $record = new object();

    if ($asignatura->category == 0){

            $record->category = $_POST['programa_seleccionado'];
            $asignatura->category = $_POST['programa_seleccionado'];

        }else{
            $record->category = $asignatura->category;
        }

    

    //estudiantes

        if ($_POST['e_ingresos_umbral1']<1 || $_POST['e_ingresos_umbral1']>999) {
            $record->e_ingresos_umbral1 = null;
        }else{
            $record->e_ingresos_umbral1 = $_POST['e_ingresos_umbral1'];
        }

        if ($_POST['e_ingresos_umbral2']<1 || $_POST['e_ingresos_umbral2']>999) {
            $record->e_ingresos_umbral2 = null;
        }else{
            $record->e_ingresos_umbral2 = $_POST['e_ingresos_umbral2'];
        }

        if (empty($_POST['e_ingresos_mensaje'])) {
            if ($record->e_ingresos_umbral1 == null) {
                $record->e_ingresos_mensaje = null;
            }else{
                $record->e_ingresos_mensaje = "Estimado estudiante:

Este mensaje ha sido enviado por el sistema de seguimiento académico por el no ingreso a plataforma en los últimos $record->e_ingresos_umbral1 día(s) en el programa $asignatura->category_name .
Lo invitamos a ingresar y participar activamente en pro de la excelencia académica.

    Cordialmente,

    Sistema de seguimiento y apoyo académico.";
            }
            
        }else{
            $record->e_ingresos_mensaje = $_POST['e_ingresos_mensaje'];
        }

    //docentes

        if ($_POST['d_ingresos_umbral1']<1 || $_POST['d_ingresos_umbral1']>999) {
            $record->d_ingresos_umbral1 = null;
        }else{
            $record->d_ingresos_umbral1 = $_POST['d_ingresos_umbral1'];
        }

        if ($_POST['d_ingresos_umbral2']<1 || $_POST['d_ingresos_umbral2']>999) {
            $record->d_ingresos_umbral2 = null;
        }else{
            $record->d_ingresos_umbral2 = $_POST['d_ingresos_umbral2'];
        }

        if ($_POST['d_ingresos_mensaje'] == "") {
            if ($record->d_ingresos_umbral1 == null) {
                $record->d_ingresos_mensaje = null;
            }else{
                $record->d_ingresos_mensaje = "Estimado docente:

Este mensaje ha sido enviado por el sistema de seguimiento académico por el no ingreso a plataforma en los últimos $record->e_ingresos_umbral1 día(s) en el programa $asignatura->category_name .
Lo invitamos a ingresar y participar activamente en pro de la excelencia académica.

    Cordialmente,

    Sistema de seguimiento y apoyo académico.";
            }
            
        }else{
            $record->d_ingresos_mensaje = $_POST['d_ingresos_mensaje'];
        }


        $query_config2 = "SELECT id as configid
                            FROM {seguimiento_config_ingresos} WHERE category = \"$asignatura->category\"";
            $files_data=$DB->get_records_sql($query_config2,array('.','configid','0'));

            if ($files_data != null) {
                foreach ($files_data as $fd) {
                        $record->id = $fd->configid;
                };
                $DB->update_record('seguimiento_config_ingresos', $record);              
            }else{
                $DB->insert_record('seguimiento_config_ingresos', $record);
            }

}


// Finish the page.
echo $OUTPUT->footer('pie de página');
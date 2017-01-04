<?php
/**
 * Created by PhpStorm.
 * User: mfacu
 * Date: 26/12/2016
 * Time: 14:01
 */

/*
 * TODO : Verificar que lleguen bien los datos y no halla errores
 */


$mysqli = new mysqli("localhost", "root", "", "picadito");

if ($mysqli->connect_error) {
    printf("Falló la conexión: %s\n", $mysqli->connect_error);
    exit();
}

error_reporting(0);


$mail = addslashes($_GET['mail']);
$pass = addslashes($_GET['pass']);
$consulta = addslashes($_GET['consulta']);



switch ($consulta)
{

    //TODO : NO TOMAR CAMPOS INSEGUROS
    case "buscadorJugadores":
        $campoBusqueda = addslashes($_GET["campoBusqueda"]);

        $dbpet = $mysqli->query("SELECT * FROM usuarios_jugadores WHERE mail LIKE '%".$campoBusqueda."%' OR nombre LIKE '%".$campoBusqueda."%' LIMIT 0,5");
        while($usuario = mysqli_fetch_array($dbpet, MYSQLI_ASSOC))
        {
            $jsonData[] = ($usuario);
        }

        echo json_encode($jsonData);
        break;

    case "dameinfousuario":
        $dbpet = $mysqli->query("SELECT * FROM usuarios_jugadores WHERE mail='".$mail."' AND pass='".$pass."'");
        if (mysqli_num_rows($dbpet)<=0)
            die("invalidlogin");
        $usuario = mysqli_fetch_array($dbpet, MYSQLI_ASSOC);

        echo "correcto:".json_encode($usuario);
        break;

    case "crearequipo":
        $dbpet = $mysqli->query("SELECT * FROM usuarios_jugadores WHERE mail='".$mail."' AND pass='".$pass."'");
        if (mysqli_num_rows($dbpet)<=0)
            die("invalidlogin");


        //TODO: verificar que existan jugadores e invitaciones sistema
        $nombreEquipo = addslashes($_POST['nombre']);
        $jugadores = $_POST['jugadores']; // CUIDADO AGREGAR ADDSLASHES EN CADA CONSULTA
        $mysqli->query("INSERT INTO `equipos` (`nombre`, `imagen`) VALUES ('".$nombreEquipo."', 'http://staticmd.lavozdelinterior.com.ar/sites/all/themes/mundod_new/img/escudos/20.png')");
        $idEquipo = $mysqli->insert_id;
        $pieces = explode("|", $jugadores);
        $mysqli->query("INSERT INTO `equipos_usuarios` (`id_equipo`, `id_usuario`, `rol`) VALUES ('".$idEquipo."', (select usuarios_jugadores.id_usuario from usuarios_jugadores where mail=\"".$mail."\" LIMIT 0,1),'1')");
        foreach($pieces as $element)
        {
            $mysqli->query("INSERT INTO `equipos_usuarios` (`id_equipo`, `id_usuario`, `rol`) VALUES ('".$idEquipo."', '".addslashes($element)."','0')");

        }


        $dbpet = $mysqli->query("SELECT equipos.id_equipo, equipos.nombre, equipos.partidos_ganados, equipos.partidos_perdidos, equipos.partidos_empatados, equipos.partidos_suspendidos, equipos.fecha_alta, equipos.ultima_actividad, equipos.imagen from equipos JOIN equipos_usuarios on equipos_usuarios.id_equipo = equipos.id_equipo WHERE equipos_usuarios.id_equipo='".$idEquipo."';");
        $equipo = mysqli_fetch_array($dbpet, MYSQLI_ASSOC);
        echo json_encode($equipo);



        break;




    case "dameequipos":
        $dbpet = $mysqli->query("SELECT * FROM usuarios_jugadores WHERE mail='".$mail."' AND pass='".$pass."'");
        if (mysqli_num_rows($dbpet)<=0)
            die("invalidlogin");
        $usuario = mysqli_fetch_array($dbpet, MYSQLI_ASSOC);

        $jsonData = array();

        $dbpet = $mysqli->query("SELECT equipos.id_equipo, equipos.nombre, equipos.partidos_ganados, equipos.partidos_perdidos, equipos.partidos_empatados, equipos.partidos_suspendidos, equipos.fecha_alta, equipos.ultima_actividad, equipos.imagen from equipos JOIN equipos_usuarios on equipos_usuarios.id_equipo = equipos.id_equipo WHERE equipos_usuarios.id_usuario=".$usuario["id_usuario"].";");
        while($equipos = mysqli_fetch_array($dbpet, MYSQLI_ASSOC))
        {
            $jsonData[] = ($equipos);
        }

        echo json_encode($jsonData);


        break;


    case "dameinfoequipo":
        $dbpet = $mysqli->query("SELECT * FROM usuarios_jugadores WHERE mail='".$mail."' AND pass='".$pass."'");
        if (mysqli_num_rows($dbpet)<=0)
            die("invalidlogin");
        $usuario = mysqli_fetch_array($dbpet, MYSQLI_ASSOC);


        $id_equipo = addslashes($_GET['id_equipo']);

        $jsonData = array();

        $dbpet = $mysqli->query("SELECT equipos.id_equipo, equipos.nombre, equipos.partidos_ganados, equipos.partidos_perdidos, equipos.partidos_empatados, equipos.partidos_suspendidos, equipos.fecha_alta, equipos.ultima_actividad, equipos.imagen from equipos WHERE id_equipo=".$id_equipo.";");
        if (mysqli_num_rows($dbpet)>0)

        {
            echo "correcto:";
            $equipo = mysqli_fetch_array($dbpet, MYSQLI_ASSOC);
            echo json_encode($equipo);
        }










        break;



    case "damenotificaciones":
        $dbpet = $mysqli->query("SELECT * FROM usuarios_jugadores WHERE mail='".$mail."' AND pass='".$pass."'");
        if (mysqli_num_rows($dbpet)<=0)
            die("invalidlogin");
        $usuario = mysqli_fetch_array($dbpet, MYSQLI_ASSOC);



        $jsonData = array();

        $dbpet = $mysqli->query("SELECT * FROM `notificaciones_usuarios` WHERE id_usuario=".$usuario["id_usuario"]." AND vista='0';");

        if (mysqli_num_rows($dbpet)>0)
            echo "correcto:";
        while($notificacion = mysqli_fetch_array($dbpet, MYSQLI_ASSOC))
        {
            $jsonData[] = ($notificacion);
            $mysqli->query("UPDATE `notificaciones_usuarios` SET `vista`='1' WHERE `id_notificacion`='".$notificacion["id_notificacion"]."';");
        }

        echo json_encode($jsonData);


        break;



    case "damepartidos":
        $dbpet = $mysqli->query("SELECT * FROM usuarios_jugadores WHERE mail='".$mail."' AND pass='".$pass."'");
        if (mysqli_num_rows($dbpet)<=0)
            die("invalidlogin");
        $usuario = mysqli_fetch_array($dbpet, MYSQLI_ASSOC);
        $id_equipo = addslashes($_GET['id_equipo']);


        //TODO: Verficar si todos pueden ver los partidos del equipo



        $jsonData = array();

        $dbpet = $mysqli->query("SELECT * from partidos WHERE id_equipo1='".$id_equipo."' OR id_equipo2='".$id_equipo."';");

        if (mysqli_num_rows($dbpet)>0)
            echo "correcto:";
        while($partido = mysqli_fetch_array($dbpet, MYSQLI_ASSOC))
        {
            $jsonData[] = ($partido);

        }

        echo json_encode($jsonData);


        break;



    case "crearusuario":



        //TODO: verificar que existan jugadores e invitaciones sistema
        $nombre = addslashes($_POST['nombre']);
        $mail = addslashes($_POST['mail']);
        $pass = addslashes($_POST['pass']);

        $dbpet = $mysqli->query("SELECT * FROM usuarios_jugadores WHERE mail='".$mail."'");
        if (mysqli_num_rows($dbpet)>0)
            die("repetido");


        $mysqli->query("INSERT INTO `usuarios_jugadores` (`nombre`, `mail`,`pass`) VALUES ('".$nombre."', '".$mail."','".$pass."')");
        $idUsuario = $mysqli->insert_id;



        $dbpet = $mysqli->query("SELECT * FROM usuarios_jugadores WHERE id_usuario='".$idUsuario."'");

        if (mysqli_num_rows($dbpet)<=0)
            die("invalidlogin");
        $usuario = mysqli_fetch_array($dbpet, MYSQLI_ASSOC);

        echo "correcto:".json_encode($usuario);
        break;







}
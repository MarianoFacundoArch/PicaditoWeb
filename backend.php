<?php
/**
 * Created by PhpStorm.
 * User: mfacu
 * Date: 26/12/2016
 * Time: 14:01
 */




$mysqli = new mysqli("localhost", "root", "", "picadito");

if ($mysqli->connect_error) {
    printf("Falló la conexión: %s\n", $mysqli->connect_error);
    exit();
}




$mail = addslashes($_GET['mail']);
$pass = addslashes($_GET['pass']);
$consulta = addslashes($_GET['consulta']);



switch ($consulta)
{

    case "dameinfousuario":
        $dbpet = $mysqli->query("SELECT * FROM usuarios_jugadores WHERE mail='".$mail."' AND pass='".$pass."'");
        if (mysqli_num_rows($dbpet)<=0)
            die("invalidlogin");
        $usuario = mysqli_fetch_array($dbpet, MYSQLI_ASSOC);

        echo json_encode($usuario);
        break;


    case "dameequipos":
        $dbpet = $mysqli->query("SELECT * FROM usuarios_jugadores WHERE mail='".$mail."' AND pass='".$pass."'");
        if (mysqli_num_rows($dbpet)<=0)
            die("invalidlogin");
        $usuario = mysqli_fetch_array($dbpet, MYSQLI_ASSOC);

        $jsonData = array();

        $dbpet = $mysqli->query("SELECT equipos.id_equipo, equipos.nombre, equipos.partidos_ganados, equipos.partidos_perdidos, equipos.partidos_empatados, equipos.partidos_suspendidos from equipos JOIN equipos_usuarios on equipos_usuarios.id_equipo = equipos.id_equipo WHERE equipos_usuarios.id_usuario=".$usuario["id_usuario"].";");
        while($equipos = mysqli_fetch_array($dbpet, MYSQLI_ASSOC))
        {
            $jsonData[] = ($equipos);
        }

        echo json_encode($jsonData);


        break;


    





}
<?php

$server= "localhost:3307";
$user="root";
$pass="";
$db= "db_pwci_darkwardrobe";

$conn=new mysqli($server,$user,$pass,$db);

if($conn->connect_errno){
    die("Conexion Fallida a la Base de Datos".$conn->connect_errno);

}
else{
    //echo "Conexion Realizada Con Exito";
}


?>

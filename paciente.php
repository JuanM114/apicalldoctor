<?php
include "config.php";
include "utils.php";
$dbConn =  connect($db);
/*
  listar todos los posts o solo uno
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    if (isset($_GET['idPaciente']))
    {
      //Mostrar un post
      $sql = $dbConn->prepare("SELECT * FROM paciente where idPaciente=:idPaciente");
      $sql->bindValue(':idPaciente', $_GET['idPaciente']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
      exit();
	  }
    else {
      //Mostrar lista de post
      $sql = $dbConn->prepare("SELECT * FROM paciente");
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      echo json_encode( $sql->fetchAll()  );
      exit();
	}
}
// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $input = $_POST;
    $sql = "INSERT INTO paciente
          (nombre, fecha_nacimiento, Direccion, Telefono, Ciudad, idusuario1)
          VALUES
          (:nombre, :fecha_nacimiento, :Direccion, :Telefono, :Ciudad, :idusuario1)";
    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);
    $statement->execute();
    $postId = $dbConn->lastInsertId();
    if($postId)
    {
      $input['idPaciente'] = $postId;
      header("HTTP/1.1 200 OK");
      echo json_encode($input);
      exit();
	 }
}
//Borrar
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
	$idPaciente = $_GET['idPaciente'];
  $statement = $dbConn->prepare("DELETE FROM paciente where idPaciente=:idPaciente");
  $statement->bindValue(':idPaciente', $idPaciente);
  $statement->execute();
	header("HTTP/1.1 200 OK");
	exit();
}
//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    $input = $_GET;
    $postId = $input['idPaciente'];
    $fields = getParams($input);
    $sql = "
          UPDATE posts
          SET $fields
          WHERE idPaciente ='$postId'
           ";
    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);
    $statement->execute();
    header("HTTP/1.1 200 OK");
    exit();
}
//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");
?>
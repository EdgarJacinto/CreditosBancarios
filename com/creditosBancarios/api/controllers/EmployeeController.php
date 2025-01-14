<?php
require_once(__DIR__.'/../../util/entities/EntityEmployee.php');
require_once(__DIR__.'/../config/UserTypes.php');

class EmployeeController{

  public $request;

  function __construct($request){
    $this->request = $request;
  }

  public function dictamination(){
    $employee = new EntityEmployee();
    $requestId = $this->request["request"]; //Obtenemos ID de solicitud a dictaminar
    $verdict = $this->request["verdict"]; // Obtenemos veredicto del dictaminador
    $response = json_encode($employee->dictaminate($requestId,$verdict));
    echo $response;
  }

  public function creditAuthorization(){
    $employee = new EntityEmployee();
    session_start();
    $employeeId = $_SESSION["user"];
    $requestId = $this->request["requestId"];
    $pswd = md5($this->request["pswd"]);
    $response = json_encode($employee->authorizeCreditRequest($employeeId,$pswd,$requestId));
    echo $response;
  }

  public function telephonicInvestigation(){
    $employee = new EntityEmployee();
    $requestId = $this->request["request"];
    $references =$this->request["references"]; //Array que contiene los datos de las 2 referencias del cliente
    $response = json_encode($employee->setInvestigationResult($requestId,$references));
    echo $response;
  }

  public function getPendingRequests(){
    $employee = new EntityEmployee();
    session_start();
    $employeeId = $_SESSION["user"];
    $response = $employee->getAllPendingRequests($employeeId);
    echo $response;
  }

  public function getProcessingView(){
    $requestId = $this->request["request"];
    $employee = new EntityEmployee();
    $response = null;
    session_start();
    $employeeType = $_SESSION["userType"];
    $response = $employee->getRequest($requestId);
    if(json_decode($response)->result != 0){
      $_SESSION["requestObject"] = $response;
      switch($employeeType){
        case UserTypes::MANAGER:
          $response = "manangerialAuthorizeCreditRequest.php";
          break;
        case UserTypes::DICTAMINATOR:
        $response = "employeDictamination.php";
          break;
        case UserTypes::MANAGERIAL:
        $response = "employeeInvestigationResult.php";
          break;
        default:
          echo json_encode(array("result"=>-1,"message"=>"Inconsistencias en el tipo de empleado"));
      }
    }
    echo $response;
  }

}

$request_type = $_POST["action"]; //Recibimos el tipo de acción para este controlador
//NOTA: Para este ejemplo, para mejor legibolidad,
// manejé strings pero pueden ser reemplazadas por enteros,.
$controller = new EmployeeController($_POST); //Guardamos el request recibido
switch($request_type){

  case "dictamination":
    $controller->dictamination(); //Llamamos al método correspondiente
    break;
  case "authorization":
    $controller->creditAuthorization();
    break;
  case "investigation":
    $controller->telephonicInvestigation();
    break;
  case "pendingRequests":
    $controller->getPendingRequests();
    break;
  case "processRequest":
    $controller->getProcessingView();
    break;
  default:
    echo json_encode(array("message"=>"Servicio no disponible"));
}

 ?>

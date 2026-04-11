<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Taller.php';
require_once __DIR__ . '/../models/Solicitud.php';

class TallerController
{
    private $tallerModel;
    private $solicitudModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->connect();
        $this->tallerModel = new Taller($db);
        $this->solicitudModel = new Solicitud($db);
    }

    public function index()
    {
        if (!isset($_SESSION['id'])) {
            header('Location: index.php?page=login');
            return;
        }
        require __DIR__ . '/../views/taller/listado.php';
    }
    
    public function getTalleresJson()
    {
        if (!isset($_SESSION['id'])) {
            echo json_encode([]);
            return;
        }
        
        $talleres = $this->tallerModel->getAllDisponibles();
        header('Content-Type: application/json');
        echo json_encode($talleres);
        exit();
    }
    
    public function solicitar()
    {
        if (!isset($_SESSION['id'])) {
            echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión']);
            return;
        }
        
        $tallerId = $_POST['taller_id'] ?? 0;
        $usuarioId = $_SESSION['id'];
        $estadoValidacion = $this->validarSolicitudTaller($tallerId,$usuarioId);
        $response = [
            "response" => "",
            "mensaje" => "",
            "data" => []
        ];
        switch ($estadoValidacion) {
            // 0 -> vacio
            //1 -> estado pendiente
            //2 -> aprobado
            case 0:
                $response["response"] = "00";
                $response["mensaje"] = "Curso Solicitado correctamente.
                Debe esperar la respuesta de la solicitud";
                break;
            case 1:
                $response["response"] = "01";
                $response["mensaje"] = "Tienes una solicitud del taller pendiente";
                break;
            case 2:
                $response["response"] = "01";
                $response["mensaje"] = "Usted ya tiene este taller aprobado";
                break;
            
            default:
                $response["response"] = "00";
                $response["mensaje"] = "Error no tratado";
                break;
        };
        if($response['response'] == "00") {
            $this->solicitudModel->create($usuarioId, $tallerId);
            $datosActualizados = $this->tallerModel->getById($tallerId);
            $response['data'] = [
                "cupo_disponible" => $datosActualizados["cupo_disponible"]
            ];
        } 
        echo json_encode($response);
        exit;

    }

    private function validarSolicitudTaller($id_taller, $id_usuario) {
        $solicitudes = $this->solicitudModel->getByUserAndTaller($id_usuario, $id_taller,);
        if(empty($solicitudes)) return  0;
        foreach($solicitudes as $solicitud) {
            if($solicitud['estado'] == "pendiente") return 1;
            if($solicitud['estado'] == "aprobada") return 2;
        }
    }

}
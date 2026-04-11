<?php

use function PHPSTORM_META\type;

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Solicitud.php';
require_once __DIR__ . '/../models/Taller.php';

class AdminController
{
    private $solicitudModel;
    private $tallerModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->connect();
        $this->solicitudModel = new Solicitud($db);
        $this->tallerModel = new Taller($db);
    }

    public function solicitudes()
    {
        if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
            header('Location: index.php?page=login');
            return;
        }
        require __DIR__ . '/../views/admin/solicitudes.php';
    }

    public function getSolicitudesJson() {
        if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }

        $response = [
            "response" => "",
            "mensaje" => "",
            "data" => []
        ];
        $solicitudes = $this->solicitudModel->getAll();
        $response = [
            "response" => "00",
            "mensaje" => "Solicitud aceptada",
            "data" => [
                "solicitudes" => $solicitudes
            ]
        ];
        /* header('Content-Type: application/json'); */
        echo json_encode($response);
        exit();
        
    }

    public function aprobar()
    {
        if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }

        $solicitudId = $_POST['id_solicitud'] ?? 0;

        $solicitud = $this->solicitudModel->getById($solicitudId);
        $tallerId = $solicitud["taller_id"];
        $cuposDisponibles = $this->tallerModel->getById($tallerId);
        if ($cuposDisponibles['cupo_disponible'] == 0) {
            $this->rechazar(true);
            exit();
        }
        
        if ($this->solicitudModel->actualizarEstado($solicitudId, 'aprobada')) {
            $this->tallerModel->descontarCupo($tallerId);
            echo json_encode(['success' => true, 'mensaje' => 'Solicitud aprobada']);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo aprobar']);
        }
        exit();
    }

    public function rechazar($cuposLimitesAlcanzados = false)
    {
        if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }

        $solicitudId = $_POST['id_solicitud'] ?? 0;
        $solicitud = $this->solicitudModel->getById($solicitudId);
        $seActualizo = $this->solicitudModel->actualizarEstado($solicitudId, 'rechazada');
        if(!$solicitud) {
            echo json_encode(['success' => false, 'error' => 'Solicitud no encontrada']);
            exit();
        }
        if(!$seActualizo) {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar estado']);
        }
        if($seActualizo && $cuposLimitesAlcanzados == false) {
            echo json_encode(['success' => true, 'mensaje' => 'Solicitud rechazada']);
            
        }
        if($seActualizo && $cuposLimitesAlcanzados) {
            echo json_encode(['success' => true, 'mensaje' => 'Solicitud rechazada cupos no disponibles']);
        }
        exit();
        
    }
}
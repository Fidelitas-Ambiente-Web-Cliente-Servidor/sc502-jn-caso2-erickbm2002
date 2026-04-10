<?php

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

        if ($this->solicitudModel->actualizarEstado($solicitudId, 'aprobada')) {
            echo json_encode(['success' => true, 'mensaje' => 'Solicitud aprobada']);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo aprobar']);
        }
        exit();
    }

    public function rechazar()
    {
        if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }

        $solicitudId = $_POST['id_solicitud'] ?? 0;
        $solicitud = $this->solicitudModel->getById($solicitudId);

        if ($solicitud) {
            if ($this->solicitudModel->actualizarEstado($solicitudId, 'rechazada')) {
                // 3. Sumar el cupo de nuevo al taller
                $this->tallerModel->sumarCupo($solicitud['taller_id']);
                echo json_encode(['success' => true, 'mensaje' => 'Solicitud rechazada y cupo devuelto']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al actualizar estado']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Solicitud no encontrada']);
        }
        exit();
    }
}
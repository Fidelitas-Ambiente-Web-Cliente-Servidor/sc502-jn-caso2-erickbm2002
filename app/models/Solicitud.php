<?php
class Solicitud
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getByUserAndTaller($userID, $tallerID) {
        $sql = "SELECT * FROM solicitudes 
        WHERE usuario_id = ? and taller_id = ? AND estado IN ('pendiente','aprobada')";
        $statement = $this->conn->prepare($sql);
        $statement->bind_param('ii', $userID,$tallerID);
        $statement->execute();
        $result = $statement->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($user_id,$taller_id) {
        $query = "INSERT INTO solicitudes (taller_id , usuario_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $taller_id,  $user_id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getAll()
    {
        $query = "SELECT s.id, t.descripcion, u.username, s.fecha_solicitud
              FROM solicitudes AS s
              JOIN talleres AS t ON t.id = s.taller_id
              JOIN usuarios AS u ON u.id = s.usuario_id
              WHERE s.estado = 'pendiente'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        $solicitudes = []; 
        while ($row = $result->fetch_assoc()) {
            $solicitudes[] = $row;
        }
        return $solicitudes;
    }
    
    public function actualizarEstado($id, $estado)
    {
        $query = "UPDATE solicitudes SET estado = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $estado, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM solicitudes WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    
}


<?php
class Taller
{

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $result = $this->conn->query("SELECT * FROM talleres ORDER BY nombre");
        $talleres = [];
        while ($row = $result->fetch_assoc()) {
            $talleres[] = $row;
        }
        return $talleres;
    }

    public function getAllDisponibles()
    {
        $sql = 'SELECT * FROM talleres WHERE cupo_disponible > 0';
        $statement = $this->conn->prepare($sql);
        $statement->execute();
        $result = $statement->get_result();
        $talleres = [];
        while($row = $result->fetch_assoc()) {
            $talleres[] = $row;
        }
        return $talleres;
    }

    public function getById($id)
    {
        $sql = 'SELECT * FROM talleres WHERE id = ?';
        $statement = $this->conn->prepare($sql);
        $statement->bind_param('i', $id);
        $statement->execute();
        $result = $statement->get_result();
        return $result->fetch_assoc();
    }

    public function descontarCupo($tallerId)
    {
        $sql = 'UPDATE talleres 
            SET cupo_disponible = cupo_disponible - 1
            WHERE id = ? AND cupo_disponible > 0';
        $statement = $this->conn->prepare($sql);
        $statement->bind_param('i', $tallerId);
        $statement->execute();
        return $statement->affected_rows;
    }

    public function sumarCupo($tallerId)
    {
        $sql = 'UPDATE talleres 
            SET cupo_disponible = cupo_disponible + 1 
            WHERE id = ?';
        $statement = $this->conn->prepare($sql);
        $statement->bind_param('i', $tallerId);
        $statement->execute();
        return $statement->affected_rows;
    }
}

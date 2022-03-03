<?php

final class Prestamo {
    public $id = null;
    public $fecha = null;
    public $fecha_fin = null;
    public $motivo= null;
    public $recibe= null;
    public $local_req= null;
    public $id_equipo = 0;
    public $id_local_dest = 0;
    public $id_estado = 0;
    public $id_usuario_req = 0;
    public $id_usuario_auth = null;
    
    public function __construct(int $id = null, DateTime $fecha = null, DateTime $fecha_fin = null, string $motivo = null, string $recibe = null, string $local_req = null, int $id_equipo = 0, int $id_local_dest = 0, int $id_estado = 0, int $id_usuario_req = 0, int $id_usuario_auth = null) {
        $this->id = $id;
        $this->fecha = $fecha;
        $this->fecha_fin = $fecha_fin;
        $this->motivo= $motivo;
        $this->recibe= $recibe;
        $this->local_req= $local_req;
        $this->id_equipo = $id_equipo;
        $this->id_local_dest = $id_local_dest;
        $this->id_estado = $id_estado;
        $this->id_usuario_req = $id_usuario_req;
        $this->id_usuario_auth = $id_usuario_auth;
    }
    
    public static function fromArray(array $data): Prestamo {
        $fecha = new DateTime($data['fecha']);
        $fecha_fin = new DateTime($data['fecha_fin']);
        return new Prestamo($data['id'], $fecha, $fecha_fin, $data['motivo'], $data['recibe'], $data['local_req'], (int)$data['id_equipo'], (int)$data['id_local_dest'], $data['id_estado'], $data['id_usuario_req'], $data['id_usuario_auth']);
    }

    public static function getLocal(int $id): int {
        $bd = new Bd();
        $id_equipo = $bd->seleccionar("prestamos", "id = '$id'", "id_equipo")->fetch()['id_equipo'];
        return Equipo::getLocal($id_equipo);
    }

    public static function getLocalDest(int $id): int {
        $bd = new Bd();
        return $bd->seleccionar("prestamos", "id = '$id'", "id_local_dest")->fetch()['id_local_dest'];
    }
}
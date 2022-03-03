<?php

final class prestamos {
    private $Bd;
    private $Raiz = null;
    private $u_actual = null;

    public function __construct(string $raiz) {
        $this->Bd = new Bd();
        $this->Raiz = $raiz;
        $this->u_actual = Usuario::getIdActual();
    }

    public function get($prestamo = null, $params = null) {
        if($prestamo != null) {
            if(is_numeric($prestamo)){
                $d = $this->Bd->seleccionar("prestamos", "id = $prestamo")->fetch();
            }
            
            if ($d != null) {

                $p = Prestamo::fromArray($d);
                if (Local::esHijoDe(Prestamo::getLocal($p->id), $this->Raiz)) {
                    return $p;
                }
            }
            return null;
        }

        $t = null;
        $l = Local::obtTodos($this->Raiz);
        if(!empty($params)){
            $params = "&&".$params;
        }
        $ids = null;
        for ($i=0; $i < count($l); $i++) { 
            $ids = $ids.$l[$i]->id;
            if($i < count($l)-1){
                $ids = $ids.", ";
            }
        }

        $d = $this->Bd->ejecutar("select prestamos.* from prestamos inner join equipos on (prestamos.id_equipo = equipos.id) where equipos.id_local in ($ids) $params")->fetchAll();
        if ($d != null) {
            foreach ($d as $key => $value) {
                $t[count($t)] = Prestamo::fromArray($value);
            }
        }
        return $t;
    }

    public function post(array $data): int {
        if($data !== null){
            $p = Prestamo::fromArray($data);
            if (Local::esHijoDe(Equipo::getLocal($p->id_equipo), $this->Raiz)) {
                if($p->id_local_dest != null && Local::esHijoDe(Equipo::getLocal($p->id_local_dest), $this->Raiz)){
                    $id_usuario = Usuario::getId($_SERVER['PHP_AUTH_USER']);
                    $fecha = date_format($p->fecha, "y/m/d H:i:s");
                    $fecha_fin = date_format($p->fecha_fin, "y/m/d H:i:s");
                    if($this->Bd->insertar("prestamos", "'$fecha', '$fecha_fin', '$p->motivo', '$p->recibe', '$p->local_req', $p->id_equipo, $p->id_local_dest, $p->id_estado, $id_usuario", "fecha, fecha_fin, motivo, recibe, local_req, id_equipo, id_local_dest, id_estado, id_usuario_req")){
                        $a = $this->Bd->seleccionar("prestamos", "1 ORDER BY id DESC LIMIT 1", "id")->fetch()['id'];
                        $this->Bd->insertar("logs", "'prestamos', '0', $this->u_actual, $a", "tabla, tipo_cambio, id_usuario, objeto");
                        return $a;
                    }
                }
            }
        }
        
        return 0;
    }
    
    public function put($prestamo, array $data): bool {
        if($prestamo !== null && $data !== null){
            $d = $this->get($prestamo);
            if ($d != null) {
                $p = Prestamo::fromArray($data);
                if (Local::esHijoDe(Prestamo::getLocal($d->id), $this->Raiz)) {
                    if($p->id_local_dest != null && Local::esHijoDe(Equipo::getLocal($p->id_local_dest), $this->Raiz)){
                        $fecha = date_format($p->fecha, "y/m/d H:i:s");
                        $fecha_fin = date_format($p->fecha_fin, "y/m/d H:i:s");
                        if($this->Bd->actualizar("prestamos", "fecha = '$fecha', fecha_fin = '$fecha_fin', motivo = '$p->motivo', recibe = '$p->recibe', local_req = '$p->local_req', id_equipo = $p->id_equipo, id_local_dest = $p->id_local_dest, id_estado = $p->id_estado", "id = $d->id")){
                            $this->Bd->insertar("logs", "'prestamos', '2', $this->u_actual, $p->id", "tabla, tipo_cambio, id_usuario, objeto");
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function delete($prestamo): bool {
        if($prestamo !== null){
            $d = $this->get($prestamo);
            if ($d != null) {
                if (Local::esHijoDe(Prestamo::getLocal($d->id), $this->Raiz)) {
                    if($this->Bd->eliminar("prestamos", "id = '$d->id'")){
                        $this->Bd->insertar("logs", "'prestamos', '3', $this->u_actual, $d->id", "tabla, tipo_cambio, id_usuario, objeto");
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
<?php

final class equipos {
    private $Bd;
    private $Raiz = null;
    private $u_actual = null;

    public function __construct(string $raiz) {
        $this->Bd = new Bd();
        $this->Raiz = $raiz;
        $this->u_actual = Usuario::getIdActual();
    }

    public function get($equipo = null, $params = null) {
        if($equipo != null) {
            if(is_numeric($equipo)){
                $d = $this->Bd->seleccionar("equipos", "id = $equipo")->fetch();
            }
            
            if ($d != null) {
                $e = Equipo::fromArray($d);
                if (Local::esHijoDe($e->id_local, $this->Raiz)) {
                    return $e;
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

        $d = $this->Bd->seleccionar("equipos", "id_local in ($ids) $params")->fetchAll();
        if ($d != null) {
            foreach ($d as $key => $value) {
                $t[count($t)] = Equipo::fromArray($value);
            }
        }
        return $t;
    }

    public function post(array $data): int {
        if($data !== null){
            $e = Equipo::fromArray($data);
            if (Local::esHijoDe($e->id_local, $this->Raiz)) {
                if($this->Bd->insertar("equipos", "$e->id_local, $e->no_inv, '$e->observaciones', $e->id_marca, $e->id_tipo, $e->id_estado, $e->sello", "id_local, no_inv, observaciones, id_marca, id_tipo, id_estado, sello")){
                    $this->Bd->insertar("logs", "'equipos', '0', $this->u_actual, $e->no_inv", "tabla, tipo_cambio, id_usuario, objeto");
                    return $this->Bd->seleccionar("equipos", "1 ORDER BY id DESC LIMIT 1", "id")->fetch()['id'];
                }
            }
        }
        
        return 0;
    }

    public function put($equipo, array $data): bool {
        if($equipo !== null && $data !== null){
            $d = $this->get($equipo);
            if ($d != null) {
                $e = Equipo::fromArray($data);
                if (Local::esHijoDe($e->id_local, $this->Raiz)) {
                    if($this->Bd->actualizar("equipos", "id_local = $e->id_local, no_inv = $e->no_inv, observaciones = '$e->observaciones', id_marca = $e->id_marca, id_tipo = $e->id_tipo, id_estado = $e->id_estado, sello = $e->sello", "id = $d->id")){
                        $this->Bd->insertar("logs", "'equipos', '2', $this->u_actual, $e->no_inv", "tabla, tipo_cambio, id_usuario, objeto");
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function delete($equipo): bool {
        if($equipo !== null){
            $d = $this->get($equipo);
            if ($d != null) {
                if($d->equipo == $_SERVER['PHP_AUTH_USER']){
                    throw new ForbiddenException("No se puede eliminar  el equipo actual.", 1);
                }
                if (Local::esHijoDe($d->id_local, $this->Raiz)) {
                    if($this->Bd->eliminar("equipos", "id = '$d->id'")){
                        $this->Bd->insertar("logs", "'equipos', '3', $this->u_actual, $d->no_inv", "tabla, tipo_cambio, id_usuario, objeto");
                        return true;
                    }
                }
                
            }
        }
        return false;
    }
}
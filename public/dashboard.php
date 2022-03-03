<?php

final class dashboard {
    private $Bd;
    private $Raiz = null;

    public function __construct(string $raiz) {
        $this->Bd = new Bd();
        $this->Raiz = $raiz;
    }

    public function get($a = null, $params = null) {
        $l = Local::obtTodos($this->Raiz);
        $ids = null;
        for ($i=0; $i < count($l); $i++) { 
            $ids = $ids.$l[$i]->id;
            if($i < count($l)-1){
                $ids = $ids.", ";
            }
        }

        $c_u = $this->Bd->contarRegistros("usuarios", "id_local in ($ids)");
        $c_c = $this->Bd->contarRegistros("computadoras", "id_local in ($ids)");
        $c_r = $this->Bd->seleccionar("reportes inner join equipos on (reportes.id_equipo = equipos.id)", "equipos.id_local in ($ids)", "count(reportes.id) as cant")->fetch()['cant'];
        $c_p_h = $this->Bd->seleccionar("prestamos inner join equipos on (prestamos.id_equipo = equipos.id)", "equipos.id_local in ($ids)", "count(prestamos.id) as cant")->fetch()['cant'];
        $c_p_r = $this->Bd->contarRegistros("prestamos", "id_local_dest in ($ids)");
        
        $t = new CDashboard($c_u, $c_c, $c_r, $c_p_h, $c_p_r);
        return $t;
    }

    public function post(array $data): int {
        return 0;
    }

    public function put($a, array $data): bool {
        return false;
    }

    public function delete($a): bool {
        return false;
    }
}
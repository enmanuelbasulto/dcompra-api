<?php

final class CDashboard {
    public $cantidadUsuarios = 0;
    public $cantidadComputadoras = 0;
    public $cantidadReportes = 0;
    public $cantidadPrestamosHechos = 0;
    public $cantidadPrestamosRecibidos = 0;
    
    public function __construct(int $cantidadUsuarios = 0, int $cantidadComputadoras = 0, int $cantidadReportes = 0, int $cantidadPrestamosHechos = 0, int $cantidadPrestamosRecibidos = 0) {
        $this->cantidadUsuarios = $cantidadUsuarios;
        $this->cantidadComputadoras = $cantidadComputadoras;
        $this->cantidadReportes = $cantidadReportes;
        $this->cantidadPrestamosHechos = $cantidadPrestamosHechos;
        $this->cantidadPrestamosRecibidos = $cantidadPrestamosRecibidos;
    }

    public static function fromArray(array $data): CDashboard {
        return new CDashboard($data['cantidadUsuarios'], $data['cantidadComputadoras'], $data['cantidadReportes'], $data['cantidadPrestamosHechos'], $data['cantidadPrestamosRecibidos']);
    }
}
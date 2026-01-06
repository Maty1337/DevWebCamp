<?php 

namespace Controllers; 

use Model\Ponente;
use Model\EventoHorario;

class APIPonentes {
    public static function index() {
        // Lógica para obtener los horarios de eventos
        $ponentes = Ponente::all() ?? [];
        echo json_encode($ponentes);
    }

    public static function ponente(){
        $id = $_GET['id'] ?? null;
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if(!$id || $id < 1) {
            echo json_encode([]);
            return;
        }

        $ponente = Ponente::find($id);
        echo json_encode($ponente, JSON_UNESCAPED_SLASHES);
    }
}
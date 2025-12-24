<?php

namespace CrudGabit\Controladores;

use CrudGabit\Config\Auth;
use CrudGabit\Config\Session;
use CrudGabit\Config\Request;
use CrudGabit\Modelos\Logro;
use CrudGabit\Modelos\Usuario;
use CrudGabit\Modelos\Habito;


class DashboardController extends BaseController
{

    /**
     * Mostrar dashboard
     * @return void
     */
    public function showDashboard(): void
    {
        if (!Session::active()) {
            Request::redirect("/login");
        }
        echo $this->render("dashboard.twig", [
            "stats" => $this->getStats(),
        ]);
    }

    /**
     * Obtener estadísticas para el dashboard
     * @return array
     */
    private function getStats(): array
    {
        $totalUsuarios = count(Usuario::getAllUsers());
        $totalHabitos = count(Habito::getHabitsByUser());
        $totalLogros = count(Logro::getLogrosByUser());

        return [
            "totalUsuarios" => $totalUsuarios,
            "totalHabitos" => $totalHabitos,
            "totalLogros" => $totalLogros
        ];
    }
}
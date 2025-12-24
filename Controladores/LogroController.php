<?php

namespace CrudGabit\Controladores;

use CrudGabit\Modelos\Habito;
use CrudGabit\Modelos\Logro;
use CrudGabit\Config\Request;
use CrudGabit\Config\Session;

class LogroController extends BaseController
{

    /**
     * Muestra la lista de logros del usuario.
     * @return void
     *
     */
    public function index(): void
    {
        $search = Request::get("search");
        $autor = Session::get("id");

        if ($search) {
            $logros = Logro::search($search, $autor);
        } else {
            $logros = Logro::getLogrosByUser();
        }

        $success = Session::get("success");
        Session::delete("success");

        echo $this->render("achievements/index.twig", [
            "logros" => $logros,
            "success" => $success,
            "search" => $search
        ]);
    }

    /**
     * Elimina un logro por su ID.
     * @return void
     */
    public function deleteLogro(): void
    {
        $idLogro = (int)Request::post("idLogro");
        Logro::deleteLogroById($idLogro);

        Session::set("success", "Logro eliminado correctamente.");

        Request::redirect("/crudGabit/achievements");
    }

    /**
     * Muestra el formulario de edición de un logro
     * @return void
     */
    public function showEdit(): void
    {
        // Intento obtener el ID desde POST (botón editar) o desde la sesión
        $idLogro = (int)Request::post("idLogro");
        if (!$idLogro) {
            $idLogro = Session::get("logro_edit_id");
        } else {
            // Guardamos en sesión para futuras llamadas
            Session::set("logro_edit_id", $idLogro);
        }

        // Si no hay ID válido, redirigimos al listado
        if (!$idLogro) {
            Request::redirect("/crudGabit/achievements");
        }

        // Obtenemos los datos del logro
        $logro = Logro::getById($idLogro);

        // Mensaje de éxito si existe
        $success = Session::get("success");
        Session::delete("success");

        echo $this->render("achievements/edit.twig", [
            "logro" => $logro,
            "success" => $success
        ]);
    }



    /**
     * Actualiza un logro con los datos del formulario.
     * @return void
     */
    public function updateLogro(): void
    {
        $idLogro = (int)Request::post("idLogro");
        $nombreLogro = Request::post("nombre");
        $descripcion = Request::post("descripcion");

        // Actualizo en la BD
        Logro::actualizarLogro($idLogro, $nombreLogro, $descripcion);

        // Guardo mensaje de éxito y ID en sesión para la redirección a la misma página
        Session::set("success", "Logro actualizado correctamente.");
        Session::set("logro_edit_id", $idLogro);

        // Redirijo a la misma página de edición
        Request::redirect("/crudGabit/achievements/edit");
    }



    /**
     * Muestra el formulario para crear un nuevo logro.
     * @return void
     */
    public function showCreate(): void
    {
        $habitosDisponibles = Habito::getHabitsByUser();

        // Recupero mensaje de éxito si existe
        $success = Session::get("success");
        Session::delete("success");

        echo $this->render("achievements/create.twig", [
            "habitos" => $habitosDisponibles,
            "success" => $success
        ]);
    }


    /**
     * Crea un nuevo logro con los datos del formulario.
     * @return void
     */
    public function createLogro(): void
    {
        $nombreLogro = Request::post("nombreLogro");
        $descripcion = Request::post("descripcion");
        $habito = Request::post("idCamino");

        $logro = Logro::create($nombreLogro, $descripcion, $habito);
        $logro->insertarLogroEnBD();

        // Guardo mensaje de éxito en sesión
        Session::set("success", "Logro creado correctamente.");

        // Redirigimos a la misma página de creación
        Request::redirect("/crudGabit/achievements/create");
    }


}
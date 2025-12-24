<?php

require_once __DIR__ . '/../vendor/autoload.php';

use CrudGabit\Config\Router;
use CrudGabit\Controladores\AuthController;
use CrudGabit\Config\Session;
use CrudGabit\Config\Request;
use CrudGabit\Controladores\DashboardController;
use CrudGabit\Controladores\UserController;
use CrudGabit\Controladores\HabitController;
use CrudGabit\Controladores\LogroController;

$router = new Router("/crudGabit");

//Rutas de autenticación
$router->get("/", [AuthController::class, "showLogin"]);
$router->get("/login", [AuthController::class, "showLogin"]);
$router->post("/login", [AuthController::class, "login"]);
$router->get("/register", [AuthController::class, "showRegister"]);
$router->post("/register", [AuthController::class, "register"]);
$router->get("/logout", [AuthController::class, "logout"]);

//Rutas del dashboard
$router->get("/dashboard" , [DashboardController::class, "showDashboard"]) ;

//Rutas de gestión de usuarios
$router->get("/users", [UserController::class, "index"] );
$router->post("/users/delete", [UserController::class, "borrarUsuario"] );
$router->post("/users/edit", [UserController::class, "showEdit"]);
$router->get("/users/edit", [UserController::class, "showEdit"]);
$router->post("/users/update", [UserController::class, "editarUsuario"]);
$router->get("/users/create", [UserController::class, "showCreate"]);
$router->post("/users/create", [UserController::class, "createUser"]);


//Rutas de gestión de hábitos
$router->get("/habits", [HabitController::class, "index"] );
$router->post("/habits/delete", [HabitController::class, "deleteHabit"]);
$router->get("/habits/edit", [HabitController::class, "showEdit"]);
$router->post("/habits/edit", [HabitController::class, "showEdit"]);
$router->post("/habits/update", [HabitController::class, "updateHabit"]);
$router->get("/habits/create", [HabitController::class, "showCreate"]);
$router->post("/habits/create", [HabitController::class, "createHabit"]);

//Rutas de gestión de logros
$router->get("/achievements", [LogroController::class, "index"] );
$router->post("/achievements/delete", [LogroController::class, "deleteLogro"]);
$router->post("/achievements/update", [LogroController::class, "updateLogro"]);
$router->post("/achievements/edit", [LogroController::class, "showEdit"]);
$router->get("/achievements/edit", [LogroController::class, "showEdit"]);
$router->get("/achievements/create", [LogroController::class, "showCreate"]);
$router->post("/achievements/create", [LogroController::class, "createLogro"]);


// Ejecutar el router
$router->run();
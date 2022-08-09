<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/includes/app.php';

use App\Http\Router;

// Inicia o Router
$obRouter = new Router(URL);

// Inclui as Rotas de páginas
include __DIR__.'/routes/pages.php';

// Inclui as Rotas do painel
include __DIR__.'/routes/admin.php';

// Inclui as Rotas da API
include __DIR__.'/routes/api.php';

// Imprime o response da rota
$obRouter->run()->sendResponse();

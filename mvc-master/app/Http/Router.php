<?php

namespace App\Http;

use \Closure;
use \Exception;
use \ReflectionFunction;
use \App\Http\Middleware\Queue as MiddlewareQueue;

class Router{

    /**
     * URl completa do projeto
     * @var string
     */
    private $url = '';

    /**
     * Prefixo de todas as rotas
     * @var string
     */
    private $prefix = '';


    /**
     * Indice de rotas
     * @var array
     */
    private $routes = [];

    /**
     * Instancia de Request
     * @var Request
     */
    private $request;

    /**
     * Content type padrão do response
     * @var string
     */
    private $contentType = 'text/html';

    /**
     * Método responsável por iniciar a classe
     * @param string $url
     */
    public function __construct($url){
        $this->request = new Request($this);
        $this->url = $url;
        $this->setPrefix();
    }

    /**
     * Método responsável por atribuir o valor do contentType
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
      $this->contentType = $contentType;
    }

    /**
     * Método responsável por definir o prefixo das rotas
     */
    public function setPrefix(){
        $parseUrl = parse_url($this->url);

        $this->prefix = $parseUrl['path'] ?? '';
    }

    /**
     * Método responsável por adicionar uma rota na classe
     * @param string $method
     * @param string $route
     * @param array  $params
     */
    private function addRoute($method, $route, $params = []){

        foreach ($params as $key => $value) {
            if ($value instanceof Closure) {
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }

        // Middlewares da rota
        $params['middlewares'] = $params['middlewares'] ?? [];

        // Variáveis da rota
        $params['variables'] = [];

        // Padrão de validação das variáveis das Rotas
        $patternVariables = '/{(.*?)}/';
        if (preg_match_all($patternVariables, $route, $matches)) {
            $route = preg_replace($patternVariables, '(.*?)', $route);
            $params['variables'] = $matches[1];
        }
        
        //Remove Barra no final da rota
        $route = rtrim($route,'/');

        //padrao de validação da URL
        $patternRoute = '/^'.str_replace('/', '\\/', $route).'$/';

        $this->routes[$patternRoute][$method] = $params;
    }

    /**
     * Método responsável por definir um rota de GET
     * @param  string $route
     * @param  array  $params
     */
    public function get($route, $params = []){
        return $this->addRoute('GET', $route, $params);
    }

    /**
     * Método responsável por definir um rota de POST
     * @param  string $route
     * @param  array  $params
     */
    public function post($route, $params = []){
        return $this->addRoute('POST', $route, $params);
    }

    /**
     * Método responsável por definir um rota de PUT
     * @param  string $route
     * @param  array  $params
     */
    public function put($route, $params = []){
        return $this->addRoute('PUT', $route, $params);
    }

    /**
     * Método responsável por definir um rota de DELETE
     * @param  string $route
     * @param  array  $params
     */
    public function delete($route, $params = []){
        return $this->addRoute('DELETE', $route, $params);
    }


    /**
     * Método responsável por retornar a URI desconsidenrando o prefixo
     * @return string
     */
    private function getUri(){
        $uri = $this->request->getUri();

        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

        return rtrim(end($xUri), '/');
    }


    /**
     * Método responsável por retornar os dados da rota atual
     * @return array
     */
    private function getRoute()
    {
        // URI
        $uri = $this->getUri();

        // METHOD
        $httpMethod = $this->request->getHttpMethod();

        // Valida as rotas
        foreach ($this->routes as $patternRoute => $methods) {
            // Verifica se a URI bate com o padrão
            if (preg_match($patternRoute, $uri, $matches)) {
                // Verifica o método
                if (isset($methods[$httpMethod])) {
                    // Remove a primeira posição
                    unset($matches[0]);

                    // Variáveis processadas
                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;

                    // Retorno dos parâmetros da rota
                    return $methods[$httpMethod];
                }

                // Método não permitido/definido
                throw new Exception("Método não permitido", 405);
            }
        }

            // URL não encontrada
            throw new Exception("URL não encontrada", 404);
    }

    /**
     * Método responsável por executar a rota atual
     * @return Response
     */
    public function run(){
        try {
            // Obtém a rota atual
            $route = $this->getRoute();

            // Verifica o controlador
            if (!isset($route['controller'])) {
                throw new Exception("A URL não pôde ser processada", 500);
            }

            // Argumentos da função
            $args = [];

            // Reflecion
            $reflection = new ReflectionFunction($route['controller']);
            foreach ($reflection->getParameters() as $parameter) {
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? '';

            }

            // Retorna a execução da fila de middlewares
            return (new MiddlewareQueue($route['middlewares'], $route['controller'], $args))->next($this->request);

        } catch(Exception $e) {
            return new Response($e->getCode(), $this->getErrorMessage($e->getMessage()), $this->contentType);
        }
    }

    /**
     * Método responsável por retornar a mensagem de erro de acordo com o contentType
     * @param  string $message
     * @return mixed
     */
    private function getErrorMessage($message)
    {
      switch($this->contentType){
        case 'application/json':
          return ['error' => $message];
          break;

        default:
          return $message;
          break;
      }
    }

    /**
     * Método responsável por retornar a URL atual
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->url.$this->getUri();
    }

    /**
     * Método responsável por redirecionar a URL
     * @param  string $route
     */
    public function redirect($route)
    {
        $url = $this->url.$route;

        // Executa o redirect
        header('location: '.$url);
        exit();
    }
}

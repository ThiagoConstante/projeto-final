<?php
namespace App\Http;

class Request{

    /**
     * Instância do Router
     * @var Router
     */
    private $router;

    /**
    * Método HTTP da requisição
    * @var string
    */
    private $httpMethod;

    /**
    * URI da página
    * @var string
    */
    private $uri;

    /**
    * Parâmentros da URL ($_GET)
    * @var array
    */
    private $queryParams = [];

    /**
    * Variaveis recebidas no POST da página ($_POST)
    * @var array
    */
    private $postVars = [];

    /**
    * Cabeçalho da requisição
    * @var array
    */
    private $headers = [];

    public function __construct($router)
    {
        $this->router = $router;
        $this->queryParams = $_GET ?? [];
        $this->headers     = getallheaders();
        $this->httpMethod  = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->setUri();
        $this->setPostVars();
    }

    /**
     * Método responsável por definir as variáveis do POST
     */
    private function setPostVars()
    {
      if ($this->httpMethod == 'GET') {
        return false;
      }

      $this->postVars = $_POST ?? [];

      $inputRaw = file_get_contents('php://input');
      $this->postVars = (strlen($inputRaw) && empty($_POST)) ? json_decode($inputRaw, true) : $this->postVars;
    }

    /**
     * Método Responsável por definir a URI
     */
    public function setUri()
    {
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';

        $xURI = explode('?', $this->uri);
        $this->uri = $xURI[0];
    }

    /**
     * Método responsável por retornar a instâcia Router
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Método responsável por retornar o método HTTP da requisição
     * @return string
     */
    public function gethttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * Método responsável por retornar a URI da requisição
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Método responsável por retornar os headers da requisição
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Método responsável por retornar os parâmetros da URL da requisição
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * Método responsável por retornar as variáveis POST da requisição
     * @return array
     */
    public function getPostVars()
    {
        return $this->postVars;
    }
}

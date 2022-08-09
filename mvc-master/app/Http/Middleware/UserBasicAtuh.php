<?php

namespace App\Http\Middleware;

use \App\Model\Entity\User;

class UserBasicAuth
{

    /**
     * Método responsável por retornar uma instancia de usuário autenticado
     * @return User
     */
    private function getBasicUser()
    {
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
          return false;
        }

        $obUser = User::getUserByEmail($_SERVER['PHP_AUTH_USER']);

        if (!$obUser instanceof User) {
          return false;
        }

        return password_verify($_SERVER['PHP_AUTH_PW'], $obUser->senha) ? $obUser : false;
    }

    /**
     * Método rsponsável por validar o acesso via HTTP Basic Auth
     * @param  Request $request
     */
    private function basicAuth($request)
    {
        if ($obUser = $this->getBasicUser()) {
          $request->user = $obUser;
          return true;
        }

        throw new \Exception("Usuário ou senha inválidos.", 403);
    }

    /**
     * Método responsável por executar o middleware
     * @param  Request $request
     * @param  Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        // Realiza a validação do acesso via basic auth
        $this->basicAuth($request);

        // Executa o próximo nível do middleware
        return $next($request);
    }
}

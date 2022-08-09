<?php

namespace App\Http\Middleware;

use \App\Model\Entity\User;
use \Firebase\JWT\JWT;

class JWTAuth
{

    /**
     * Método responsável por retornar uma instancia de usuário autenticado
     * @param Request $request
     * @return User
     */
    private function getJWTAuthUser($request){
        //Headers
        $headers = $request->getHeaders();
        //Token puro em JWT
        $jwt = isset($headers['Authorization']) ? str_replace('Bearer ','',$headers['Authorization']) : '';

        try{
        //Decode
        $decode = (array)JWT::decode($jwt,getenv('JWT_Key'),['HS256']);
        }catch(\exception $e){
          throw new \Exception("Token inválido", 403);
        }

        //Email
        $email = $decode['email'] ?? '';
        //Busca o usuário pelo email
        $obUser = User::getUserByEmail($email);
        //Retorna o usuário
        return $obUser instanceof User ? $obUser : false;
        }
    /**
     * Método responsável por validar o acesso via JWT
     * @param  Request $request
     */
    private function auth($request){
    //Verifica o usuário recebido
        if ($obUser = $this->getJWTAuthUser($request)) {
          $request->user = $obUser;
          return true;
        }

        throw new \Exception("Acesso Negado.", 403);
    }

    /**
     * Método responsável por executar o middleware
     * @param  Request $request
     * @param  Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        // Realiza a validação do acesso JWT
        $this->auth($request);

        // Executa o próximo nível do middleware
        return $next($request);
    }
}

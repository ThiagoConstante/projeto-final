<?php

namespace App\Http\Middleware;

class Cache{
    
    /**
     * Método resposável por verificar se a request atual pode ser cacheada
     * @param Request $request
     * @return boolean
     */
    private function iscacheable($request){
        //Valida o tempo de cache
        if(getenv('CACHE_TIME') <= 0){
            return false;
        }

        //Valida o método da requisição
        if($request->getHttpMethod() != 'GET'){
            return false;
        }

        //Valida o header de cache
        $headers = $request->getHeaders();
        if(isset($headers['Cache-Control']) and $headers['Cache-Control'] == 'no-cache'){
            return false;
        }
        //Cacheável
        return true;
}

    /**
     * Método responsável por executar o middleware
     * @param  Request $request
     * @param  Closure $next
     * @return Response
     */
    public function handle($request, $next){ 
        //Verifica se a request atual é cacheada
        if(!$this->iscacheable()) return $next($request);

        die('cache');
    }
}

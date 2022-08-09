<?

namespace APP\Controller\Api;

use \APP\Model\Entity\User;

class Auth extends Api{
    /**
     * *Método responsável por gerar um token JWT
     * @param Request $request
     * @return array
     */
    public static function generateToken($request){
        //POST VARS
        $postVars = $request->getPostVars();
        
        //Valida os campos obrigatórios
        if(!isset($postVars['email']) or !isset($postVars['senha'])){
            throw new \exception("Os campos 'email' e 'senha' são obrigatórios", 400);
        }

        //Busca usuários pelo email
        $obUser = User::getUserbyEmail($postVars['email']);
        if(!$obUser instanceof User){
            throw new \exception("O usuário ou senha são inválidos", 400);
        }

        //Valida a senha do usuário
        if(password_verify($postVars['senha'],$obUser->senha)){
            throw new \exception("O usuário ou senha são inválidos", 400);
        }

        //Payload
        $payload = [
            'email' => $obUser->email
        ];
        //Retorna o Token gerado
        return [
            'token' => JWT::encode($payload,getenv('JWT_KEY'))
        ];
    }
}
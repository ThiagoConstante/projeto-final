<?php

namespace App\Session\Admin;

class Login
{

    /**
     * Método responsável por iniciar a sessão
     */
    public static function init()
    {
        // Verifica se a sessão não está ativa
        if (session_status() != PHP_SESSION_ACTIVE){
            session_start();
        }
    }

    /**
     * Método responsável por criar o login do usuário
     * @param  User $obUser
     * @return boolean
     */
    public static function login($obUser)
    {

        // Inicia a sessão
        self::init();

        // Define a sessão do usuário
        $_SESSION['admin']['usuario'] = [
            'id' => $obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];

        return true;
    }

    /**
     * Método responsável por verificar se o usuário está logado
     * @return boolean
     */
    public static function isLogged()
    {
        // Inicia a sessão
        self::init();

        return isset($_SESSION['admin']['usuario']['id']);
    }

    /**
     * Método responsável por executar o logout do usuário
     * @return boolean
     */
    public static function logout()
    {
        // Inicia a sessão
        self::init();

        // Desloga o usuário
        unset($_SESSION['admin']['usuario']);

        return true;
    }
}

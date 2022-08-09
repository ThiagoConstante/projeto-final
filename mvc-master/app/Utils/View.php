<?php

namespace App\Utils;

class View{

    /**
     * Variáveis padões da View
     * @var array
     */
    private static $vars = [];

    /**
     * Método responsável por definir os dados iniciais da classe
     * @param  array  $vars
     */
    public static function init($vars = []){
        self::$vars = $vars;
    }

    /**
    * Método responsavel por retornar o conteúdo de uma View
    * @param string $view
    * @return string
    */
    private static function getContentView($view){
        $file = __DIR__.'/../../resources/view/'.$view.'.html';
        return file_exists($file) ? file_get_contents($file) : '';
    }

    /**
    * Método responsavel por retornar o conteúdo renderizado de uma View
    * @param string $view
    * @param array $vars (string/numeric)
    * @return string
    */
    public static function render($view, $vars = []){

        $contentView = self::getContentView($view);

        $vars = array_merge(self::$vars, $vars);

        $keys = array_keys($vars);
        $keys = array_map(function($item){
            return '{{'.$item.'}}';
        }, $keys);

        return str_replace($keys, array_values($vars), $contentView);
    }
}

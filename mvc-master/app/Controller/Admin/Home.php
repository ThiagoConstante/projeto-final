<?php

namespace App\Controller\Admin;

use \App\Utils\View;

class Home extends Page
{
  /**
   * Método responsável por renderizar a view do painel
   * @param  Request $request
   * @return string
   */
    public static function getHome($request)
    {
      //Conteúdo da Home
      $content = View::render('admin/modules/home/index', []);

      // Retorna a página completa
      return parent::getPanel('Home > WDEV', $content, 'home');
    }

}

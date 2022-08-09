<?php

namespace App\Controller\Admin;

use \App\Utils\View;

class Page
{

    /**
    * Módulos disponíveis no painel
    * @var array
    */
    private static $modules = [
                                'home' => ['label' => 'Home', 'link' => URL.'/admin'],
                                'testimonies' => ['label' => 'Depoimentos', 'link' => URL.'/admin/testimonies'],
                                'users' => ['label' => 'Usuários', 'link' => URL.'/admin/users']
                              ];

    /**
     * Método responsável por retornar o conteúdo (view) da estrutura genérica da pagina do painel
     * @param  string $tittle
     * @param  string $content
     * @return string
     */
    public static function getPage($title, $content)
    {
        return View::render('admin/page', [
            'title' => $title,
            'content' => $content
        ]);
    }

    /**
     * Método responsável por renderizar a view do menu do painels
     * @param  string $currentModule
     * @return string
     */
    private static function getMenu($currentModule)
    {
      // Links do Menu
      $links = '';

      // Itera os modulos
      foreach (self::$modules as $hash => $module) {
        $links .= View::render('admin/menu/link', [ 'label' => $module['label'],
                                                    'link' => $module['link'],
                                                    'current' => $hash === $currentModule ? 'text-danger' : ''
                                                  ]);
      }

      // Retorna a renderização do menu
      return View::render('admin/menu/box', ['links' => $links]);
    }

    /**
     * Método responsável por renderizar a view do painel com conteúdos dinâmicos
     * @param  string $tittle
     * @param  string $content
     * @param  string $currentModule
     * @return string
     */
    public static function getPanel($title, $content, $currentModule)
    {
      // Renderiza a view do Painel
      $contentPanel = View::render('admin/panel', [ 'menu' => self::getMenu($currentModule),
                                                    'content' => $content]);

      return self::getPage($title, $contentPanel);
    }

    /**
     * Método responsável por renderizar o layout da paginação
     * @param  Request $request
     * @param  Pagination $obPagination
     * @return string
     */
    public static function getPagination($request, $obPagination)
    {
        $pages = $obPagination->getPages();

        if (count($pages) <= 1){
            return '';
        }

        $links = '';

        $url = $request->getRouter()->getCurrentUrl();

        $queryParams = $request->getQueryParams();

        foreach ($pages as $page) {

            $queryParams['page'] = $page['page'];

            $link = $url.'?'.http_build_query($queryParams);

            $links .= View::render('admin/pagination/link', [
                             'page' => $page['page'],
                             'link' => $link,
                             'active' => $page['current'] ? 'active' : ''
                         ]);

        }

        return View::render('admin/pagination/box', ['links' => $links]);
    }
}

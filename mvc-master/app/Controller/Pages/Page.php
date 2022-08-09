<?php

namespace App\Controller\Pages;

use \App\Utils\View;

class Page{

    /**
    * Método responsável por retornar o topo da página
    * @return string
    */
    private static function getHeader(){
        return View::render('pages/header');
    }

    /**
    * Método responsável por retornar o rodapé da página
    * @return string
    */
    private static function getFooter(){
        return View::render('pages/footer');
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

            $links .= View::render('pages/pagination/link', [
                             'page' => $page['page'],
                             'link' => $link,
                             'active' => $page['current'] ? 'active' : ''
                         ]);

        }

        return View::render('pages/pagination/box', ['links' => $links]);
    }

    /**
    * Método responsável por retornar o conteúdo (view) da nossa pagina genérica
    * @return string
    */
    public static function getPage($title, $content){
        return View::render('pages/page',
            ['title' => $title,
             'header' => self::getHeader(),
             'content' => $content,
             'footer' => self::getFooter()]);
    }

}

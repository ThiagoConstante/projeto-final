<?php

namespace App\Controller\Api;

class Api
{
  /**
   * Método responsável por retornar os detalhes da API
   * @param Request $Request
   * @return array
   */
  public static function getDetails($request)
  {
    return ['nome' => 'API WDEV',
            'versao' => 'v1.0.0',
            'autor' => 'Willian Costa',
            'email' => 'canalwdev@.com'
    ];
  }

  /**
   * Método responsável por retornar detalhes da paginação
   * @param  Request $request
   * @param  Pagination $obPagination
   * @return array
   */
  protected static function getPagination($request, $obPagination)
  {
    $queryParams = $request->getQueryParams();

    $pages = $obPagination->getPages();

    return ['paginaAtual' => isset($queryParams['page']) ? $queryParams['page'] : 1,
            'quantidadePaginas' => !empty($pages) ? count($pages) : 1
           ];
  }

  /**
   * Método responsável por cadastrar um novo depoimento
   * @param Request $request
   */
  // public static function setNewTestimony($request)
  // {
  //   return ['sucesso' => true];
  // }
}

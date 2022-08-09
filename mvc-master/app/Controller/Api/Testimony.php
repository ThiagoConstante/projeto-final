<?php

namespace App\Controller\Api;

use \App\Model\Entity\Testimony as EntityTestimony;
use \WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Api
{

  /**
   * Método responsável por obter a renderização dos itens de depoimentos para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
  public static function getTestimonyItems($request, &$obPagination)
  {
      $items = [];

      $quantidadeTotal = EntityTestimony::getTestimonies(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

      $queryParams = $request->getQueryParams();
      $paginaAtual = $queryParams['page'] ?? 1;

      $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 10);

      $results = EntityTestimony::getTestimonies(null, 'id ASC', $obPagination->getLimit());

      while ($obTestimony = $results->fetchObject(EntityTestimony::class)) {

          $items[] = [
              'id' => (int)$obTestimony->id,
              'nome' => $obTestimony->nome,
              'mensagem' => $obTestimony->mensagem,
              'data' => $obTestimony->data
          ];
      }

      return $items;
  }

  /**
   * Método responsável por retornar os depoimentos cadastrados
   * @param Request $Request
   * @return array
   */
  public static function getTestimonies($request)
  {
    return ['depoimentos' =>  self::getTestimonyItems($request, $obPagination),
            'paginacao' => parent::getPagination($request, $obPagination)
    ];
  }

  /**
   * Método responsável por retornar um depoimento apartir de um id
   * @param  Request $request
   * @param  int $id
   * @return array
   */
  public static function getTestimony($request, $id)
  {
    if (!is_numeric($id)) {
      throw new \Exception("O id {$id} não é válido", 400);
    }
    $obTestimony = EntityTestimony::getTestimonyById($id);

    if (!$obTestimony instanceof EntityTestimony) {
      throw new \Exception("O dempoimento não {$id} foi encontrado", 404);
    }

    return [
            'id' => (int)$obTestimony->id,
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data' => $obTestimony->data
           ];
  }

  /**
   * Método responsável por cadastrar um novo depoimento
   * @param Request $request
   */
  public static function setNewTestimony($request)
  {
    $postVars = $request->getPostVars();

    if (!isset($postVars['nome']) || !isset($postVars['mensagem'])) {
      throw new \Exception("Os campos 'nome' e 'mensagem' são obrigatórios", 400);
    }

    $obTestimony = new EntityTestimony();
    $obTestimony->nome = $postVars['nome'];
    $obTestimony->mensagem = $postVars['mensagem'];
    $obTestimony->cadastrar();


    return [
            'id' => (int)$obTestimony->id,
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data' => $obTestimony->data
           ];
  }

  /**
   * Método responsável por atualizar um depoimento
   * @param Request $request
   */
  public static function setEditTestimony($request, $id)
  {
    $postVars = $request->getPostVars();

    if (!isset($postVars['nome']) || !isset($postVars['mensagem'])) {
      throw new \Exception("Os campos 'nome' e 'mensagem' são obrigatórios", 400);
    }

    $obTestimony = EntityTestimony::getTestimonyById($id);

    if (!$obTestimony instanceof EntityTestimony) {
      throw new \Exception("O depoimento {$id} não foi encontrado.", 404);
    }

    $obTestimony->nome = $postVars['nome'];
    $obTestimony->mensagem = $postVars['mensagem'];
    $obTestimony->atualizar();


    return [
            'id' => (int)$obTestimony->id,
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data' => $obTestimony->data
           ];
  }

  /**
   * Método responsável por apagar um depoimento
   * @param Request $request
   */
  public static function setDeleteTestimony($request, $id)
  {

    $obTestimony = EntityTestimony::getTestimonyById($id);

    if (!$obTestimony instanceof EntityTestimony) {
      throw new \Exception("O depoimento {$id} não foi encontrado.", 404);
    }

    $obTestimony->excluir();


    return ['sucesso' => true];
  }
}

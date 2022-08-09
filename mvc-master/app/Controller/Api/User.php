<?php

namespace App\Controller\Api;

use \App\Model\Entity\User as EntityUser;
use \WilliamCosta\DatabaseManager\Pagination;

class User extends Api
{

  /**
   * Método responsável por obter a renderização dos itens do usuário para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
  public static function getUserItems($request, &$obPagination)
  {
      $items = [];
      $quantidadeTotal = EntityUser::getUsers(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

      $queryParams = $request->getQueryParams();
      $paginaAtual = $queryParams['page'] ?? 1;

      $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 10);

      $results = EntityUser::getUsers(null, 'id ASC', $obPagination->getLimit());

      while ($obUser = $results->fetchObject(EntityUser::class)) {

          $items[] = [
              'id' => (int)$obUser->id,
              'nome' => $obUser->nome,
              'email' => $obUser->email
          ];
      }

      return $items;
  }

  /**
   * Método responsável por retornar os usuários cadastrados
   * @param Request $Request
   * @return array
   */
  public static function getUsers($request)
  {
    return ['usuarios' =>  self::getUserItems($request, $obPagination),
            'paginacao' => parent::getPagination($request, $obPagination)
    ];
  }

  /**
   * Método responsável por retornar os detalhes de um usuário
   * @param  Request $request
   * @param  int $id
   * @return array
   */
  public static function getUser($request, $id)
  {
    if (!is_numeric($id)) {
      throw new \Exception("O id {$id} não é válido", 400);
    }

    $obUser = EntityUser::getUserById($id);

    if (!$obUser instanceof EntityUser) {
      throw new \Exception("O usuário não {$id} foi encontrado", 404);
    }

    return [
        'id' => (int)$obUser->id,
        'nome' => $obUser->nome,
        'email' => $obUser->email
    ];
  }
/**
 * Método resposável por retornar o usuário atualmente conectado
 * @param Request $request
 * @return array
 */ 
  public static function getCurrentUser($request){
    //Usuário atual
    $obUser = $request->user;
    //Retorna os detalhes do depoimento
    return [
      'id' => (int)$obUser->id,
      'nome' => $obUser->nome,
      'email' => $obUser->email
    ];
  }

  /**
   * Método responsável por cadastrar um novo usuário
   * @param Request $request
   */
  public static function setNewUser($request)
  {
    $postVars = $request->getPostVars();

    if (!isset($postVars['nome']) || !isset($postVars['email']) || !isset($postVars['senha'])) {
      throw new \Exception("Os campos 'nome', 'email' e 'senha' são obrigatórios", 400);
    }

    // valida o email do usuário
    $obUser = EntityUser::getUserByEmail($postVars['email']);

    if ($obUser instanceof EntityUser) {
      throw new \Exception("O email {$postVars['email']} já está cadastrado", 400);

    }

    $obUser = new EntityUser();
    $obUser->nome = $postVars['nome'];
    $obUser->email = $postVars['email'];
    $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);
    $obUser->cadastrar();


    return [
        'id' => (int)$obUser->id,
        'nome' => $obUser->nome,
        'email' => $obUser->email,
    ];
  }

  /**
   * Método responsável por atualizar um usuário
   * @param Request $request
   */
  public static function setEditUser($request, $id)
  {
    $postVars = $request->getPostVars();

    if (!isset($postVars['nome']) || !isset($postVars['email']) || !isset($postVars['senha'])) {
      throw new \Exception("Os campos 'nome', 'email' e 'senha' são obrigatórios", 400);
    }

    $obUser = EntityUser::getUserById($id);

    if (!$obUser instanceof EntityUser) {
      throw new \Exception("O id de usuário {$id} não foi encontrado", 404);
    }

    // valida o email do usuário
    $obUserEmail = EntityUser::getUserByEmail($postVars['email']);

    if ($obUserEmail instanceof EntityUser && $obUserEmail->id != $obUser->id) {
      throw new \Exception("O email {$postVars['email']} já está cadastrado", 400);

    }

    $obUser->nome = $postVars['nome'];
    $obUser->email = $postVars['email'];
    $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);
    $obUser->atualizar();


    return [
        'id' => (int)$obUser->id,
        'nome' => $obUser->nome,
        'email' => $obUser->email,
    ];
  }

  /**
   * Método responsável por apagar um usuário
   * @param Request $request
   */
  public static function setDeleteUser($request, $id)
  {

    $obUser = EntityUser::getUserById($id);

    if (!$obUser instanceof EntityUser) {
      throw new \Exception("O usuário {$id} não foi encontrado.", 404);
    }

    // Impede a exclusão do próprio cadastro
    if ($obUser->id == $request->user->id) {
      throw new \Exception("Não é possivel excluir o cadastro atualmente logado.", 400);

    }

    $obUser->excluir();


    return ['sucesso' => true];
  }
}

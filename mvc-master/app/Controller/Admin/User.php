<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\User as EntityUser;
use \WilliamCosta\DatabaseManager\Pagination;

class User extends Page
{

  /**
   * Método responsável por obter a renderização dos itens de usuários para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
  public static function getuserItems($request, &$obPagination)
  {
      $items = '';

      $quantidadeTotal = EntityUser::getUsers(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

      $queryParams = $request->getQueryParams();
      $paginaAtual = $queryParams['page'] ?? 1;

      $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 10);

      $results = EntityUser::getUsers(null, 'id ASC', $obPagination->getLimit());

      while ($obUser = $results->fetchObject(EntityUser::class)) {

          $items .= View::render('admin/modules/users/item', [
              'id' => $obUser->id,
              'nome' => $obUser->nome,
              'email' => $obUser->email
          ]);

      }

      return $items;
  }

   /**
   * Método responsável por renderizar a view de listagem de usuários
   * @param  Request $request
   * @return string
   */
    public static function getUsers($request)
    {
      //Conteúdo da Home
      $content = View::render('admin/modules/users/index', [
        'itens' => self::getUserItems($request, $obPagination),
        'pagination' => parent::getPagination($request, $obPagination),
        'status' => self::getStatus($request)
      ]);

      // Retorna a página completa
      return parent::getPanel('usuarios > WDEV', $content, 'users');
    }

    /**
    * Método responsável por retornar o formulário de cadastro de um novo usuário
    * @param  Request $request
    * @return string
    */
    public static function getNewUser($request)
    {
      //Conteúdo dp formulário
      $content = View::render('admin/modules/users/form', [
        'title' => 'Cadastrar usuário',
        'nome' => '',
        'email' => '',
        'status' => self::getStatus($request)
      ]);

      // Retorna a página completa
      return parent::getPanel('Cadastrar usuário > WDEV', $content, 'users');
    }

    /**
    * Método responsável por cadastrar um novo usuário no banco
    * @param  Request $request
    * @return string
    */
    public static function setNewUser($request)
    {
      $postVars = $request->getPostVars();

      $nome = $postVars['nome'] ?? '';
      $email = $postVars['email'] ?? '';
      $senha = $postVars['senha'] ?? '';

      // valida o email do usuário
      $obUser = EntityUser::getUserByEmail($email);

      if ($obUser instanceof EntityUser) {
        $request->getRouter()->redirect('/admin/users/new?status=duplicated');
      }

      // Nova instância de usuário
      $obUser = new EntityUser();
      $obUser->setNome($nome);
      $obUser->setEmail($email);
      $obUser->setSenha(password_hash($senha, PASSWORD_DEFAULT));
      $obUser->cadastrar();

      // redireciona o usuário
      $request->getRouter()->redirect('/admin/users/'.$obUser->getId().'/edit?status=created');
    }

    /**
    *
    * Método responsável por retornar a mensagem de status
    * @param Request $request
    * @return string
    */
    private static function getStatus($request)
    {
      // Query params
      $queryParams = $request->getQueryParams();

      // verifica se existe status
      switch ($queryParams['status'] ?? '') {
        case 'created':
          return Alert::getSuccess('Usuário criado com sucesso!');
          break;

          case 'updated':
          return Alert::getSuccess('Usuário atualizado com sucesso!');
          break;

          case 'deleted':
          return Alert::getSuccess('Usuário excluído com sucesso!');
          break;

          case 'duplicated':
          return Alert::getError('Email já cadastrado!');
          break;
      }
    }

    /**
    * Método responsável por retornar o formulario de edição de um usuário
    * @param  Request $request
    * @param  integer $id
    * @return string
    */
    public static function getEditUser($request, $id)
    {
      // Obtém o usuário do banco de dados
      $obUser = EntityUser::getUserById($id);

      // valida a instância
      if (!$obUser instanceof EntityUser) {
        $request->getRouter()->redirect('/admin/users');
      }

      //Conteúdo do formulário
      $content = View::render('admin/modules/users/form', [
        'title' => 'Editar Usuário',
        'nome' => $obUser->getNome(),
        'email' => $obUser->getEmail(),
        'status' => self::getStatus($request)
      ]);

      // Retorna a página completa
      return parent::getPanel('Editar usuário > WDEV', $content, 'users');
    }

    /**
    * Método responsável por gravar a atualização de um usuário
    * @param  Request $request
    * @param  integer $id
    * @return string
    */
    public static function setEditUser($request, $id)
    {
      // Obtém o usuário do banco de dados
      $obUser = EntityUser::getUserById($id);

      // valida a instância
      if (!$obUser instanceof EntityUser) {
        $request->getRouter()->redirect('/admin/users');
      }

      // PostVars
      $postVars = $request->getPostVars();
      $nome = $postVars['nome'] ?? '';
      $email = $postVars['email'] ?? '';
      $senha = $postVars['senha'] ?? '';

      // valida o email do usuário
      $obUserEmail = EntityUser::getUserByEmail($email);

      if ($obUserEmail instanceof EntityUser && $obUserEmail->getId() != $id) {
        $request->getRouter()->redirect('/admin/users/'.$id.'/edit?status=duplicated');
      }

      // Atualiza a instância
      $obUser->nome = $nome;
      $obUser->email = $email;
      $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
      $obUser->atualizar();

      // redireciona o usuário
      $request->getRouter()->redirect('/admin/users/'.$obUser->getId().'/edit?status=updated');

    }

    /**
    * Método responsável por retornar o formulario de exclusão de usuário
    * @param  Request $request
    * @param  integer $id
    * @return string
    */
    public static function getDeleteUser($request, $id)
    {
      // Obtém o usuário do banco de dados
      $obUser = EntityUser::getUserById($id);

      // valida a instância
      if (!$obUser instanceof EntityUser) {
        $request->getRouter()->redirect('/admin/users');
      }

      //Conteúdo dp formulário
      $content = View::render('admin/modules/users/delete', [
        'nome' => $obUser->nome,
        'email' => $obUser->email
      ]);

      // Retorna a página completa
      return parent::getPanel('Excluir usuário > WDEV', $content, 'users');
    }

    /**
    * Método responsável por excluir um usuário
    * @param  integer $id
    * @return string
    */
    public static function setDeleteUser($request, $id)
    {
      // Obtém o usuário do banco de dados
      $obUser = EntityUser::getUserById($id);

      // valida a instância
      if (!$obUser instanceof EntityUser) {
        $request->getRouter()->redirect('/admin/users');
      }

      // Exclui o usuário
      $obUser->excluir();

      // redireciona o usuário
      $request->getRouter()->redirect('/admin/users?status=deleted');

    }

}

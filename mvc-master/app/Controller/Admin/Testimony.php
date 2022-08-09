<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Testimony as EntityTestimony;
use \WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Page
{

  /**
   * Método responsável por obter a renderização dos itens de depoimentos para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
  public static function getTestimonyItems($request, &$obPagination)
  {
      $items = '';

      $quantidadeTotal = EntityTestimony::getTestimonies(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

      $queryParams = $request->getQueryParams();
      $paginaAtual = $queryParams['page'] ?? 1;

      $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 10);

      $results = EntityTestimony::getTestimonies(null, 'id ASC', $obPagination->getLimit());

      while ($obTestimony = $results->fetchObject(EntityTestimony::class)) {

          $items .= View::render('admin/modules/testimonies/item', [
              'id' => $obTestimony->id,
              'nome' => $obTestimony->nome,
              'mensagem' => $obTestimony->mensagem,
              'data' => date('d/m/Y - H:i:s', strtotime($obTestimony->data))
          ]);

      }

      return $items;
  }

   /**
   * Método responsável por renderizar a view de listagem de depoimentos
   * @param  Request $request
   * @return string
   */
    public static function getTestimonies($request)
    {
      //Conteúdo da Home
      $content = View::render('admin/modules/testimonies/index', [
        'itens' => self::getTestimonyItems($request, $obPagination),
        'pagination' => parent::getPagination($request, $obPagination),
        'status' => self::getStatus($request)
      ]);

      // Retorna a página completa
      return parent::getPanel('Depoimentos > WDEV', $content, 'testimonies');
    }

    /**
    * Método responsável por retornar o formulário de cadastro de um novo depoimento
    * @param  Request $request
    * @return string
    */
    public static function getNewTestimony()
    {
      //Conteúdo dp formulário
      $content = View::render('admin/modules/testimonies/form', [
        'title' => 'Cadastrar Depoimento',
        'nome' => '',
        'mensagem' => '',
        'status' => ''
      ]);

      // Retorna a página completa
      return parent::getPanel('Cadastrar depoimento > WDEV', $content, 'testimonies');
    }

    /**
    * Método responsável por retornar o formulario de cadastro de um novo depoimento
    * @param  Request $request
    * @return string
    */
    public static function setNewTestimony($request)
    {
      $postVars = $request->getPostVars();

      // Nova instancia de depoimento
      $obTestimony = new EntityTestimony();
      $obTestimony->nome = $postVars['nome'] ?? '';
      $obTestimony->mensagem = $postVars['mensagem'] ?? '';
      $obTestimony->Cadastrar();

      // redireciona o usuário
      $request->getRouter()->redirect('/admin/testimonies/'.$obTestimony->id.'/edit?status=created');
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
          return Alert::getSuccess('Depoimento criado com sucesso!');
          break;

          case 'updated':
          return Alert::getSuccess('Depoimento atualizado com sucesso!');
          break;

          case 'deleted':
          return Alert::getSuccess('Depoimento excluído com sucesso!');
          break;
        default:
          // code...
          break;
      }
    }

    /**
    * Método responsável por retornar o formulario de edição de um depoimento
    * @param  Request $request
    * @param  integer $id
    * @return string
    */
    public static function getEditTestimony($request, $id)
    {
      // Obtém o depoimento do banco de dados
      $obTestimony = EntityTestimony::getTestimonyById($id);

      // valida a instancia
      if (!$obTestimony instanceof EntityTestimony) {
        $request->getRouter()->redirect('/admin/testimonies');
      }

      //Conteúdo dp formulário
      $content = View::render('admin/modules/testimonies/form', [
        'title' => 'Editar Depoimento',
        'nome' => $obTestimony->nome,
        'mensagem' => $obTestimony->mensagem,
        'status' => self::getStatus($request)
      ]);

      // Retorna a página completa
      return parent::getPanel('Editar depoimento > WDEV', $content, 'testimonies');
    }

    /**
    * Método responsável por gravar a atualização de um depoimento
    * @param  Request $request
    * @param  integer $id
    * @return string
    */
    public static function setEditTestimony($request, $id)
    {
      // Obtém o depoimento do banco de dados
      $obTestimony = EntityTestimony::getTestimonyById($id);

      // valida a instancia
      if (!$obTestimony instanceof EntityTestimony) {
        $request->getRouter()->redirect('/admin/testimonies');
      }

      // PostVars
      $postVars = $request->getPostVars();

      // Atualiza a instancia
      $obTestimony->nome  = $postVars['nome'] ?? $obTestimony->nome;
      $obTestimony->mensagem  = $postVars['mensagem'] ?? $obTestimony->mensagem;
      $obTestimony->atualizar();

      // redireciona o usuário
      $request->getRouter()->redirect('/admin/testimonies/'.$obTestimony->id.'/edit?status=updated');

    }

    /**
    * Método responsável por retornar o formulario de exclusão de depoimento
    * @param  Request $request
    * @param  integer $id
    * @return string
    */
    public static function getDeleteTestimony($request, $id)
    {
      // Obtém o depoimento do banco de dados
      $obTestimony = EntityTestimony::getTestimonyById($id);

      // valida a instancia
      if (!$obTestimony instanceof EntityTestimony) {
        $request->getRouter()->redirect('/admin/testimonies');
      }

      //Conteúdo dp formulário
      $content = View::render('admin/modules/testimonies/delete', [
        'nome' => $obTestimony->nome,
        'mensagem' => $obTestimony->mensagem
      ]);

      // Retorna a página completa
      return parent::getPanel('Excluir depoimento > WDEV', $content, 'testimonies');
    }

    /**
    * Método responsável por excluir um depoimento
    * @param  Request $request
    * @param  integer $id
    * @return string
    */
    public static function setDeleteTestimony($request, $id)
    {
      // Obtém o depoimento do banco de dados
      $obTestimony = EntityTestimony::getTestimonyById($id);

      // valida a instancia
      if (!$obTestimony instanceof EntityTestimony) {
        $request->getRouter()->redirect('/admin/testimonies');
      }

      // Exclui o depoimento
      $obTestimony->excluir();

      // redireciona o usuário
      $request->getRouter()->redirect('/admin/testimonies?status=deleted');

    }

}

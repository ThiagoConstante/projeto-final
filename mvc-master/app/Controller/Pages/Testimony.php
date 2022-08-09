<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Testimony as EntityTestimony;
use \WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Page{

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

        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 3);

        $results = EntityTestimony::getTestimonies(null, 'id DESC', $obPagination->getLimit());

        while ($obTestimony = $results->fetchObject(EntityTestimony::class)) {
            $items .= View::render('pages/testimony/item', [
                'nome' => $obTestimony->nome,
                'mensagem' => $obTestimony->mensagem,
                'data' => date('d/m/Y - H:i:s', strtotime($obTestimony->data))
            ]);

        }

        return $items;
    }

    /**
    * Método responsavel por retornar o conteúdo (view) de depoimentos
    * @param Request $request
    * @return string
    */
    public static function getTestimonies($request)
    {

        $content = View::render('pages/testimonies', [
                                    'itens' => self::getTestimonyItems($request, $obPagination),
                                    'pagination' => parent::getPagination($request, $obPagination)
                                ]);

        return parent::getPage('DEPOIMENTOS > WDEV', $content);
    }

    /**
     * Método responsável por cadastrar um depoimento
     * @param Request $request
     * @return string
     */
    public static function insertTestimony($request)
    {
        $postVars = $request->getPostVars();

        $obTestimony = new EntityTestimony();
        $obTestimony->nome = $postVars['nome'];
        $obTestimony->mensagem = $postVars['mensagem'];
        $obTestimony->cadastrar();

        return self::getTestimonies($request);
    }

}

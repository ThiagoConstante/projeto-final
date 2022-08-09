<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use App\Model\Entity\Organization;

class Home extends Page{

    /**
    * Método responsavel por retornar o conteúdo (view) da nossa Home
    * @return string
    */
    public static function getHome(){
        $obOrganization = new Organization();

        $content = View::render('pages/home', [
                                'name' => $obOrganization->name
                            ]);

        return parent::getPage('HOME > WDEV', $content);
    }

}

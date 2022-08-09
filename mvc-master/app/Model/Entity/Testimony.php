<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Testimony
{
    /**
     * ID do depoimento
     * @var integer
     */
    public $id;

    /**
     * Nome do usuário que fez o depoimento
     * @var string
     */
    public $nome;

    /**
     * Mensagem do depoimento
     * @var string
     */
    public $mensagem;

    /**
     * Data de publicação do depoimento
     * @var string
     */
    public $data;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar()
    {
        $this->data = date('Y-m-d H:i:s');

        $this->id = (new Database('depoimentos'))->insert([
            'nome' => $this->nome,
            'mensagem' => $this->mensagem,
            'data' => $this->data
        ]);

        return true;
    }

     /**
     * Método responsável por atualizar os dados da instancia atual
     * @return boolean
     */
    public function atualizar()
    {

        $this->id = (new Database('depoimentos'))->update('id = '.$this->id, [
            'nome' => $this->nome,
            'mensagem' => $this->mensagem
        ]);

        return true;
    }

    /**
    * Método responsável por excluir um depoimento do banco de dados
    * @return boolean
    */
    public function excluir()
    {
      return (new Database('depoimentos'))->delete('id = '.$this->id);
    }

    /**
    * Método responsável por retornar um depoimento com base no seu ID
    * @param integer $id
    * @return Testimony
    */
    public static function getTestimonyById($id)
    {
      return self::getTestimonies('id = '.$id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar Depoimentos
     * @param  string $where
     * @param  string $order
     * @param  string $limit
     * @param  string $field
     * @return PDOStatement
     */
    public static function getTestimonies($where = null, $order = null, $limit = null, $field = '*')
    {
        return (new Database('depoimentos'))->select($where, $order, $limit,$field);
    }
}

<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class User
{
    /**
     * ID do usuário
     * @var integer
     */
    public $id;

    /**
     * Nome do usuário
     * @var string
     */
    public $nome;

    /**
     * E-mail do usuário
     * @var string
     */
    public $email;

    /**
     * Senha do usuário
     * @var string
     */
    public $senha;

    /**
    * Atribui o id da instância do objeto
    * @param integer $id
    */
    public function setId($id)
    {
      $this->id = $id;
    }

    /**
    * Retorna o id da instância do objeto
    */
    public function getId()
    {
      return $this->id;
    }

    /**
    * Atribui o nome da instância do objeto
    * @param string $nome
    */
    public function setNome($nome)
    {
      $this->nome = $nome;
    }

    /**
    * Retorna o nome da instância do objeto
    */
    public function getNome()
    {
      return $this->nome;
    }

    /**
    * Atribui o email da instância do objeto
    * @param string $email
    */
    public function setEmail($email)
    {
      $this->email = $email;
    }

    /**
    * Retorna o email da instância do objeto
    */
    public function getEmail()
    {
      return $this->email;
    }

    /**
    * Atribui a senha da instância do objeto
    * @param string $senha
    */
    public function setSenha($senha)
    {
      $this->senha = $senha;
    }

    /**
    * Retorna a senha da instância do objeto
    */
    public function getSenha()
    {
      return $this->senha;
    }

    /**
    * Método responsável por cadastrar a instância atual no banco de dados
    * @return boolean
    */
    public function cadastrar()
    {
        $this->id = (new Database('usuarios'))->insert(['nome' => $this->nome,
                                                        'email' => $this->email,
                                                        'senha' => $this->senha]);

        return true;
    }

    /**
    * Método responsável por atualizar a instância atual no banco de dados
    * @return boolean
    */
    public function atualizar()
    {
      return (new Database('usuarios'))->update('id = '.$this->id, ['nome' => $this->nome,
                                                                    'email' => $this->email,
                                                                    'senha' => $this->senha]);
    }

    /**
    * Método responsável por excluir um usuário do banco de dados
    * @return boolean
    */
    public function excluir()
    {
      return (new Database('usuarios'))->delete('id = '.$this->id);
    }


    /**
    * Método responsável por retornar um usuário com base no seu id
    * @param integer $id
    * @return User
    */
    public static function getUserById($id)
    {
      return self::getUsers('id = '.$id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar um usuário com base no seu e-mail
     * @param  string $email
     * @return User
     */
    public static function getUserbyEmail($email)
    {
      return self::getUsers('email = "'.$email.'"')->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar Usuários
     * @param  string $where
     * @param  string $order
     * @param  string $limit
     * @param  string $field
     * @return PDOStatement
     */
    public static function getUsers($where = null, $order = null, $limit = null, $field = '*')
    {
        return (new Database('usuarios'))->select($where, $order, $limit,$field);
    }
}

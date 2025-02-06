<?php
namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;
use Source\Expections\TransactionException;
use Source\Support\Auth;

/**
 * @property array $headCsvFile Array com o cabeçalho da tabela de exportação de dados
 * @property array $types Tipos de transações aceitos no banco de dados
 * @property object $loggedUserData Dados do usuário logado
 */
class Transaction extends DataLayer
{
    public static $headCsvFile = ["Categoria", "Tipo", "Valor", "Descrição", "Data"];
    private array $types = ["receita", "despesa"];
    private object $loggedUserData;

    public function __construct()
    {
        parent::__construct("transactions", ["user_id", "type", "amount"]);

        $this->loggedUserData = Auth::getData();
    }

    /**
     * Realiza as regras de negócio e insere uma nova transação no banco
     * @param array $data Dados da requisição
     * @return self Irá retornar a transação cadastrada
     * @throws TransactionException Irá lançar uma excessão caso tenha algum dado incorreto ou falha na conexão com o banco
     */
    public function insert(array $data): self
    {
        $this->validateType($data['type']);
        $this->validateCategoryExistsAndUserIsOwner($data['category_id']);

        $this->user_id = $this->loggedUserData->id;
        $this->category_id = $data['category_id'];
        $this->type = $data['type'];
        $this->amount = $data['amount'];
        $this->description = $data['description'] ?? null;

        if (!$this->save()) {
            throw new TransactionException([
                "database" => [
                   $this->fail()->getMessage()
                ]
            ], "Erro no cadastro!", $this->fail()->getCode());
        }

        return $this;
    }

    /**
     * Realiza as regras de negócio e edita uma transação no banco
     * @param array $data Dados da requisição
     * @return self Irá retornar a transação editada
     * @throws TransactionException Irá lançar uma excessão caso tenha algum dado incorreto ou falha na conexão com o banco
     */
    public function edit(array $data): self
    {
        $transaction = $this->checkTransactionById($data['id']);
        $this->checkIsOwner($transaction);
        $this->validateType($data['type']);
        $this->validateCategoryExistsAndUserIsOwner($data['category_id']);

        $transaction->category_id = $data['category_id'];
        $transaction->type = $data['type'];
        $transaction->amount = $data['amount'];
        $transaction->description = $data['description'] ?? null;

        if (!$transaction->save()) {
            throw new TransactionException([
                "database" => [
                   $transaction->fail()->getMessage()
                ]
            ], "Erro na edição!", $transaction->fail()->getCode());
        }

        return $transaction;
    }

    /**
     * Remove uma transação do banco
     * @param int $id Id da transação
     * @return void Não terá retorno caso a transação seja removida com sucesso
     * @throws TransactionException Irá lançar uma excessão caso tenha algum dado incorreto ou falha na conexão com o banco
     */
    public function remove(int $id): void
    {
        $transaction = $this->checkTransactionById($id);
        $this->checkIsOwner($transaction);

        if (!$transaction->destroy()) {
            throw new TransactionException([
                "database" => [
                    "Erro ao deletar a transação. Verifique os dados e tente novamente!"
                ]
            ], "Erro ao deletar!");
        }
    }

    /**
     * Irá buscar uma transação por id caso o usuário logado seja o dono da transação
     * @param int $id Id da transação
     * @return self Irá retornar a transação encontrada
     */
    public function getById(int $id): self
    {
        $transaction = $this->checkTransactionById($id);
        $this->checkIsOwner($transaction);

        /**
         * Verifica se o id da categoria não é null
         * Se o id não for null, dados da categoria são adicionados na transação
         */
        if (!empty($transaction->category_id)) {
            $category = (new Category())->findById($transaction->category_id);
            $transaction->category = $category->data();
        }

        return $transaction;
    }

    /**
     * Retorna todas as transações do usuário logado
     * @return array Irá retornar todas as transações do usuário ou um array vazio[] caso o usuário não tenha transações cadastradas
     */
    public function getAll(): array
    {
        $transactions = $this->getAllByUser(); 

        if (empty($transactions)) return [];

        $response = $this->getCategoriesByTransactions($transactions);
        return $response;
    }

    /**
     * Retorna todas as transações do usuário logado
     * @return array Retorna um array com todas as transações ou um array vazio[] caso não tenha transações cadastadas
     */
    public function getAllByUser(): array
    {
        return $this->find("user_id = :uid", "uid={$this->loggedUserData->id}")->fetch(true) ?? [];
    }

    /**
     * Retorna todas as transações pelo type do usuário logado
     * @param string $type Tipo da transação
     * @return array Retorna um array com as transações e com o valor total
     * @return array Retorna um array vazio[] caso o usuário não tenha transações do tipo informado cadastradas
     */
    public function getByType(string $type): array
    {
        $this->validateType($type);

        $transactions = $this->find("user_id = :uid AND type = :type", "uid={$this->loggedUserData->id}&type={$type}")->fetch(true);
        if (empty($transactions)) return [];

        $response['totalAmount'] = $this->getTotalAmountByTypeAndUser($type)->totalAmount;
        $response['transactions'] = $this->getCategoriesByTransactions($transactions);

        return $response;
    }

    /**
     * Retorna o valor total das transações pelo tipo do usuário logado
     * @param string $type Tipo da transação
     * @return self Irá retornar um objeto com o valor total das transações
     * @return array Irá retornar um array vazio[] caso o valor total seja igual a 0
     */
    private function getTotalAmountByTypeAndUser(string $type): self|array
    {
        return $this->find("user_id = :uid AND type = :type", "uid={$this->loggedUserData->id}&type={$type}", "type, SUM(amount) AS totalAmount")->fetch() ?? [];
    }

    /**
     * Verifica se o tipo informado é valido
     * @param string $type Tipo a ser verificado
     * @return void Não irá ter retorno caso o tipo seja válido
     * @throws TransactionException Irá lançar uma excessão caso o tipo seja inválido
     */
    private function validateType(string $type): void
    {
        if (!in_array($type, $this->types)) {
            throw new TransactionException([
                "type" => ["O tipo de transação deve ser 'receita' ou 'despesa''"]
            ], "Dados inválidos!");
        }
    }

    /**
     * Verifica se existe uma categoria por id e o usuário é dono
     * @param int $category_id Id da categoria
     * @return void Não terá retorno se existir uma categoria e o usuário for dono
     * @throws TransactionException Irá lançar uma excessão caso a categoria não exista ou o usuário não seja dono
     */
    private function validateCategoryExistsAndUserIsOwner(int $category_id): void
    {
        $category = (new Category())->getByIdAndUserId($category_id, $this->loggedUserData->id);

        if (empty($category)) {
            throw new TransactionException([
                "category_id" => ["Você não tem acesso a esta categoria!"]
            ], "Dados inválidos!");
        }
    }

    /**
     * Verifica se a transação é do usuário logado
     * @param Transaction $transaction Transação a ser verificada
     * @return bool Retorna true caso a transação seja do usuário e false caso não seja do usuário
     */
    private function isOwner(Transaction $transaction): bool
    {
        return $transaction->user_id == Auth::getData()->id;
    }

    /**
     * Verifica se a transação é do usuário logado
     * @param Transaction $transaction Transação a ser verificada
     * @return void Não terá retorno caso a transação seja do usuário
     * @throws TransactionException Irá lançar uma excessão caso a transação não seja do usuário
     */
    private function checkIsOwner(Transaction $transaction): void
    {
        if (!$this->isOwner($transaction)) {
            throw new TransactionException([
                "user" => "Este usuário não tem permissão para editar esta transação!"
            ], message: "Permissão negada!", code: 403);
        }
    }

    /**
     * Verifica se existe uma transação com o id
     * @param int $id Id da transação
     * @return self Irá retornar a transação
     * @throws TransactionException Irá lançar uma excessão caso não exista uma transação com o id
     */
    private function checkTransactionById(int $id): self
    {
        $transaction = $this->findById($id);
        if (empty($transaction)) {
            throw new TransactionException([
                "id" => [
                    "Id inválido!"
                ]
            ], message: "Erro ao encontrar uma transação!");   
        }

        return $transaction;
    }

    /**
     * Irá Adicionar as categorias de cada transação no seu objeto
     * @param array $transaction Array com as transações
     * @return array Irá retornar uma array com as categorias de cada transação
     */
    private function getCategoriesByTransactions(array $transactions): array
    {
        $response = array_map(function($transaction) {
            /**
             * Verifica se a categoria da transação não é null
             */
            if (!empty($transaction->category_id)) {
                $category = (new Category())->findById($transaction->category_id);
                $transaction->category = $category->data();
            }

            return $transaction->data();
        }, $transactions);

        return $response;
    }
}
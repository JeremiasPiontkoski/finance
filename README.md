# Finance - API RestFull

## 📖 Descrição Geral
**Finance** é uma API RestFull desenvolvida em PHP para o gerenciamento de gastos e receitas, que é possível categorizar tanto os gastos quanto as receitas para uma melhor organização!

### 🎯 Objetivo do Projeto
Atualmente, muitas pessoas gastam mais do que ganham. O objetivo desta API é ajudar os usuários a equilibrar seus ganhos e despesas, fornecendo um controle financeiro eficiente.

### 👥 Público-Alvo
Essa API foi desenvolvida para pessoas que desejam gerenciar melhor suas receitas e gastos.

## 🛠 Tecnologias Utilizadas
- **Linguagem**: PHP 8.2
- **Banco de Dados**: MySQL
- **Bibliotecas utilizadas**:
  - [coffeecode Router](https://github.com/robsonvleite/router)
  - [coffeecode Datalayer](https://github.com/robsonvleite/datalayer)
  - [FireBase php-jwt](https://github.com/firebase/php-jwt)
  - [zircote swagger-php](https://github.com/zircote/swagger-php)
  - [Php Unit Test](https://phpunit.de/index.html)

## 🚀 Instalação e Configuração

### 📌 Pré-requisitos
Para rodar o projeto localmente, é necessário:
- PHP 8.2
- MySQL
- Git
- Composer

### 🖥 Passos para Instalação
1. Instale o [XAMPP](https://www.apachefriends.org/pt_br/index.html) e inicie o Apache e o MySQL.
2. Instale o [Composer](https://getcomposer.org/).
3. Instale o [Git](https://git-scm.com/downloads).
4. Acesse a pasta `htdocs` dentro do diretório do XAMPP.
5. Clone o repositório na pasta `htdocs`:
   ```sh
   git clone https://github.com/JeremiasPiontkoski/finance.git
   ```
6. Acesse a pasta do projeto:
   ```sh
   cd finance
   ```
7. Instale as dependências do projeto:
   ```sh
   composer install --ignore-platform-reqs
   ```
8. Importe o banco de dados:
   - Acesse `http://localhost/phpmyadmin`.
   - Crie um banco de dados chamado `finance`.
   - Importe o arquivo `finance.sql` localizado na pasta `db` do projeto.
9. Renomeie o arquivo `.env.example` para `.env` e configure as credenciais do banco de dados e a chave da API.
10. Acesse a URL `http://localhost/finance/documentation` para testar a API.
11. Para testar via **Postman**, importe o arquivo `Finance.postman_collection.json` localizado na raiz do projeto.

## 🔥 Uso
Esta API pode ser testada por meio da documentação Swagger disponível em:
```
http://localhost/finance/documentation
```
Ou via **Postman**, importando o arquivo `Finance.postman_collection.json`.

## 📂 Estrutura do Projeto
```
finance/
│── db/
│   └── finance.sql
│── documentation/
│   ├── api.php
│   ├── index.php
│── source/
│   ├── Boot/
│   │   ├── Config.php
│   │   ├── Helpers.php
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── CategoryController.php
│   │   ├── Controller.php
│   │   ├── FileController.php
│   │   ├── TransactionController.php
│   │   ├── UserController.php
│   ├── Exceptions/
│   │   ├── CategoryControllerException.php
│   │   ├── FileException.php
│   │   ├── TransactionException.php
│   │   ├── UserException.php
│   │   ├── ValidationException.php
│   ├── Middlewares/
│   │   ├── AuthMiddleware.php
│   ├── Models/
│   │   ├── Category.php
│   │   ├── Transaction.php
│   │   ├── User.php
│   ├── Support/
│   │   ├── Auth.php
│   │   ├── JwtToken.php
│   │   ├── Response.php
│   │   ├── Validator.php
│── swagger-ui/
│── vendor/
│── .env
│── .gitignore
│── .htaccess
│── composer.json
│── composer.lock
│── index.php
│── README.md
```

## 📡 Endpoints da API

### **User**
- **Update**: `PUT http://localhost/finance/users`
- **Insert**: `POST http://localhost/finance/users`

### **Auth**
- **Login**: `POST http://localhost/finance/auth`

### **Category**
- **Insert**: `POST http://localhost/finance/categories`
- **Update**: `PUT http://localhost/finance/categories/{id}`
- **Delete**: `DELETE http://localhost/finance/categories/{id}`
- **GetAll**: `GET http://localhost/finance/categories`

### **Transaction**
- **Insert**: `POST http://localhost/finance/transactions`
- **Update**: `PUT http://localhost/finance/transactions/{id}`
- **Delete**: `DELETE http://localhost/finance/transactions/{id}`
- **GetById**: `GET http://localhost/finance/transactions/{id}`
- **GetAll**: `GET http://localhost/finance/transactions`
- **GetByType**: `GET http://localhost/finance/transactions/type/{type}`

### **File**
- **ExportToCsvByType**: `GET http://localhost/finance/files/csv/{type}`

## 🔐 Autenticação
Esta API utiliza **JWT (JSON Web Token)** para autenticação.

### Como funciona?
- O usuário deve enviar um **e-mail** e **senha** para obter um token JWT.
- Se as credenciais forem válidas, um token será gerado e retornado.
- Esse token deve ser enviado em cada requisição autenticada no cabeçalho:
  ```sh
  Authorization: Bearer <TOKEN>
  ```

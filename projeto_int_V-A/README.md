# TrampoFácil - Portal de Oportunidades 🚀

O **TrampoFácil** é uma aplicação Web desenvolvida para conectar profissionais às melhores oportunidades de mercado. A plataforma permite o cadastro de dois tipos de usuários: **Candidatos** (que buscam vagas) e **Empresas** (que publicam vagas).

Este projeto foi desenvolvido como parte da Etapa 2 do Projeto Integrador.

## 🛠️ Tecnologias Utilizadas

**Front-end:**

- HTML5 e CSS3
- Bootstrap 5 (Estilização e Responsividade)
- JavaScript (Comunicação assíncrona com API usando `fetch`)

**Back-end:**

- PHP 8+ (Regras de negócio e Endpoints)
- MySQL (Banco de Dados Relacional)
- PDO (PHP Data Objects para comunicação segura com o banco)

## ⚙️ Pré-requisitos

Para rodar este projeto localmente, você precisará de um ambiente de servidor local que suporte PHP e MySQL. Recomendamos o uso de uma das seguintes ferramentas:

- **XAMPP** (Windows/Mac/Linux)
- **WampServer** (Windows)
- **MAMP** (Mac)

## 🚀 Como executar o projeto

Siga os passos abaixo para configurar e executar a aplicação na sua máquina:

### 1. Clonar/Mover o projeto

Mova a pasta inteira do projeto (`projeto_int_V-A`) para o diretório público do seu servidor local:

- No **XAMPP**: `C:\xampp\htdocs\`
- No **WAMP**: `C:\wamp64\www\`

### 2. Iniciar o Servidor

Abra o painel de controle do XAMPP/WAMP e inicie os serviços:

- **Apache** (Servidor Web)
- **MySQL** (Banco de Dados)

### 3. Configurar o Banco de Dados

1. Acesse o **phpMyAdmin** pelo navegador: `http://localhost/phpmyadmin/`
2. Na aba SQL ou Importar, execute o script do banco de dados que está localizado na pasta do projeto:
   - **Arquivo:** `backend/banco.sql`
3. Certifique-se de que o banco de dados `trampofacil` e suas respectivas tabelas foram criados com sucesso.

> **Nota:** As credenciais de conexão no arquivo `backend/conexao.php` estão configuradas como padrão do XAMPP (`host=localhost`, `user=root`, `pass=""`). Caso o seu banco tenha senha, altere este arquivo.

### 4. Acessar a Aplicação

Abra o navegador e acesse a URL:

```text
http://localhost/projeto_int_V-A/
```

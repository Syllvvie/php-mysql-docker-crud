# Pokedex CRUD — PHP + MySQL + Docker

## 1. Descrição do projeto

Aplicação web CRUD (Create, Read, Update, Delete) desenvolvida em **PHP** com banco de
dados **MySQL**, totalmente orquestrada com **Docker Compose**.

A entidade escolhida foi **Pokémon**. Cada registro cadastrado no banco possui os campos:

- `id` — chave primária, auto incremento
- `nome` — nome do Pokémon
- `tipo` — tipo (ex.: elétrico, fogo, água...)
- `descricao` — anotação livre escrita pelo usuário sobre o Pokémon
- `data_cadastro` — data em que o registro foi criado (definida automaticamente pelo servidor)
- `imagem_url` — link do sprite oficial do Pokémon

O grande diferencial do projeto é que o cadastro **não é feito digitando os dados manualmente**:
o usuário busca o Pokémon pelo nome ou número (ex.: `pikachu` ou `25`) e a aplicação consome a
**[PokeAPI](https://pokeapi.co/)** (API pública e gratuita) diretamente do navegador, via
JavaScript `fetch`, para trazer nome, tipo e imagem oficiais. Isso garante que os dados
cadastrados na Pokedex sempre correspondam a um Pokémon real, existente na PokeAPI.

## 2. Pré-requisitos

- [Docker](https://www.docker.com/) instalado
- [Docker Compose](https://docs.docker.com/compose/) instalado (já vem junto com o Docker Desktop)
- Conexão com a internet (necessária para a busca na PokeAPI ao cadastrar um Pokémon)

Nenhuma outra dependência é necessária na máquina.

## 3. Passo a passo para executar o projeto

1. **Clone o repositório:**
   ```bash
   git clone <URL_DO_REPOSITORIO>
   cd php-mysql-docker-crud
   ```

2. **Suba os containers:**
   ```bash
   docker compose up -d
   ```
   Esse comando sobe dois containers: o da aplicação PHP (`pokedex_app`) e o do banco de
   dados MySQL (`pokedex_db`).

3. **Criação da tabela no banco:**
   A tabela `pokemons` **não precisa ser criada manualmente**. O arquivo `src/db.php` é
   incluído em todas as páginas da aplicação e, a cada requisição, executa um
   `CREATE TABLE IF NOT EXISTS`, criando a tabela automaticamente caso ela ainda não
   exista. Ou seja: assim que a aplicação PHP consegue se conectar ao MySQL pela primeira
   vez, a estrutura do banco já fica pronta para uso, sem precisar rodar nenhum script
   `.sql` à parte.

4. **Acesse a aplicação no navegador:**
   ```
   http://localhost:8080
   ```

5. **Para derrubar os containers:**
   ```bash
   docker compose down
   ```
   (os dados do banco continuam salvos no volume `db_data`, mesmo depois do `down`)

## 4. Explicação do `docker-compose.yml`

O arquivo já contém comentários linha a linha explicando cada diretiva. Em resumo:

### Serviço `app`
- Usa a imagem pronta `php:8.1-apache` (PHP + Apache já integrados).
- Mapeia a porta `8080` da máquina host para a porta `80` do container, permitindo acessar
  a aplicação em `http://localhost:8080`.
- Usa um **volume bind mount** (`./src:/var/www/html`) que sincroniza a pasta local `src/`
  com a raiz do servidor Apache dentro do container — qualquer alteração nos arquivos PHP
  locais é refletida imediatamente, sem precisar reconstruir a imagem.
- No `command`, instala a extensão `pdo_mysql` (necessária para o PHP conversar com o
  MySQL via PDO) antes de iniciar o Apache.
- Recebe as variáveis de ambiente `DB_HOST`, `DB_USER`, `DB_PASSWORD` e `DB_NAME`
  diretamente na seção `environment` do compose (sem uso de arquivo `.env`), que são lidas
  pelo `db.php` através de `getenv()` para montar a conexão PDO.
- Depende do serviço `db` (`depends_on`), garantindo que o banco suba antes da aplicação.

### Serviço `db`
- Usa a imagem oficial `mysql:8.0`.
- Recebe `MYSQL_ROOT_PASSWORD` e `MYSQL_DATABASE`, variáveis próprias da imagem do MySQL
  que definem a senha do usuário root e criam automaticamente o banco `pokedex` na
  primeira inicialização do container.
- Usa `restart: always`, reiniciando o container automaticamente em caso de falha.
- Usa um **volume nomeado** (`db_data:/var/lib/mysql`), garantindo que os dados do banco
  persistam mesmo se o container for removido e recriado.

### Rede
Os dois serviços são conectados à rede personalizada `pokedex-network` (driver `bridge`),
o que permite que o container `app` se conecte ao banco simplesmente usando o nome do
serviço (`db`) como host — é exatamente o valor usado na variável `DB_HOST`.

## 5. Autores

- Joao Vitor Paiva Borges
- Nikolas Lodi Monteiro

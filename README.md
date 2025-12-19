# Sistema de Gestão de Treinamentos (Laravel + Vue.js)

Este é um sistema robusto para gestão de treinamentos e capacitações, desenvolvido com Laravel e Vue.js, focado na administração de cursos, inscrições e relatórios gerenciais.

## 🚀 Tecnologias Utilizadas

- **Backend**: Laravel 10+
- **Frontend**: Vue.js 3
- **Build Tool**: Vite
- **Banco de Dados**: MySQL

## ✨ Funcionalidades

### Painel Administrativo
O sistema conta com uma área administrativa completa para gestão de recursos:

- **Controle de Acesso**: Gerenciamento de usuários e aprovação de cadastros.
- **Estrutura Organizacional**:
    - Diretorias
    - Regionais (com vínculo a Estados)
    - Estados e Cidades
- **Gestão Pedagógica**:
    - Treinamentos
    - Cursos
    - Modalidades
    - Estratégias
    - Tipos de Treinamento
    - Públicos-Alvo
- **Monitoramento**:
    - Logs do Sistema (Rastreabilidade de ações)
    - Relatórios Gerais

### Área Pública / Usuário
- **Autenticação**: Login e Registro de novos usuários.
- **Inscrições**: Fluxo para usuários se inscreverem em cursos disponíveis.

## 🛠️ Instalação e Configuração

Siga os passos abaixo para rodar o projeto localmente:

1. **Clone o repositório**
   ```bash
   git clone <URL_DO_REPOSITORIO>
   cd laravel-vue-project
   ```

2. **Instale as dependências do Backend (PHP)**
   ```bash
   composer install
   ```

3. **Instale as dependências do Frontend (Node.js)**
   ```bash
   npm install
   ```

4. **Configuração do Ambiente**
   - Copie o arquivo `.env.example` para `.env`:
     ```bash
     cp .env.example .env
     ```
   - Gere a chave da aplicação:
     ```bash
     php artisan key:generate
     ```
   - Configure as credenciais do banco de dados no arquivo `.env`.

5. **Banco de Dados**
   - Execute as migrações para criar as tabelas:
     ```bash
     php artisan migrate
     ```
   - (Opcional) Execute os seeders se houver dados iniciais:
     ```bash
     php artisan db:seed
     ```

6. **Iniciar a Aplicação**
   - Inicie o servidor de desenvolvimento do Vite:
     ```bash
     npm run dev
     ```
   - Em outro terminal, inicie o servidor do Laravel:
     ```bash
     php artisan serve
     ```

## 📝 Licença

Este projeto é software open-source licenciado sob a [MIT license](https://opensource.org/licenses/MIT).

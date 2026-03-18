# Keys SaaS — Gerenciador de Senhas e Cofre Digital

![PHP](https://img.shields.io/badge/PHP-%3E%3D8.0-blue?logo=php) ![Docker](https://img.shields.io/badge/Docker-enabled-blue?logo=docker) ![License](https://img.shields.io/badge/License-MIT-green)

Projeto de gerenciador de senhas e cofre digital para armazenamento seguro de documentos internos, senhas de serviços e dados bancários.

**Status do projeto**
- **API**: Estrutura madura, mas precisa de adições extras e melhorias (backend em PHP).
- **Front-end**: Em desenvolvimento (parte em Node.js/TypeScript/SCSS).
- **Estado geral**: Projeto em andamento — não finalizado.

**Ideia / Objetivo**

O projeto é um gerenciador de senhas que também funciona como um cofre digital pessoal. Permite ao usuário guardar de forma segura:
- Documentos internos (arquivos privados)
- Senhas de contas e serviços
- Dados bancários e informações sensíveis

Todas as informações sensíveis devem ser tratadas com atenção à segurança e com práticas de criptografia apropriadas.

**Features implementadas**
- Autenticação e gerenciamento básico de usuários (API).
- Rotas e controllers para senhas, softwares e cartões (backend).
- Persistência com MySQL (esquema em [backend/db/db.sql](backend/db/db.sql#L1-L200)).
- Upload e armazenamento controlado de arquivos (mecanismo básico no backend).

Observação: o front-end contém componentes e scripts em TypeScript/SCSS, porém ainda há interfaces e modais em construção.

**Tecnologias usadas**
- Redis — cache e sessão.
- MySQL — banco de dados principal.
- PHP — API/serviços (estrutura em [backend/](backend/)).
- Node.js — front-end dev server e tooling (`frontend/server.js`).
- Docker / Docker Compose — conteinerização e orquestração (veja `docker-compose.yml`).
- SCSS — estilos do projeto (pasta `frontend/dev/scss`).
- TypeScript — scripts e components do frontend (`frontend/dev/ts`).

**Como rodar (rápido)**

Requisitos: `Docker` e `docker-compose` instalados.

Comandos básicos:

```bash
# Build e subir containers (API + DB + Redis, conforme docker-compose.yml)
docker-compose up --build

# Para desenvolvimento do frontend (na máquina local, sem docker):
cd frontend
# npm install
# node server.js
```

Verifique e ajuste variáveis de ambiente (credenciais de banco, chaves de criptografia, endpoints do Redis) antes de rodar.

**Estrutura importante**
- `backend/` — API PHP, controllers, models e utilitários.
- `frontend/` — scripts, SCSS e components TypeScript.
- `docker-compose.yml` — orquestração esperada.
- `backend/db/db.sql` — esboço do esquema do banco de dados.

**Como os segredos são armazenados / Criptografia**

- `main_pass` (senha mestra): atualmente a senha principal dos usuários é armazenada utilizando a função `password_hash()` do PHP, disponível em [backend/Source/Models/User.php](backend/Source/Models/User.php#L1-L200). Isso utiliza o algoritmo padrão do PHP (ex.: bcrypt), que é um hash unidirecional adequado para senhas de autenticação.

- Senhas armazenadas em `passwords.value`: no esquema atual (veja [backend/db/db.sql](backend/db/db.sql#L1-L200)) o campo `value` existe como `TEXT` e, no código atual, os valores são gravados diretamente pelo backend em `backend/Source/Models/Password.php` sem uma etapa de criptografia reversível no servidor. Ou seja, atualmente esses itens permanecem como texto salvo no banco.

- Cartões / `encrypted_number`: o esquema do banco define campos `encrypted_number` para cartões (tabelas `cards`, `credit_cards`, `debit_cards`), porém a implementação atual dos modelos insere ao menos `masked_number` e outros metadados.

**Roadmap**

- [x] Adicionar badges no README
- [x] Documentar como as senhas/documentos são tratados atualmente
- [x] Adicionar roadmap com checkboxes
- [ ] Implementar cifragem cliente (E2EE) com chave derivada da senha mestra
- [ ] Implementar cifragem no servidor para campos sensíveis (libsodium / AES-256-GCM)
- [ ] Migrar uso de `encrypted_number` no banco para implementação real ou remover campo se não for utilizado
- [ ] Testes automatizados de segurança e integração
- [ ] Revisão de segurança e hardening (segredos, headers, CORS, rate limiting)

---

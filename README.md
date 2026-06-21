# Materiais Local — PMESP / CPI-7

Sistema PHP de gestão de materiais do CPI-7, com banco MySQL local. Estrutura adaptada a partir do sistema legado de Gestão de Turmas (TESTE), parametrizada via `.env` para evitar credenciais hardcoded.

## Stack

- **PHP 8.2** (com extensão PDO + MySQL)
- **MySQL/MariaDB 10.11+** (local)
- **Apache 2.4** com mod_rewrite
- **Composer 2** (gerenciamento de dependências — Twig 3.0)
- **Ubuntu 24.04 LTS** (recomendado)

## Estrutura

```
materiais_local/
├── app/
│   ├── Core/         # Config, Core (router)
│   ├── Controller/   # Controllers MVC
│   └── Model/        # Models
├── lib/
│   ├── Database/     # Connection (PDO via .env)
│   ├── Env.php       # Loader de .env
│   └── fpdf/         # Geração de PDFs
├── public/           # CSS, JS, imagens, uploads
├── signin/           # Tela de login + check-login.php
├── tmp/sessions/     # Sessões PHP (NÃO vai pro git)
├── avaliacao/        # Subsistema de avaliação
├── .env.example      # Template de configuração
├── .env              # Config local (NÃO vai pro git)
├── composer.json     # Dependência: twig/twig
└── index.php         # Entry point
```

## Setup local (desenvolvimento)

```bash
# 1. Clonar
git clone https://github.com/Willians96/materiais_local.git
cd materiais_local

# 2. Instalar dependências
composer install

# 3. Configurar banco
cp .env.example .env
# Editar .env com suas credenciais MySQL

# 4. Criar schema (rodar SQL de migrations/001_inicial.sql)

# 5. Servir (Apache vhost configurado) ou via PHP built-in:
php -S localhost:8000
```

## Segurança

- **NUNCA** commitar o arquivo `.env`
- **NUNCA** commitar credenciais MySQL hardcoded
- Credenciais vivem **apenas** no `.env` local
- `hostinger.txt` e outros arquivos sensíveis estão no `.gitignore`

## Migrações

Coloque seus scripts SQL em `migrations/` na ordem:
- `001_inicial.sql`
- `002_alteracoes.sql`
- ...

## Deploy

Veja `DEPLOY.md` para instruções de deploy na VPS Hostinger.

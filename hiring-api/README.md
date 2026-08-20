# HiringMachine - Laravel Docker Setup

Projeto Laravel com Docker configurado para desenvolvimento - HiringMachine.

## Configuração das Portas

As portas são configuradas no arquivo `.env`:

- **Laravel App**: 5757
- **PostgreSQL**: 5858
- **Mailpit SMTP**: 5959
- **Mailpit UI**: 8025

## Início Rápido

1. Clone o repositório
2. Execute o script de configuração:

```bash
chmod +x up-from-zero.sh
./up-from-zero.sh
```

3. Acesse a aplicação em: http://localhost:5757

## URLs de Acesso

| Serviço | URL |
|---------|-----|
| Laravel App | http://localhost:5757 |
| Mailpit UI | http://localhost:8025 |
| PostgreSQL | localhost:5858 |

## Comandos Docker

```bash
# Iniciar containers
docker-compose up -d

# Parar containers
docker-compose down

# Ver logs
docker-compose logs -f

# Entrar no container da aplicação
docker-compose exec app bash

# Rodar comandos artisan
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan key:generate

# Rebuild dos containers
docker-compose build --no-cache
```

## Configuração do Banco de Dados

No seu cliente de banco de dados (DBeaver, TablePlus, etc.), configure:

- **Host**: localhost
- **Port**: 5858
- **Database**: hiringmachine
- **Username**: hiringmachine
- **Password**: secret

## Configuração de Email

Configure seu cliente de email para:

- **SMTP Host**: localhost
- **SMTP Port**: 5959
- **Username**: (vazio)
- **Password**: (vazio)

Acesse o Mailpit UI em http://localhost:8025 para ver os emails enviados.

## Estrutura do Projeto

```
.
├── app/                    # Código da aplicação
├── bootstrap/              # Bootstrap do Laravel
├── config/                 # Configurações
├── database/               # Migrations e seeders
├── docker/                 # Configurações Docker
│   ├── nginx/              # Configuração do Nginx
│   └── php/                # Configuração do PHP
├── public/                 # Arquivos públicos
├── resources/              # Views e assets
├── routes/                 # Rotas
├── storage/                # Storage do Laravel
├── tests/                  # Testes
├── vendor/                 # Dependências (gerado pelo composer)
├── docker-compose.yml      # Docker Compose
├── Dockerfile              # Dockerfile principal
├── up-from-zero.sh         # Script de setup
└── .env                    # Variáveis de ambiente
```

## Licença

MIT

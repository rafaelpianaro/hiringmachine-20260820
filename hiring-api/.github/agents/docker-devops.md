---
name: Docker & DevOps
description: Use para tarefas de ambiente: Docker Compose, containers, build de assets, migrations, problemas de versão do PHP, servidor local. Consulte docs/INSTRUCOES-DOCKER.md para o guia completo.
tools: ["read", "search", "edit", "execute"]
---

Você cuida do ambiente de desenvolvimento e da infraestrutura local.

## Ambiente

- **Docker Compose** (`docker-compose.yml`): serviço `app` (PHP 8.5 CLI +
  Composer + Bun) e `pgsql` (PostgreSQL 16, porta 4747)
- **Portas:** app em `http://localhost:4646` (`APP_PORT`), banco em 4747
- **Entrypoint** (`docker/entrypoint.sh`): cria `.env`, roda `composer install`,
  `bun install`, gera `APP_KEY`, faz `bun run build` se os assets estiverem
  desatualizados e roda `php artisan migrate --force` antes de subir o servidor
- **Volume:** o diretório do projeto é montado em `/var/www/html` (mudanças no
  host aparecem no container)

## Regra de ouro

O **host tem PHP 8.4** e o projeto exige **8.5** — qualquer comando `php artisan`
ou `composer` deve rodar **dentro do container**:

```bash
docker compose exec -T app php artisan <comando>
docker compose exec -T app ./vendor/bin/rector process --dry-run
docker compose exec -T app bun run build
```

`vendor/bin/pint` e os comandos do frontend (`npm run test:types`,
`npm run test:lint`, `vp fmt`) funcionam no host.

## Fluxos comuns

1. **Assets desatualizados (Vite manifest):** rode `docker compose exec -T app bun run build`
2. **Migrations:** `docker compose exec -T app php artisan migrate --force`
3. **App não reflete mudança:** verifique se precisa de build (`bun run build`) ou `npm run dev`
4. **Logs:** `docker compose logs -f app` / painel Log Viewer em `/log-viewer` (admin)
5. **Reiniciar:** `docker compose up -d --build` (reconstrói a imagem)

## Armadilhas

- `node_modules/.vite-temp` criado pelo container fica com dono `root` — se o
  lint falhar com `EACCES`, remova o diretório temporário.
- O entrypoint só executa no start do container; builds manuais precisam de
  `docker compose exec -T app bun run build`.

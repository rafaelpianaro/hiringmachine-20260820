#!/usr/bin/env bash
#
# up-from-zero.sh — Sobe o projeto do zero com Docker.
#
# Faz tudo que é necessário para subir do zero:
#   1. Garante que o .env existe (cria a partir do .env.example se faltar);
#   2. Remove containers antigos com nomes fixos do compose — inclusive os de
#      projetos anteriores (ex.: quando o diretório tinha outro nome), que o
#      `docker compose down` do projeto atual não alcança;
#   3. Derruba o projeto atual com os volumes (banco, redis, mailpit e
#      playwright zerados) e remove volumes órfãos de nomes antigos;
#   4. Constrói a imagem e sobe todos os serviços com as configurações do .env;
#   5. Aguarda o servidor da app ficar pronto e mostra as URLs.
#
# Uso:
#   ./up-from-zero.sh
#
set -euo pipefail

cd "$(dirname "$0")"

# 0. Docker disponível?
if ! command -v docker >/dev/null 2>&1; then
    echo "ERRO: docker não encontrado no PATH." >&2
    exit 1
fi

# 1. Garante o .env (portas e credenciais ficam sob controle do usuário)
if [ ! -f .env ]; then
    cp .env.example .env
    echo "==> .env criado a partir do .env.example"
    echo "    Revise as portas (APP_PORT, DB_PORT, REDIS_PORT, etc.) antes de subir."
fi

# 1.1. Prefixo dos nomes de containers e da imagem (definido no .env — derive-o do APP_NAME:
#      minúsculas, sem espaços, hífens no lugar dos espaços)
APP_CONTAINER_PREFIX="$(grep -E '^APP_CONTAINER_PREFIX=' .env | head -n1 | cut -d= -f2- | tr -d '"' | tr -d "'" || true)"
APP_CONTAINER_PREFIX="${APP_CONTAINER_PREFIX:-starter-kit-inertia-react}"
APP_CONTAINER="${APP_CONTAINER_PREFIX}-app"

# 2. Remove containers antigos com nomes fixos do compose — podem ser de um
#    projeto anterior (ex.: starter-kit-inertia-react-20260808) que o
#    `docker compose down` do projeto atual não alcança.
CONTAINERS=(
    "${APP_CONTAINER_PREFIX}-app"
    "${APP_CONTAINER_PREFIX}-pgsql"
    "${APP_CONTAINER_PREFIX}-redis"
    "${APP_CONTAINER_PREFIX}-mailpit"
)

for container in "${CONTAINERS[@]}"; do
    if docker ps -aq --filter "name=^/${container}$" | grep -q .; then
        echo "==> Removendo container antigo: ${container}"
        docker rm -f "${container}" >/dev/null
    fi
done

# 3. Derruba o projeto atual com os volumes (banco, redis, mailpit e playwright zerados)
echo "==> Derrubando o projeto atual (containers, rede e volumes)..."
docker compose down --remove-orphans -v

# 3.1. Remove volumes órfãos de nomes antigos deste projeto (quando o diretório/
#      projeto tinha outro nome). Só volumes sem nenhum container usando.
ORPHAN_VOLUMES="$(docker volume ls -qf dangling=true | grep -E '_(pgsql_data|redis_data|mailpit_data|playwright_cache)$' || true)"
if [ -n "${ORPHAN_VOLUMES}" ]; then
    echo "==> Removendo volumes órfãos:"
    echo "${ORPHAN_VOLUMES}" | sed 's/^/    /'
    echo "${ORPHAN_VOLUMES}" | xargs docker volume rm >/dev/null
fi

# 4. Sobe tudo do zero (builda a imagem se necessário)
echo "==> Subindo os serviços (build + bootstrap: composer, bun, migrations)..."
docker compose up -d --build

# 5. Aguarda o servidor da app ficar pronto.
#    Usa polling no log (em vez de `docker logs -f | grep -qm1`): com o follow, o
#    grep encontra a mensagem e sai, mas o `docker logs -f` continua seguindo o log
#    para sempre — a pipeline nunca termina e o script só morre no timeout de 10 min,
#    mesmo com o servidor já no ar.
echo "==> Aguardando o servidor da aplicação..."
if timeout 600 bash -c "while ! docker logs '${APP_CONTAINER}' 2>&1 | grep -qm1 'Server running'; do sleep 2; done"; then
    APP_PORT="$(grep -E '^APP_PORT=' .env | cut -d= -f2- | tr -d '"' || true)"
    APP_PORT="${APP_PORT:-8080}"
    MAILPIT_PORT="$(grep -E '^MAILPIT_PORT=' .env | cut -d= -f2- | tr -d '"' || true)"
    MAILPIT_PORT="${MAILPIT_PORT:-8025}"

    echo ""
    echo "✅ Projeto no ar do zero!"
    echo "   App:      http://localhost:${APP_PORT}"
    echo "   Mailpit:  http://localhost:${MAILPIT_PORT}"
    echo ""
    echo "Para o Vite (HMR) em desenvolvimento:"
    echo "   docker exec -it ${APP_CONTAINER} bun run dev"
else
    echo "⚠️  O servidor não ficou pronto dentro do tempo esperado." >&2
    echo "    Confira o log: docker compose logs -f app" >&2
    exit 1
fi

#!/usr/bin/env bash
#
# xdebug-up.sh — Sobe o container com o Xdebug apontando para o IP real do host.
#
# Necessário no Docker Desktop for Linux, onde o container não alcança o host
# via host.docker.internal/gateway (o Xdebug não consegue conectar no VSCode).
# Em Docker Engine Linux nativo ou Docker Desktop Mac/Windows, o entrypoint já
# detecta o host automaticamente e você pode usar `docker compose up -d` direto.
#
# Uso:
#   ./xdebug-up.sh
#
set -euo pipefail

cd "$(dirname "$0")"

# 1. Detecta o IP do host (IP de origem da rota padrão — a interface da rede real)
HOST_IP="$(ip -4 route get 8.8.8.8 2>/dev/null | awk '{for (i = 1; i <= NF; i++) if ($i == "src") { print $(i + 1); exit }}')"
if [ -z "${HOST_IP:-}" ]; then
    HOST_IP="$(hostname -I 2>/dev/null | awk '{print $1}')"
fi

if [ -z "${HOST_IP:-}" ]; then
    echo "ERRO: não foi possível detectar o IP do host. Defina XDEBUG_CLIENT_HOST manualmente." >&2
    exit 1
fi

echo "==> IP do host detectado: ${HOST_IP}"
echo "==> Subindo o container com XDEBUG_CLIENT_HOST=${HOST_IP} ..."

# 2. Injeta o IP via env — o docker-compose.yml faz a interpolação ${XDEBUG_CLIENT_HOST}
XDEBUG_CLIENT_HOST="$HOST_IP" docker compose up -d app

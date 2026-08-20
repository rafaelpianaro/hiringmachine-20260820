#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

# 0. Permite que o git (usado pelo composer) opere no diretório montado
if ! git config --global --get-all safe.directory 2>/dev/null | grep -qx '/var/www/html'; then
    git config --global --add safe.directory /var/www/html
fi

# 1. Cria .env a partir do .env.example, se não existir
if [ ! -f .env ]; then
    cp .env.example .env
    echo "==> .env criado a partir de .env.example"
fi

# 1.1. Detecta automaticamente o IP do host para o Xdebug conectar no debugger do VSCode.
#      Ordem de tentativa: XDEBUG_CLIENT_HOST explícito > host.docker.internal > gateway da rede > docker0.
#      Funciona em Docker Engine Linux nativo (gateway = host) e Docker Desktop Mac/Windows
#      (host.docker.internal). Em Docker Desktop for Linux o container não alcança o host por
#      nenhum desses caminhos — use ./xdebug-up.sh, que injeta o IP real do host via env.
detect_xdebug_host() {
    local xdebug_host=""

    if [ -n "${XDEBUG_CLIENT_HOST:-}" ]; then
        xdebug_host="${XDEBUG_CLIENT_HOST}"
        echo "==> Xdebug: usando XDEBUG_CLIENT_HOST=${XDEBUG_CLIENT_HOST} (definido explicitamente)"
    else
        local candidates=("host.docker.internal")

        # Gateway padrão (/proc/net/route) — no Docker Engine Linux nativo ele É o host
        local gw_hex gw_ip
        gw_hex="$(awk '$2=="00000000" && $3!="00000000"{print $3; exit}' /proc/net/route 2>/dev/null || true)"
        if [ -n "$gw_hex" ]; then
            gw_ip="$(printf '%d.%d.%d.%d' "0x${gw_hex:6:2}" "0x${gw_hex:4:2}" "0x${gw_hex:2:2}" "0x${gw_hex:0:2}" 2>/dev/null || true)"
            [ -n "$gw_ip" ] && candidates+=("$gw_ip")
        fi
        candidates+=("172.17.0.1")

        local host
        for host in "${candidates[@]}"; do
            if timeout 1 bash -c "echo > /dev/tcp/${host}/9003" 2>/dev/null; then
                xdebug_host="$host"
                echo "==> Xdebug: host detectado em ${host}:9003"
                break
            fi
        done

        if [ -z "$xdebug_host" ]; then
            xdebug_host="host.docker.internal"
            echo "==> Aviso: nenhum listener Xdebug detectado na porta 9003 (debugger do VSCode parado?)."
            echo "    Usando host.docker.internal como fallback. Se o debug não conectar, rode ./xdebug-up.sh"
            echo "    ou defina XDEBUG_CLIENT_HOST no .env com o IP do host (veja ip -brief addr show)."
        fi
    fi

    # Exporta a env var E escreve um ini dinâmico: o `php artisan serve` lança o `php -S`
    # (que processa as requisições) sem repassar a env var, então só o ini garante que
    # TODO processo PHP use o client_host correto.
    export XDEBUG_CLIENT_HOST="$xdebug_host"
    printf 'xdebug.client_host=%s\n' "$xdebug_host" > /usr/local/etc/php/conf.d/zzz-xdebug-client.ini
}

detect_xdebug_host

# Os comandos de bootstrap abaixo rodam com XDEBUG_MODE=off: com o debugger do VSCode
# escutando, cada processo PHP (composer/artisan) conectaria no debugger e ficaria
# pausado esperando o IDE — travando a inicialização do container. O debug só é
# habilitado no servidor final (php artisan serve), que herda o XDEBUG_MODE=debug.
BOOTSTRAP_XDEBUG=("env" "XDEBUG_MODE=off")

# 2. Cria os diretórios de cache e storage do Laravel, necessários para o bootstrap
mkdir -p bootstrap/cache storage/framework/{cache,sessions,views,testing} storage/logs
chmod -R 775 bootstrap/cache storage/framework storage/logs 2>/dev/null || true

# 3. Instala as dependências PHP (obrigatório antes de qualquer comando artisan)
"${BOOTSTRAP_XDEBUG[@]}" composer install --no-interaction --prefer-dist --no-progress --no-security-blocking

# 3. Instala as dependências do frontend apenas se houver package.json
if [ -f package.json ]; then
    bun install --no-progress
else
    echo "==> Nenhum package.json encontrado; pulando bun install/build"
fi

# 3.1. Instala os browsers do Playwright na primeira subida (necessário para os testes de browser)
if [ -z "$(ls -A /root/.cache/ms-playwright 2>/dev/null)" ]; then
    echo "==> Instalando browsers do Playwright..."
    bunx playwright install chromium
fi

# 4. Gera a APP_KEY se estiver vazia
if ! grep -qE '^APP_KEY=.+' .env; then
    "${BOOTSTRAP_XDEBUG[@]}" php artisan key:generate --force
    echo "==> APP_KEY gerada"
fi

# 5. Compila os assets apenas se necessário (manifest ausente ou código mais novo)
if [ -f package.json ] && ([ ! -f public/build/manifest.json ] || [ -n "$(find resources -newer public/build/manifest.json 2>/dev/null | head -n 1)" ]); then
    echo "==> Compilando assets do frontend..."
    bun run build
fi

# 6. Executa as migrations pendentes
"${BOOTSTRAP_XDEBUG[@]}" php artisan migrate --force --no-interaction

# 7. Sobe o servidor (com o Xdebug ativo, XDEBUG_MODE=debug herdado do compose)
exec php artisan serve --host=0.0.0.0 --port=80

#!/usr/bin/env bash
#
# Arranca a stack de teste do PrestaShop 9 e renomeia a pasta /admin
# (o instalador da imagem oficial falha o rename em Docker Desktop/Windows).
#
# Uso:
#   ./setup.sh            arranca a stack e renomeia o admin
#   ./setup.sh --rebuild  destrói tudo (incl. BD) e reinstala do zero
#
set -euo pipefail

WEB_CONTAINER="moloni_ps9_web"
ADMIN_DIR="adminmoloni"
BASE_URL="http://localhost:8080"

if [[ "${1:-}" == "--rebuild" ]]; then
  echo "==> A destruir stack e volumes..."
  docker compose down -v
fi

echo "==> A arrancar a stack..."
docker compose up -d

echo "==> A aguardar que o PrestaShop termine a instalação..."
# Espera até a pasta 'admin' existir dentro do container (fim do install).
for _ in $(seq 1 60); do
  if docker exec "$WEB_CONTAINER" test -d /var/www/html/admin 2>/dev/null; then
    break
  fi
  if docker exec "$WEB_CONTAINER" test -d "/var/www/html/$ADMIN_DIR" 2>/dev/null; then
    echo "==> Pasta de admin já renomeada."
    break
  fi
  sleep 5
done

echo "==> A renomear a pasta de admin e a limpar cache..."
docker exec "$WEB_CONTAINER" sh -c "
  if [ -d /var/www/html/admin ]; then
    mv /var/www/html/admin /var/www/html/$ADMIN_DIR
  fi
  rm -rf /var/www/html/var/cache/* 2>/dev/null || true
"

echo ""
echo "==> Pronto!"
echo "    Loja:       $BASE_URL"
echo "    Backoffice: $BASE_URL/$ADMIN_DIR"
echo "    Login:      admin@example.com / prestashop123"

# Le poste des corrections d'Holoworkflow (.holoworkflow/fix.yaml) : PHP et Node aux
# versions de la stack, et les dépendances installées depuis les verrous de la
# branche par défaut. Construit avec le réseau ouvert ; les corrections tournent
# ensuite sans réseau. Contexte de construction : la racine du dépôt.
FROM node:24-slim AS node
FROM composer:2 AS composer

# PHP 8.3 comme l'API. Debian bookworm des deux côtés : les binaires natifs de
# node_modules sont liés à la glibc.
FROM php:8.3-cli-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules

ENV COREPACK_HOME=/opt/corepack
# Sans TTY, pnpm refuse de recréer node_modules après `pnpm fetch` : CI=true est la
# réponse documentée.
ENV CI=true
RUN ln -s /usr/local/lib/node_modules/corepack/dist/corepack.js /usr/local/bin/corepack \
    && corepack enable \
    && corepack prepare pnpm@10.30.3 --activate

WORKDIR /workspace

# Les verrous d'abord : un changement de code seul réutilise ces couches.
# Les extensions PHP de l'API (intl, pdo_mysql…) ne servent pas aux outils lancés
# ici, d'où --ignore-platform-reqs.
COPY api/composer.json api/composer.lock api/
RUN composer install --working-dir=api --no-interaction --no-scripts --no-progress --prefer-dist --ignore-platform-reqs

COPY package.json pnpm-lock.yaml pnpm-workspace.yaml .npmrc ./
RUN pnpm fetch --frozen-lockfile

# Le reste du dépôt, dont les paquets du workspace que pnpm relie.
COPY . .
RUN pnpm install --prefer-offline --frozen-lockfile

# Lisible par l'utilisateur de l'hôte, sous lequel tournent les corrections.
RUN chmod -R a+rX /workspace

# Image de développement partagée par les conteneurs front et par le service
# d'installation. pnpm est installé à la construction (en root) pour que les
# conteneurs puissent ensuite tourner sous l'utilisateur hôte sans jamais avoir à
# écrire dans un emplacement appartenant à root — c'est ce qui garde les fichiers
# générés (SDK orval, .nuxt, node_modules…) à l'utilisateur hôte plutôt qu'à root.
# Image Debian (glibc) et NON alpine (musl), délibérément.
#
# node_modules est monté depuis l'hôte et partagé avec lui. Les dépendances à
# binaire natif (rolldown, esbuild, @parcel/watcher) s'installent par variante de
# libc : avec une image musl, chaque `pnpm install` fait par le conteneur
# remplace les binaires glibc de l'hôte, et inversement. Le symptôme est
# déroutant — « Cannot find module './rolldown-binding…node' » dans un
# environnement qui marchait la veille.
FROM node:24-slim

# Le cache corepack vit à un chemin fixe et lisible par tous, gravé dans l'image.
ENV COREPACK_HOME=/opt/corepack
RUN corepack enable && corepack prepare pnpm@10.30.3 --activate

# pnpm écrit sa configuration et son état sous $HOME ; le pointer vers un chemin
# accessible en écriture évite qu'un UID arbitraire bute sur /root.
ENV HOME=/tmp

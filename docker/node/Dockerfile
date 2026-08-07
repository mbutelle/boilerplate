# Image de développement partagée par les conteneurs front et par le service
# d'installation. pnpm est installé à la construction (en root) pour que les
# conteneurs puissent ensuite tourner sous l'utilisateur hôte sans jamais avoir à
# écrire dans un emplacement appartenant à root — c'est ce qui garde les fichiers
# générés (SDK orval, .nuxt, node_modules…) à l'utilisateur hôte plutôt qu'à root.
FROM node:24-alpine

# Le cache corepack vit à un chemin fixe et lisible par tous, gravé dans l'image.
ENV COREPACK_HOME=/opt/corepack
RUN corepack enable && corepack prepare pnpm@10.30.3 --activate

# pnpm écrit sa configuration et son état sous $HOME ; le pointer vers un chemin
# accessible en écriture évite qu'un UID arbitraire bute sur /root.
ENV HOME=/tmp

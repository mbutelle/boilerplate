# Socle applicatif Symfony + Nuxt

Socle pour démarrer une application web : API Symfony / API Platform, front Nuxt,
et l'outillage Docker, tests, analyse statique et génération de SDK qui va avec.

Il embarque un contexte métier d'exemple (`Catalog`) qui n'est pas une démo
décorative : c'est la référence à copier pour un nouveau contexte, avec un objet
valeur, une énumération de domaine, une collection paginée et filtrée, et les
cinq écritures passant par le bus de commande.

## Prérequis

- Docker et Docker Compose
- Make

## Démarrage

```bash
make start
```

`make start` écrit d'abord vos UID/GID dans `.env` (cible `env`), pour que les
conteneurs tournent sous votre identité et que les fichiers qu'ils créent vous
appartiennent.

| Application | URL                                |
|-------------|------------------------------------|
| Front       | http://front.boilerplate.localhost |
| API         | http://api.boilerplate.localhost   |
| MailHog     | http://mail.boilerplate.localhost  |

Pour faire tourner plusieurs projets issus de ce socle en même temps, changer les
ports d'entrée :

```bash
HTTP_PORT=8081 TRAEFIK_PORT=8091 make start
```

Créer un premier compte administrateur :

```bash
docker compose exec api bin/console app:create-user vous@example.test --admin
```

## Architecture

Le back est découpé en contextes, chacun en trois couches :

```
src/<Contexte>/
  Domain/          modèle métier pur — ni Doctrine, ni API Platform
    Model/  ValueObject/  Contract/Repository/
  Application/     cas d'usage — ne connaît que les ports de Domain
    Command/<CasDUsage>/  Query/<CasDUsage>/  Event/
  Infrastructure/  tout ce qui touche un framework ou l'extérieur
    ApiPlatform/{Resource,Payload,State}  Doctrine/Repository/  Email/  Security/
```

Le mapping Doctrine vit en XML dans `api/config/doctrine/mapping/`, hors des
classes du domaine. Les tests d'architecture (`tests/Architecture/`) verrouillent
ces frontières.

**Toute écriture passe par le bus de commande** : une Resource expose, un Payload
traduit la requête en commande, et le processor générique `ApplyCommandProcessor`
dispatche. Aucun endpoint n'est un cas particulier. Ne pas oublier l'entrée
`<Modèle> => <Resource>` dans `domain_resource_mapping` (`api_platform.yaml`),
sans quoi le processor lève `No mapping found for …`.

## Commandes

`make help` liste tout. Les principales :

| Commande            | Effet                                            |
|---------------------|--------------------------------------------------|
| `make start` / `stop` / `ps` / `logs` | cycle de vie de la stack      |
| `make start-wait`   | démarre et attend que l'api et le front répondent |
| `make qa`           | linter, phpstan, typecheck et tests, avant toute PR |
| `make test`         | tests back (Pest) et front (Vitest)              |
| `make phpstan`      | analyse statique, niveau 6, sans baseline        |
| `make typecheck`    | types du front (vue-tsc)                         |
| `make linter`       | php-cs-fixer et eslint                           |
| `make sdk`          | exporte l'OpenAPI puis régénère le SDK partagé   |
| `make fix-perms`    | reprend les fichiers laissés en root             |

## Le SDK partagé

Le front n'appelle jamais l'API à la main. `packages/api-client` est généré par
Orval depuis l'OpenAPI exporté par l'API : hooks vue-query, types, et schémas
zod. Les `useCases` du front sont de fins adaptateurs par-dessus ces hooks.

Conséquence directe : un paramètre qui n'est pas déclaré sur l'opération API
Platform n'existe pas dans le SDK, donc le front ne peut pas le demander. C'est
voulu.

Les schémas zod servent deux usages : valider un formulaire contre les
contraintes réellement publiées par l'API (plutôt que des règles recopiées qui
divergent), et vérifier une réponse à l'exécution.

## Versions

### API

- Symfony 7.4, API Platform 4.3, PHP 8.3 (FrankenPHP en worker mode)
- MySQL 8.0, Redis 7
- PHPStan 2 (niveau 6, sans baseline), php-cs-fixer 3, Pest 4

### Front

- Nuxt 4.5, Vue 3.5, PrimeVue 4.5
- Vitest 4, vue-tsc, eslint 10
- Orval 8, TanStack Vue Query 5, zod 4, axios 1

## Tests

```bash
make test
```

Côté back, trois familles : unitaires (`tests/Unit`, sans base ni conteneur),
architecture (`tests/Architecture`), intégration (`tests/Integration`, avec le
noyau).

Côté front, la convention sépare la logique pure (`*.test.ts`, sans Nuxt) des
composants et composables (`*.nuxt.test.ts`). Attention au type de mock :
`mockNuxtImport` pour ce que Nuxt auto-importe, `vi.mock` pour un import
explicite comme le SDK. Se tromper donne un mock silencieusement ignoré.

`tests/` est volontairement exclu de l'analyse PHPStan : elle ne comprend pas le
binding runtime de Pest et produit des milliers de faux positifs.

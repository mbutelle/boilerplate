# Déploiement

Déploiement par releases datées avec bascule atomique d'un lien `current`, via
ansible. Les inventaires livrés sont des exemples : remplacer les hôtes.

## Ce que ce déploiement provisionne, et pourquoi ça compte

Un déploiement qui ne pose que le code applicatif laisse deux trous silencieux :

- **le worker Messenger** — les messages partent dans le transport et personne ne
  les consomme ;
- **les commandes planifiées** — elles ne se déclenchent jamais.

Dans les deux cas il n'y a aucune erreur, aucune trace : juste des effets qui
n'arrivent pas. C'est le défaut le plus coûteux à diagnostiquer, parce que rien
ne pointe vers lui.

Le rôle `runtime` installe donc, en plus de l'application :

| Unité systemd                 | Rôle                                        |
|-------------------------------|---------------------------------------------|
| `boilerplate-worker.service`  | consomme le transport `async`, redémarré à chaque déploiement |
| `boilerplate-cron.service`    | exécute les commandes de `scheduled_commands` |
| `boilerplate-cron.timer`      | déclenche le précédent (`scheduler_on_calendar`) |

Le rôle est joué **après** la bascule de `current`, sinon le worker redémarrerait
sur le code de la release précédente. Et le déploiement échoue si le worker n'est
pas `active` à l'arrivée, plutôt que de rendre la main en donnant l'illusion que
tout va bien.

## Ajouter une commande planifiée

Une seule chose à faire, dans `ansible/group_vars/all.yml` :

```yaml
scheduled_commands:
  - gesdinet:jwt:clear
  - app:ma:commande
```

L'unité systemd est rendue depuis cette liste.

## Secrets

Les valeurs sensibles vivent dans un coffre `ansible-vault`, jamais en clair.
`make vault-create` / `vault-edit` / `vault-view` / `vault-rekey`.

Le fichier `.env.local` de production est rendu depuis
`roles/deploy/templates/api.env.j2` à partir de ces variables.

## Commandes

```bash
make deploy APP=api ENV=staging      # déployer une application
make deploy-all ENV=staging          # déployer tout l'inventaire
make migrate ENV=staging             # migrations seules
```

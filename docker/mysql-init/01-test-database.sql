-- Base de test, créée au premier démarrage du conteneur MySQL.
-- Doctrine ajoute le suffixe `_test` en environnement de test (voir
-- doctrine.yaml, when@test) mais ne crée pas la base : sans ce script, la suite
-- de tests échoue au premier accès avec un « Unknown database ».
CREATE DATABASE IF NOT EXISTS `boilerplate_test`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON `boilerplate_test`.* TO 'boilerplate'@'%';
FLUSH PRIVILEGES;

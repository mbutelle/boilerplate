# Welcome to this Boilerplate

This project is a complete boilerplate for developing modern web applications with a Symfony API and a Nuxt.js client. It also includes Docker container management, tools for linting, testing, and automatic API client generation.

## Prerequisites

Before starting, make sure you have the following tools installed:

- Docker
- Docker Compose
- Make

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-project.git
cd your-project
```
   
### 2. Start the project using Docker

```bash
make start
```

### 3. Access the different parts of the project through the following URLs

| Application             | Link                                |
|-------------------------|-------------------------------------|
| Client                  | http://client.boilerplate.localhost |
| API                     | http://api.boilerplate.localhost    |
| MailHog                 | http://mail.boilerplate.localhost   |


## Tools list

### API

This project uses Symfony and API Platform to manage the API:

- [Symfony](https://github.com/symfony/symfony) 6.4
- [APIPlatform](https://github.com/api-platform/api-platform) 3.3.7

Additional tools for backend development:

- [phpstan](https://github.com/phpstan/phpstan) 1.12
- [php-cs-fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) 3.64
- [Alice fixture](https://github.com/nelmio/alice) 3.13
- [phpunit](https://github.com/sebastianbergmann/phpunit) 9.5

### CLIENT

The frontend is built using Nuxt.js with several tools for API integration and testing:

- [nuxtJS](https://github.com/nuxt/nuxt) 3.13
- [orval](https://github.com/orval-labs/orval) 7.1
- [vitest](https://github.com/vitest-dev/vitest) 2.1.3
- [primevue](https://github.com/primefaces/primevue) 4.1.1
- [axios](https://github.com/axios/axios) 1.7.7


## API Client Generation with Orval

Orval is used to generate a Swagger client from an OpenAPI file. To generate the client based on the APIs created with API Platform, run the following command:

```bash
make generate-front-services
```

The client will be generated in the front/service/client.ts file


Thank you for using this boilerplate! Feel free to contribute or open issues if you encounter any problems.

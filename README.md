### Hexlet tests and linter status:
[![Actions Status](https://github.com/irudenok/php-project-9/actions/workflows/hexlet-check.yml/badge.svg)](https://github.com/irudenok/php-project-9/actions)

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=irudenok_php-project-9&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=irudenok_php-project-9)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=irudenok_php-project-9&metric=bugs)](https://sonarcloud.io/summary/new_code?id=irudenok_php-project-9)
[![Code Smells](https://sonarcloud.io/api/project_badges/measure?project=irudenok_php-project-9&metric=code_smells)](https://sonarcloud.io/summary/new_code?id=irudenok_php-project-9)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=irudenok_php-project-9&metric=coverage)](https://sonarcloud.io/summary/new_code?id=irudenok_php-project-9)
[![Duplicated Lines (%)](https://sonarcloud.io/api/project_badges/measure?project=irudenok_php-project-9&metric=duplicated_lines_density)](https://sonarcloud.io/summary/new_code?id=irudenok_php-project-9)
[![Lines of Code](https://sonarcloud.io/api/project_badges/measure?project=irudenok_php-project-9&metric=ncloc)](https://sonarcloud.io/summary/new_code?id=irudenok_php-project-9)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=irudenok_php-project-9&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=irudenok_php-project-9)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=irudenok_php-project-9&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=irudenok_php-project-9)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=irudenok_php-project-9&metric=sqale_index)](https://sonarcloud.io/summary/new_code?id=irudenok_php-project-9)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=irudenok_php-project-9&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=irudenok_php-project-9)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=irudenok_php-project-9&metric=vulnerabilities)](https://sonarcloud.io/summary/new_code?id=irudenok_php-project-9)

# Описание

["Анализатор страниц"](https://php-project-9-6vna.onrender.com) - сайт, который анализирует указанные страницы на SEO пригодность.

**Ключевые технологии и реализованные задачи:**

Бэкенд: Фреймворк **Slim**.

Работа с данными: Для хранения информации использовалась СУБД **PostgreSQL**, а для выполнения запросов — библиотека **PDO**.

Фронтенд: Визуальная часть была построена на компонентах **Bootstrap** для обеспечения адаптивности и скорости разработки.

## Требования

* Docker
* Linux, Macos, WSL
* PHP >= 8.4
* Make
* Git

## Установка и запуск

```bash
git clone git@github.com:irudenok/php-project-9.git

docker-compose up -d
```
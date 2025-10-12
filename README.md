# Пояснительная записка: как запустить проект

## 1) Требования

* Docker Desktop (или Docker Engine) + Docker Compose v2
* Порты **8080** (приложение) и **5432** (PostgreSQL) должны быть свободны

## 2) Репозиторий и окружение

1. Клонировать репозиторий:

   ```bash
   git clone git@github.com:Sema-qq/routes.git
   cd routes
   ```
2. Проверить настройки БД в `config/db.php`:

   ```php
   return [
       'class' => 'yii\\db\\Connection',
       'dsn' => 'pgsql:host=db;port=5432;dbname=csu',
       'username' => 'dbuser',
       'password' => 'password',
       'charset' => 'utf8',
   ];
   ```

   Параметры совпадают с `docker-compose.yml` (сервис `db`).

## 3) Сборка и запуск

Запускаем инфраструктуру в фоне (первый запуск может занять время):

```bash
docker compose up -d --build
```

После старта приложение доступно на: **[http://localhost:8080](http://localhost:8080)**

## 4) Установка зависимостей PHP

(выполняется внутри контейнера `php`/`yii2-app`)

```bash
docker compose exec php composer install --no-interaction --prefer-dist --no-dev
```

> Если в проекте используются dev-инструменты — уберите `--no-dev`.

## 5) Миграции БД (Yii2)

Выполнить миграции без вопросов:

```bash
docker compose exec php php yii migrate --interactive=0
```

Если есть сиды/инициализация данных — выполнить соответствующие команды (например `php yii fixture/load` или кастомные консоли).

## 6) Проверка работоспособности

* Открыть **[http://localhost:8080](http://localhost:8080)** — должна отобразиться главная страница.
* Проверить соединение с БД: в логах `db` не должно быть ошибок.

## Полезные команды

* Логи сервиса (tail):

  ```bash
  docker compose logs -f php
  docker compose logs -f db
  ```
* Войти в контейнер с PHP (bash):

  ```bash
  docker compose exec php bash
  ```
* Перезапуск сервисов:

  ```bash
  docker compose restart php db
  ```
* Создать дамп БД (custom-формат) из контейнера `db`:

  ```bash
  docker exec -i -e PGPASSWORD=password pgsql \
    pg_dump -U dbuser -d csu -n public -Fc > ./dumps/csu_$(date +%F_%H-%M).dump
  ```

## Остановка и очистка

* Остановить контейнеры:

  ```bash
  docker compose down
  ```
* Полностью удалить том с данными БД (необратимо!):

  ```bash
  docker compose down -v
  ```

## Частые проблемы и решения

* **Порт занят (8080/5432):** освободить порт или изменить его в `docker-compose.yml`.
* **Нет прав на запись** для `runtime/` и `web/assets/`: внутри контейнера выполнить

  ```bash
  chown -R www-data:www-data runtime web/assets
  ```
* **Миграции не видят БД:** убедиться, что сервис `db` в статусе `healthy`, и правильно указан `dsn` (`host=db`).

---

**Итого:** для запуска достаточно:

```bash
docker compose up -d --build
docker compose exec php composer install
docker compose exec php php yii migrate --interactive=0
```

После этого приложение доступно на **[http://localhost:8080](http://localhost:8080)**.

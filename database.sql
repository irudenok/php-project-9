-- Очистка таблиц если они существуют и сеанс только что стартовал
TRUNCATE TABLE url_checks;
TRUNCATE TABLE urls CASCADE;

-- Создание таблицы urls если она не существует
CREATE TABLE IF NOT EXISTS urls (
    id bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    name varchar(255) NOT NULL UNIQUE,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы url_checks если она не существует
CREATE TABLE IF NOT EXISTS url_checks (
    id bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    url_id bigint NOT NULL REFERENCES urls (id) ON DELETE CASCADE,
    status_code smallint,
    h1 varchar(255),
    title varchar(255),
    description text,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP
);

-- Создание индексов для улучшения производительности
CREATE INDEX IF NOT EXISTS idx_urls_name ON urls (name);
CREATE INDEX IF NOT EXISTS idx_url_checks_url_id ON url_checks (url_id);
CREATE INDEX IF NOT EXISTS idx_url_checks_created_at ON url_checks (created_at);
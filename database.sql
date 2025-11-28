DROP TABLE IF EXISTS url_checks;
DROP TABLE IF EXISTS urls;

CREATE TABLE urls (
    id bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    name varchar(255) NOT NULL UNIQUE,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE url_checks (
    id bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    url_id bigint NOT NULL REFERENCES urls (id) ON DELETE CASCADE,
    status_code smallint NULL,
    h1 text NULL,
    title text NULL,
    description text NULL,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_urls_name ON urls (name);
CREATE INDEX idx_url_checks_url_id ON url_checks (url_id);
CREATE INDEX idx_url_checks_created_at ON url_checks (created_at);
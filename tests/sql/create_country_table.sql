CREATE TABLE IF NOT EXISTS country (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name CHAR(255) NOT NULL UNIQUE
);
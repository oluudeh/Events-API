CREATE TABLE IF NOT EXISTS event (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name CHAR(255) NOT NULL,
    city_id INTEGER(11) NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL
);
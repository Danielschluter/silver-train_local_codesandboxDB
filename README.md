# PHP + PostgreSQL Sample

This workspace contains a minimal PHP app served by Nginx and PHP-FPM with a PostgreSQL database using Docker Compose.

Quick start:

```bash
# Build and start services
docker compose up --build

# Open in browser
# Visit http://localhost:8080
```

Default DB credentials (set in `docker-compose.yml`):
- POSTGRES_USER: `user`
- POSTGRES_PASSWORD: `password`
- POSTGRES_DB: `appdb`

Notes:
- PHP files are in `src/`.
- `php/Dockerfile` installs `pdo_pgsql`.
- Exposed ports: PHP site on `8080`, Postgres on `5432`.

If you want to override DB credentials, create a `.env` file or set environment variables before bringing up the stack.

**Azure SQL (Linux)**

To connect this app to an Azure SQL Database from Linux/PHP you need the Microsoft ODBC driver and the `pdo_sqlsrv` (or `sqlsrv`) PHP extensions installed. Set `DB_DRIVER=sqlsrv` and the connection variables (see `src/.env.example`).

Example install steps for Debian/Ubuntu (adjust PHP version):

```bash
# add MS repo
curl https://packages.microsoft.com/keys/microsoft.asc | sudo apt-key add -
curl https://packages.microsoft.com/config/ubuntu/20.04/prod.list | sudo tee /etc/apt/sources.list.d/mssql-release.list
sudo apt-get update
sudo ACCEPT_EULA=Y apt-get install -y msodbcsql17
sudo apt-get install -y unixodbc-dev g++ make autoconf libc-dev pkg-config

# install PECL extensions (replace php7.4 with your PHP version)
sudo apt-get install -y php-dev php-pear
sudo pecl install sqlsrv pdo_sqlsrv

# enable extensions (example path; adjust PHP version and ini path)
echo "extension=sqlsrv.so" | sudo tee /etc/php/7.4/cli/conf.d/20-sqlsrv.ini
echo "extension=pdo_sqlsrv.so" | sudo tee /etc/php/7.4/cli/conf.d/20-pdo_sqlsrv.ini
```

Then set environment variables and run the provided test script:

```bash
# export vars or create a .env file (see src/.env.example)
export DB_DRIVER=sqlsrv
export DB_HOST=your-server.database.windows.net
export DB_PORT=1433
export DB_NAME=your_database
export DB_USER=your_user@your-server
export DB_PASS=your_password

# run test
php src/test_connection.php
```

If you prefer ODBC, install `msodbcsql17` and configure an ODBC DSN, then adjust the DSN in `src/db.php` or use `odbc_connect`.

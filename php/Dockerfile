FROM php:8.2-fpm
RUN apt-get update && apt-get install -y libpq-dev git unzip && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo pdo_pgsql

# Install Microsoft ODBC driver and PHP sqlsrv/pdo_sqlsrv extensions
RUN apt-get update \
	&& apt-get install -y --no-install-recommends gnupg curl apt-transport-https ca-certificates \
	&& curl -sSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor > /etc/apt/trusted.gpg.d/microsoft.gpg \
	&& curl -sSL https://packages.microsoft.com/config/debian/11/prod.list -o /etc/apt/sources.list.d/mssql-release.list \
	&& apt-get update \
	&& ACCEPT_EULA=Y apt-get install -y --no-install-recommends msodbcsql17 unixodbc-dev \
	&& apt-get install -y --no-install-recommends build-essential g++ make autoconf pkg-config libssl-dev \
	&& pecl install sqlsrv pdo_sqlsrv \
	&& docker-php-ext-enable sqlsrv pdo_sqlsrv \
	&& rm -rf /var/lib/apt/lists/* /tmp/pear

WORKDIR /var/www/html

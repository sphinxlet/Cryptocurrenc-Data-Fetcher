# Cryptocurrency-Data-Fetcher

This project retrieves the top 50 cryptocurrencies from the CoinMarketCap API and stores the data in a PostgreSQL database.

Features

- Fetches cryptocurrency data including name, symbol, price, market cap, and volume.

- Stores the data in a PostgreSQL table cryptocurrency_prices.

- Utilizes Composer for dependency management.

Prerequisites

- PHP installed on your system

- PostgreSQL database setup

- Composer installed for package management

- Access to the CoinMarketCap API

Installation

1. Clone the repository:

git clone <your-repo-url>

cd <project-directory>

2. Install Composer dependencies:

composer require guzzlehttp/guzzle

composer require vlucas/phpdotenv

3. Set up the PostgreSQL database:

Create a table by executing the following SQL command:

CREATE TABLE cryptocurrency_prices (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    symbol VARCHAR(10) NOT NULL,
    price NUMERIC(18, 8) NOT NULL,
    market_cap NUMERIC(18, 2) NOT NULL,
    volume NUMERIC(18, 2) NOT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

4. Configure environment variables:

Edit a .env file in the root directory and add your database details and CoinCapMarket API Key:

DB_HOST=your_database_host

DB_NAME=your_database_name

DB_USER=your_database_user

DB_PASS=your_database_password

CMC_API_KEY=your_coinmarketcap_api_key

5. Run the project:

Execute the PHP script to fetch and store data.

php index.php

Usage

- Upon execution, the script connects to the CoinMarketCap API, retrieves the top 50 cryptocurrencies, and inserts their details into the PostgreSQL database.

Dependencies

- Guzzle - HTTP client for API requests.

- vlucas/phpdotenv - Loads environment variables from a .env file.

License

- This project is licensed under the MIT License.

Acknowledgements

- CoinMarketCap for cryptocurrency data.

- The creators of Guzzle and PHP dotenv libraries.

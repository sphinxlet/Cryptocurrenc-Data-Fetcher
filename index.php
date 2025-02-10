<?php
#environment variables
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

#postgresql database connection 
$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Successful connection the database";
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

#CoinMarketCap API URL & key
$apiUrl = "https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest?limit=50";
$apiKey = $_ENV['CMC_API_KEY'];

#initialize curl session
$ch = curl_init();

#set curl options
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-CMC_PRO_API_KEY: $apiKey",
    "Accept: application/json"
]);

#curl timeout
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

#execute curl
$response = curl_exec($ch);

#curl error handling
if (!$response) {
    error_log("API Request failed: " . curl_error($ch));
    exit("An error occurred while fetching data");
}

if(curl_errno($ch)) {
    echo "cURL error: " . curl_error($ch);
}

#close session
curl_close($ch);

#decode requested data
$data = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    exit("Error decoding JSON response.");
}

#insert into postgresql database
$stmt = $pdo->prepare("
    INSERT INTO cryptocurrency_prices (name, symbol, price, market_cap, volume)
    VALUES (:name, :symbol, :price, :market_cap, :volume)
");

foreach ($data['data'] as $crypto) {
    if (!isset($crypto['name'], $crypto['symbol'], $crypto['quote']['USD'])) {
        continue;
    }

    $price = filter_var($crypto['quote']['USD']['price'], FILTER_VALIDATE_FLOAT);
    $marketCap = filter_var($crypto['quote']['USD']['market_cap'], FILTER_VALIDATE_FLOAT);
    $volume = filter_var($crypto['quote']['USD']['volume_24h'], FILTER_VALIDATE_FLOAT);

    $stmt->execute([
        'name' => $crypto['name'],
        'symbol' => $crypto['symbol'],
        'price' => $price,
        'market_cap' => $marketCap,
        'volume' => $volume
    ]);
}

echo "Top 50 cryptocurrencies have been inserted into the database";

?>
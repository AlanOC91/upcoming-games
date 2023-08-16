<?php
require 'vendor/autoload.php';

//Dot ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//Check Cache
$cache = new UpcomingGames\Cache('cache', 21600); // Cache data for 6 hours (21600 seconds)
$platforms = [48, 167]; // PS4 and PS5
$cacheKey = implode(',', $platforms);
$games = $cache->get($cacheKey);

//If no Cache exists, pull from IGDB API
if (!$games) {
    $clientID = $_ENV['CLIENT_ID'];
    $clientSecret = $_ENV['CLIENT_SECRET'];
    //Auth
    $auth = new UpcomingGames\Authentication($clientID, $clientSecret);
    $token = $auth->getAccessToken();
    if (!$token) {
        die('Failed to obtain access token.');
    }
    //IGDB Retrieval
    $igdb = new UpcomingGames\IGDB($clientID, $token);
    $games = $igdb->fetchUpcomingGames($platforms);
    //Sort the Games
    $games = $igdb->sort_list($games);
    //Set Cache
    $cache->set($cacheKey, $games);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_ENV['APP_NAME']; ?> - Upcoming Game Releases</title>
    <meta name="description" content="<?php echo $_ENV['APP_NAME']; ?> offers the latest updates on upcoming game releases. Stay informed about the newest titles and release dates across major platforms.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="assets/favicon.ico" type="image/x-icon">

    <!-- Facebook Meta Tags -->
    <meta property="og:url" content="<?php echo $_ENV['APP_URL']; ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo $_ENV['APP_NAME']; ?> - Upcoming Game Releases">
    <meta property="og:description" content="<?php echo $_ENV['APP_NAME']; ?> offers the latest updates on upcoming game releases. Stay informed about the newest titles and release dates across major platforms.">
    <meta property="og:image" content="/assets/logo.svg">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo $_ENV['APP_URL']; ?>">
    <meta name="twitter:title" content="<?php echo $_ENV['APP_NAME']; ?> - Upcoming Game Releases">
    <meta name="twitter:description" content="<?php echo $_ENV['APP_NAME']; ?> offers the latest updates on upcoming game releases. Stay informed about the newest titles and release dates across major platforms.">
    <meta name="twitter:image" content="/assets/logo.svg">
</head>
<body class="bg-dark text-light">

<div class="container mt-5">

    <!-- Logo -->
    <div class="d-flex justify-content-center">
        <a href="<?php echo $_ENV['APP_SECONDARY_URL']; ?>"><img src="assets/logo.svg" alt="<?php echo $_ENV['APP_NAME']; ?> Logo" width="200"></a>
    </div>

    <h1 class="mb-4">Upcoming Game Releases</h1>

    <p class="lead">This page shows the upcoming game releases. The data is retrieved from the <a href="https://api-docs.igdb.com/#about" target="_blank">IGDB API</a>.</p>

    <p class="text-muted small">Some dates may be incorrect as we are pulling the original release date a game was released on (regardless of platform)</p>

    <table class="table table-striped table-dark">
        <thead>
        <tr>
            <th scope="col">Cover</th>
            <th scope="col">Game Name</th>
            <th scope="col">Release Date</th>
            <th scope="col">Platform</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($games as $game): ?>
            <tr>
                <td>
                    <?php if (isset($game['cover_url']) && $game['cover_url']): ?>
                        <img src="<?php echo htmlspecialchars($game['cover_url']); ?>" alt="<?php echo htmlspecialchars($game['name']); ?> Cover" width="50" loading="lazy">
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($game['name']); ?></td>
                <td><?php $releaseDate = $game['release_dates'][0]['date'] ?? null;
                    echo $releaseDate ? date("Y-m-d", $releaseDate) : 'Unknown Date';  ?></td>
                <td>
                    <?php
                    echo implode(', ', array_map(function($platform) {
                        return htmlspecialchars($platform['name']);
                    }, $game['platforms']));
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
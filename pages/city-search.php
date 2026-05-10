<?php
require_once __DIR__ . '/../includes/functions.php';

$cities = [
    [
        'name' => 'Tokyo',
        'country' => 'Japan',
        'mood' => 'Adventurous',
        'temperature' => '27°C',
        'weather' => 'Sunny',
        'budget' => '$$$',
        'match' => '92%',
        'image' => 'https://images.unsplash.com/photo-1542051841857-5f90071e7989?q=80&w=1200&auto=format&fit=crop',
        'insight' => 'Perfect for high-energy night exploration.',
        'tag' => 'Trending'
    ],
    [
        'name' => 'Santorini',
        'country' => 'Greece',
        'mood' => 'Romantic',
        'temperature' => '24°C',
        'weather' => 'Clear',
        'budget' => '$$$$',
        'match' => '88%',
        'image' => 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?q=80&w=1200&auto=format&fit=crop',
        'insight' => 'Best sunsets for couples and luxury retreats.',
        'tag' => 'Luxury'
    ],
    [
        'name' => 'Bali',
        'country' => 'Indonesia',
        'mood' => 'Spiritual',
        'temperature' => '29°C',
        'weather' => 'Tropical',
        'budget' => '$$',
        'match' => '95%',
        'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?q=80&w=1200&auto=format&fit=crop',
        'insight' => 'Ideal for soul-searching and wellness journeys.',
        'tag' => 'AI Match'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>City Discovery • JourneyOS AI</title>

<link rel="stylesheet" href="../assets/css/design-system.css">
<link rel="stylesheet" href="../assets/css/animations.css">
<link rel="stylesheet" href="../assets/css/pages/search.css">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">

</head>

<body>

<div class="search-page">

    <section class="hero-section">

        <div class="hero-content">

            <span class="hero-badge">
                AI Powered Discovery
            </span>

            <h1>
                Discover Cities That Match Your Mood
            </h1>

            <p>
                Find destinations tailored to your energy, budget, and travel style.
            </p>

        </div>

    </section>

    <section class="search-controls glass-card">

        <input
            type="text"
            id="citySearch"
            placeholder="Search cities, countries, experiences..."
        >

        <div class="filter-row">

            <select>
                <option>All Moods</option>
                <option>Adventurous</option>
                <option>Romantic</option>
                <option>Relaxed</option>
                <option>Luxury</option>
            </select>

            <select>
                <option>Budget</option>
                <option>$</option>
                <option>$$</option>
                <option>$$$</option>
                <option>$$$$</option>
            </select>

            <select>
                <option>Sort By</option>
                <option>Trending</option>
                <option>Highest Rated</option>
                <option>AI Match</option>
            </select>

        </div>

    </section>

    <section class="cities-grid" id="citiesGrid">

        <?php foreach ($cities as $city): ?>

       <div class="city-card glass-card"

    data-name="<?= strtolower($city['name']) ?>"
    data-country="<?= strtolower($city['country']) ?>"
    data-mood="<?= strtolower(trim($city['mood'])) ?>"
    data-budget="<?= trim($city['budget']) ?>"

>

            <div class="city-image">

                <img src="<?= $city['image'] ?>" alt="<?= $city['name'] ?>">

                <div class="image-overlay"></div>

                <div class="city-tag">
                    <?= $city['tag'] ?>
                </div>

            </div>

            <div class="city-content">

                <div class="city-top">

                    <div>

                        <h2>
                            <?= $city['name'] ?>
                        </h2>

                        <p>
                            <?= $city['country'] ?>
                        </p>

                    </div>

                    <div class="match-score">
                        <?= $city['match'] ?>
                    </div>

                </div>

                <div class="city-stats">

                    <div>
                        <span>Weather</span>
                        <strong><?= $city['temperature'] ?> <?= $city['weather'] ?></strong>
                    </div>

                    <div>
                        <span>Budget</span>
                        <strong><?= $city['budget'] ?></strong>
                    </div>

                    <div>
                        <span>Mood</span>
                        <strong><?= $city['mood'] ?></strong>
                    </div>

                </div>

                <div class="insight-box">
                    “<?= $city['insight'] ?>”
                </div>

                <div class="city-actions">

                   <button class="btn-primary"
    onclick="openItinerary('<?= $city['name'] ?>')">
    View City
</button>

                    <button class="btn-secondary">
                        Save
                    </button>

                </div>

            </div>

        </div>

        <?php endforeach; ?>

    </section>

</div>

<script src="../assets/js/search.js"></script>

</body>
</html>
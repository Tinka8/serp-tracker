<?php

$search_for_domain = $_GET["domain"] ?? null;
$search_for_phrase = $_GET["phrase"] ?? null;

$results = json_decode(
  file_get_contents(
    "http://serp-tracker.test/api/?" .
      http_build_query([
        "domain" => $search_for_domain,
        "phrase" => $search_for_phrase,
      ])
  ),
  true
);

$domains = json_decode(
  file_get_contents("http://serp-tracker.test/api/domains/"),
  true
);

$phrases = json_decode(
  file_get_contents("http://serp-tracker.test/api/phrases/"),
  true
);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>SERP tracker</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;700;800&display=swap" rel="stylesheet">

        <style type="text/css">
            .font-headline {
                font-family: 'Kanit', sans-serif;
            }
        </style>
    </head>
    <body class="bg-zinc-200 py-12">
        <div class="max-w-6xl mx-auto">
            <h1 class="uppercase font-extrabold text-2xl pb-6">
                <a href="/" class="text-black hover:text-zinc-800 tracking-tighter">
                    SERP Tracker
                </a>
            </h1>
        </div>
        <div class="max-w-6xl mx-auto bg-white px-6 shadow-xl overflow-hidden z-20">
            <form class="grid grid-cols-4 divide-x divide-zinc-200">
                <div class="py-4 px-6">
                    <label for="domain" class="text-sm font-bold mb-1 block">
                        Search for domain
                    </label>
                    <select name="domain" id="domain" class="py-1 px-2 text-sm border border-t-zinc-300 bg-zinc-200 rounded-sm">
                        <option value="">All domains</option>
                        <?php foreach ($domains as $domain): ?>
                            <option value="<?php echo $domain[
                              "domain"
                            ]; ?>" <?php echo $search_for_domain ===
                                    $domain["domain"]
                                    ? "selected"
                                    : ""; ?>>
                                <?php echo $domain["domain"]; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="py-4 px-6">
                    <label for="phrase" class="text-sm font-bold mb-1 block">
                        Search for phrase
                    </label>
                    <select name="phrase" id="phrase" class="py-1 px-2 text-sm border border-t-zinc-300 bg-zinc-200 rounded-sm">
                        <option value="">All phrases</option>
                        <?php foreach ($phrases as $phrase): ?>
                            <option value="<?php echo $phrase[
                              "phrase"
                            ]; ?>" <?php echo $search_for_phrase ===
                                    $phrase["phrase"]
                                    ? "selected"
                                    : ""; ?>>
                                <?php echo $phrase["phrase"]; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="py-4">
                </div>
                <div class="justify-end flex items-center px-6">
                    <button type="submit" class="rounded shadow-sm border border-lime-300 border-t-lime-100 bg-lime-400 hover:bg-lime-300 text-lime-900 hover:text-lime-950 px-3 py-1 hover:shadow-md">
                        Search
                    </button>
                </div>
            </form>
        </div>

        <div class="max-w-6xl mx-auto mb-12 bg-zinc-800 text-zinc-400 rounded-b-xl px-6 shadow-sm overflow-hidden">
            <form class="grid grid-cols-4 divide-x divide-transparent" method="POST" action="/api/run">
                <div class="py-4 px-6">
                    <label for="search_domain" class="text-sm font-bold mb-1 block">
                        Search for domain
                    </label>
                    <select name="domain" id="search_domain" class="py-1 px-2 text-sm border border-transparent border-b-zinc-700 bg-zinc-900 rounded-sm" required>
                        <option value="">Select domain</option>
                        <?php foreach ($domains as $domain): ?>
                            <option value="<?php echo $domain[
                              "domain"
                            ]; ?>" <?php echo $search_for_domain ===
                                    $domain["domain"]
                                    ? "selected"
                                    : ""; ?>>
                                <?php echo $domain["domain"]; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="py-4 px-6">
                    <label for="search_phrase" class="text-sm font-bold mb-1 block">
                        Search for phrase
                    </label>
                    <input type="text" name="phrase" id="search_phrase" class="py-1 px-2 text-sm border border-transparent border-b-zinc-700 bg-zinc-900 rounded-sm"/>
                </div>
                <div class="py-4 px-6">
                </div>
                <div class="justify-end flex items-center px-6">
                    <button type="submit" class="rounded shadow border border-yellow-300 bg-yellow-400 hover:bg-yellow-500 text-yellow-900 hover:text-yellow-950 px-3 py-1 hover:shadow-md">
                        Get results
                    </button>
                </div>
            </form>

            <?php if (isset($_GET['message'])) : ?>
                <div class="mx-6 bg-zinc-950 border border-zinc-800 rounded-sm p-4 mb-4 text-xs text-white font-mono">
                    <?php echo nl2br($_GET['message']); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="max-w-6xl mx-auto pb-2 bg-white rounded-xl shadow-lg overflow-hidden px-20 py-12">
            <div>
                <canvas id="serpChart"></canvas>
            </div>
        </div>

        <script>
            const ctx = document.getElementById('serpChart');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [
                        <?php foreach ($results['labels'] as $label): ?>
                            '<?php echo $label; ?>',
                        <?php endforeach; ?>
                    ],
                    datasets: [
                        <?php foreach($results['datasets'] as $domain => $keywords): ?>
                            <?php foreach($keywords as $keyword => $positions): ?>
                                {
                                    label: '<?php echo $domain; ?> - <?php echo $keyword; ?>',
                                    data: [
                                        <?php foreach($results['labels'] as $label): ?>
                                            <?php echo isset($positions[$label]) ? $positions[$label]['position'] : $results['max']; ?>,
                                        <?php endforeach; ?>
                                    ]
                                },
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    ]
                },
                options: {
                    // Nastavíme osu tak, aby nejvyšší hodnota byla 1 a nejnižší maximální nalezená - poziční sledování
                    scales: {
                        y: {
                            reverse: true,
                            min: 1,
                            max: <?php echo $results['max']; ?>,
                            title: {
                                display: true,
                                text: 'Pozice'
                            },
                            ticks: {
                                stepSize: 10
                            }
                        }
                    }
                }
            });
        </script>
    </body>
</html>
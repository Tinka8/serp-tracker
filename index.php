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
    </head>
    <body class="bg-zinc-200 py-12">
        <div class="max-w-6xl mx-auto mb-2 bg-white rounded-xl px-6 shadow-sm overflow-hidden">
            <form class="grid grid-cols-4 divide-x divide-zinc-200">
                <div class="py-4 px-6">
                    <label for="domain" class="text-sm font-bold mb-1 block">
                        Search for domain
                    </label>
                    <select name="domain" id="domain" class="py-1 px-2 text-sm border border-zinc-200 rounded-sm">
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
                    <select name="phrase" id="phrase" class="py-1 px-2 text-sm border border-zinc-200 rounded-sm">
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
                <div class="justify-end flex items-center">
                    <button type="submit" class="rounded shadow border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 text-zinc-700 hover:text-zinc-800 px-3 py-1 hover:shadow-md">
                        Search
                    </button>
                </div>
            </form>
        </div>

        <div class="max-w-6xl mx-auto mb-12 bg-zinc-900 text-zinc-400 rounded-xl px-6 shadow-sm overflow-hidden">
            <form class="grid grid-cols-4 divide-x divide-zinc-700" method="POST" action="/api/run">
                <div class="py-4 px-6">
                    <label for="search_domain" class="text-sm font-bold mb-1 block">
                        Search for domain
                    </label>
                    <select name="domain" id="search_domain" class="py-1 px-2 text-sm border border-zinc-700 bg-zinc-950 rounded-sm" required>
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
                    <input type="text" name="phrase" id="search_phrase" class="py-1 px-2 text-sm border border-zinc-700 bg-zinc-950 rounded-sm"/>
                </div>
                <div class="py-4 px-6">
                </div>
                <div class="justify-end flex items-center">
                    <button type="submit" class="rounded shadow border border-yellow-300 bg-yellow-400 hover:bg-yellow-500 text-yellow-900 hover:text-yellow-950 px-3 py-1 hover:shadow-md">
                        Get results
                    </button>
                </div>
            </form>

            <?php if (isset($_GET['message'])) : ?>
                <div class="bg-zinc-950 border border-zinc-800 rounded-sm p-4 mb-4 text-xs text-white font-mono">
                    <?php echo nl2br($_GET['message']); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="max-w-6xl mx-auto pb-2 bg-white rounded-xl shadow-lg overflow-hidden">
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
                            min: <?php echo $results['max']; ?>,
                            max: 1,
                        }
                    }
                }
            });
        </script>
    </body>
</html>
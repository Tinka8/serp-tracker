<?php 
// /index.php

$results = json_decode(file_get_contents('http://serp-tracker.test/api/'), true);

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Serp tracker</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body class="bg-zinc-200 py-12">
        <div class="max-w-6xl mx-auto pb-2 bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="mb-4 border-b border-zinc-200 bg-zinc-50 py-6 px-12">
                <table class="table-fixed w-full text-sm">
                    <thead>
                        <tr>
                            <th class="text-left w-80">Search for domain</th>
                            <th class="text-left w-80">Search for phrase</th>
                            <th class="text-left">Position</th>
                            <th class="text-left">Current time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($results as $result) : ?>
                            <tr>
                                <td>
                                    <?php echo $result['search_for_domain']; ?>
                                </td>
                                <td>
                                    <?php echo $result['search_for_phrase']; ?>
                                </td>
                                <td>
                                    <?php echo $result['position']; ?>
                                </td>
                                <td>
                                    <?php echo $result['current_time']; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

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
                        <?php foreach($results as $result): ?>
                            '<?php echo $result['current_time']; ?>',
                        <?php endforeach; ?>
                    ],
                    datasets: [
                        {
                            label: 'Position',
                            data: [
                                <?php foreach($results as $result): ?>
                                    <?php echo $result['position']; ?>,
                                <?php endforeach; ?>
                            ]
                        }
                    ]
                },
                options: {
                    // Nastavíme osu tak, aby nejvyšší hodnota byla 1 a nejnižší 10 - poziční sledování
                    scales: {
                        y: {
                            reverse: true,
                            min: 10,
                            max: 1,
                        }
                    }
                }
            });
        </script>

    </body>
</html>
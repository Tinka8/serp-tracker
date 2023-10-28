<?php 

$results = json_decode(file_get_contents('http://serp-tracker.test/api/'), true);

?>
<!DOCTYPE html>
<html>
    <head>
        <title>SERP tracker</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body>
        <table class="border border-gray-400 table-fixed w-full">
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
    </body>
</html>
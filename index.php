<?php
// /index.php

// Načteme data z interní API
$results = json_decode(file_get_contents('http://serp-tracker.test/api/'), true);

?>
<!DOCTYPE html>
<html>
    <head>

    </head>
    <body>
        <table border="1">
            <thead>
                <tr>
                    <th>Search for domain</th>
                    <th>Search for phrase</th>
                    <th>Position</th>
                    <th>Current time</th>
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
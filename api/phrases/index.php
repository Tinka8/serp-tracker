<?php

include "../../db.php";

$sql = "SELECT distinct(search_for_phrase) as `phrase` FROM serp_results";

include "../../api.php";

echo json_encode($data);
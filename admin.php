<?php

include './db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'create_preset':
            $search_for_domain = $_POST['search_for_domain'];
            $search_for_phrase = $_POST['search_for_phrase'];

            $sql = "INSERT INTO serp_presets (search_for_domain, search_for_phrase) VALUES ('$search_for_domain', '$search_for_phrase')";

            $conn->query($sql);

            break;

        case 'update_preset':
            $id = $_POST['id'];
            $search_for_domain = $_POST['search_for_domain'];
            $search_for_phrase = $_POST['search_for_phrase'];

            $sql = "UPDATE serp_presets SET search_for_domain = '$search_for_domain', search_for_phrase = '$search_for_phrase' WHERE id = $id";

            $conn->query($sql);

            break;
    }
}

if (isset($_GET['delete'])) {
    $delete = $_GET['delete'];

    $sql = "DELETE FROM serp_presets WHERE id = $delete";

    $conn->query($sql);
}

$sql = 'SELECT id, search_for_domain, search_for_phrase FROM serp_presets';

$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$conn->close();
?>
<div class="max-w-6xl mx-auto bg-white rounded-xl mt-6 p-6">
    <table class="w-full border text-sm">
        <thead class="bg-zinc-50">
            <tr>
                <th class="text-left border px-1 py-1">
                    ID
                </th>
                <th class="text-left border px-1 py-1">
                    Search for domain
                </th>
                <th class="text-left border px-1 py-1">
                    Search for phrase
                </th>
                <th class="border"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <?php if (isset($_GET['edit']) && $_GET['edit'] === $row['id']): ?>
                    <form method="POST" action="/" class="inline-block">
                    <tr>
                        <td class="border px-1 py-1">
                            <input type="hidden" name="action" value="update_preset">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <?php echo $row['id']; ?>
                        </td>
                        <td class="border px-1 py-1">
                            <input type="text" name="search_for_domain" 
                            class="w-full py-1 px-2 text-sm border border-t-zinc-300 bg-zinc-200 rounded-sm"  
                            value="<?php echo $row['search_for_domain']; ?>" 
                            required>
                        </td>
                        <td class="border px-1 py-1">
                            <input type="text" name="search_for_phrase" 
                            class="w-full py-1 px-2 text-sm border border-t-zinc-300 bg-zinc-200 rounded-sm"  
                            value="<?php echo $row['search_for_phrase']; ?>" 
                            required>
                        </td>
                        <td class="border px-1 py-1 text-right">
                            <button type="submit" class="text-sm border border-zinc-600 bg-zinc-100 hover:bg-zinc-200 px-1 py-0.5 inline-block">Update</button>
                        </td>
                    </tr>
                    </form>
                <?php else: ?>
                    <tr>
                        <td class="border px-1 py-1">
                            <?php echo $row['id']; ?>
                        </td>
                        <td class="border px-1 py-1">
                            <?php echo $row['search_for_domain']; ?>
                        </td>
                        <td class="border px-1 py-1">
                            <?php echo $row['search_for_phrase']; ?>
                        </td>
                        <td class="border px-1 py-1 text-right">
                            <a href="/?edit=<?php echo $row[
                                'id'
                            ]; ?>" class="text-sm border border-zinc-600 bg-zinc-100 hover:bg-zinc-200 px-1 py-0.5 inline-block">Edit</a>
                            <a href="/?delete=<?php echo $row[
                                'id'
                            ]; ?>" class="text-sm border border-zinc-600 bg-zinc-100 hover:bg-zinc-200 px-1 py-0.5 inline-block">Delete</a>
                            <form method="POST" action="/api/run" class="inline-block">
                                <input type="hidden" name="domain" value="<?php echo $row['search_for_domain']; ?>">
                                <input type="hidden" name="phrase" value="<?php echo $row['search_for_phrase']; ?>">
                                <input type="hidden" name="number" value="100">
                                <button type="submit" class="text-sm border border-zinc-600 bg-zinc-100 hover:bg-zinc-200 px-1 py-0.5 inline-block">Run</button>
                            </form>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>

        <form method="POST">
            <input type="hidden" name="action" value="create_preset">
            <tfoot class="bg-zinc-50">
                <tr>
                    <td class="border px-1 py-1"></td>
                    <td class="border px-1 py-1">
                        <input 
                            type="text" 
                            name="search_for_domain" 
                            placeholder="Search for domain" 
                            id="add_search_for_domain" 
                            class="w-full py-1 px-2 text-sm border border-t-zinc-300 bg-zinc-200 rounded-sm" 
                            required>
                    </td>
                    <td class="border px-1 py-1">
                        <input 
                            type="text" 
                            name="search_for_phrase" 
                            placeholder="Search for phrase" 
                            id="add_search_for_phrase" 
                            class="w-full py-1 px-2 text-sm border border-t-zinc-300 bg-zinc-200 rounded-sm" 
                            required>
                    </td>
                    <td class="border px-1 py-1 text-right">
                        <button 
                            type="submit" 
                            class="rounded shadow-sm border border-lime-300 border-t-lime-100 bg-lime-400 hover:bg-lime-300 text-lime-900 hover:text-lime-950 px-3 py-1 hover:shadow-md">Add</button>
                    </td>
                </tr>
            </tfoot>
        </form>
    </table> 
</div>
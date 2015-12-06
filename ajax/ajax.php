<?php

if (isset($_POST['task'])) {

    $file = "tasks.json";
    $file_data = json_decode(file_get_contents($file), true);

    $max = max(array_values($file_data));

    $id = $max['id'] + 1;

    $new_task = array(
        'id' => $id,
        'task' => $_POST['task'],
        'type' => $_POST['type'],
    );

    array_push($file_data, $new_task);

    $fh = fopen($file, 'w') or die("can't open file");

    fwrite($fh, json_encode($file_data));
    fclose($fh);

    echo $id;
}

if (isset($_POST['reset']) && $_POST['reset'] == true) {

    $reset_file = "reset.json";
    $new_file = "tasks.json";

    if (!copy($reset_file, $new_file)) {
        echo "failed to copy $reset_file...\n";
    } else {
        file_get_contents($new_file);
    }

}

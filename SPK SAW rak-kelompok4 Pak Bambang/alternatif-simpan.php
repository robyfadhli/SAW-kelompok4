<?php
require "include/conn.php";
$name = $_POST['name'];
// $x = $db->query($sql);
// var_dump($x);
$sql = "REPLACE INTO saw_alternatives (name) VALUES ('$name')";

if ($db->query($sql) === true) {
    echo "<script>
    alert('Data berhasil disimpan!');
    window.location.href = './alternatif.php';
</script>";
} else {
    echo "Error: " . $sql . "<br>" . $db->error;
}


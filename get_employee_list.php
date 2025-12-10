<?php
// Database connection
include 'connection.php';

$country = isset($_POST['country']) ? $_POST['country'] : '';
$store = isset($_POST['store']) ? substr($_POST['store'], 0, 3) : '';
//$country = "El Salvador";
//$store = "A90";

$where = " WHERE 1";

if ($country != '') {
    $where .= " AND (
        CASE 
            WHEN e.SBS_NAME = 'EL SALVADOR' THEN 'El Salvador'
            WHEN e.SBS_NAME = 'GUATEMALA' THEN 'Guatemala'
            WHEN e.SBS_NAME = 'COSTA RICA' THEN 'Costa Rica'
            WHEN e.SBS_NAME = 'HONDURAS' THEN 'Honduras'
            WHEN e.SBS_NAME = 'NICARAGUA' THEN 'Nicaragua'
            ELSE e.SBS_NAME
        END
    ) = '$country'";
}

if ($store != '') {
    $where .= " AND e.STORE_CODE = '$store'";
}

$query = "SELECT e.EMPL_NAME , e.RPRO_FULL_NAME
    FROM EMPLOYEE e $where";

$result = $conn->query($query);

$options_e = "";
while($row = $result->fetch_assoc()) {
    $empl_name = $row['EMPL_NAME'];
    $rpro_full_name = $row['RPRO_FULL_NAME'];
    $options_e .= "<option value='$empl_name - $rpro_full_name'>$empl_name - $rpro_full_name</option>";
}

echo $options_e;
?>
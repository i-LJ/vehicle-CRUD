<?php

// Επιστρέφει το όχημα με το ID που έχουμε δηλώσει
function getVehicleById($pdo, $id)
{
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    $vehicle = $stmt->fetch();

    if (!$vehicle) {
        http_response_code(404);
        echo json_encode(['error' => "Vehicle $id not found."]);
        return;
    }

    echo json_encode($vehicle);
}

// Επιστρέφει όλα τα οχήματα, με προεραιτικά φίλτρα & ταξινόμηση
function getVehicles($pdo)
{
    $conditions = [];
    $params     = [];
    
    // Προσθήκη φίλτρων μόνο αν δηλώθηκαν στο request
    if (isset($_GET['price_min']) && is_numeric($_GET['price_min'])) {
        $conditions[]         = 'price >= :price_min';
        $params[':price_min'] = (float)$_GET['price_min'];
    }

    if (isset($_GET['price_max']) && is_numeric($_GET['price_max'])) {
        $conditions[]         = 'price <= :price_max';
        $params[':price_max'] = (float)$_GET['price_max'];
    }

    if (isset($_GET['transmission']) && in_array($_GET['transmission'], ['manual', 'automatic'])) {
        $conditions[]              = 'transmission = :transmission';
        $params[':transmission']   = $_GET['transmission'];
    }

    if (isset($_GET['type_id']) && is_numeric($_GET['type_id'])) {
        $conditions[]        = 'type_id = :type_id';
        $params[':type_id']  = (int)$_GET['type_id'];
    }

    // Δημιουργία του WHERE clause από ό,τι φίλτρα προστέθηκαν στο πάνω βήμα
    if (count($conditions) > 0) {
        $where = 'WHERE ' . implode(' AND ', $conditions);
    } else {
        $where = '';
    }

    // Αφήνουμε μόνο συγκεγκριμένες τιμές για την ταξινόμηση για την αποφυγή πιθανών SQL injection
    $allowedSorts = [
        'name_asc'   => 'model_name ASC',
        'name_desc'  => 'model_name DESC',
        'price_asc'  => 'price ASC',
        'price_desc' => 'price DESC',
    ];

    if (isset($_GET['sort']) && array_key_exists($_GET['sort'], $allowedSorts)) {
        $sort = $allowedSorts[$_GET['sort']];
    } else {
        $sort = 'id ASC';
    }

    $stmt = $pdo->prepare("SELECT * FROM vehicles $where ORDER BY $sort");
    $stmt->execute($params);
    $vehicles = $stmt->fetchAll();

    echo json_encode($vehicles);
}

// Δημιουργία νέου οχήματος από τα δεδομένα του request
function createVehicle($pdo)
{
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON.']);
        return;
    }

    $errors = validateNewVehicle($data);

    if (count($errors) > 0) {
        http_response_code(422);
        echo json_encode(['errors' => $errors]);
        return;
    }

    $stmt = $pdo->prepare("
        INSERT INTO vehicles (model_name, type_id, vehicle_type, doors, transmission, fuel, price)
        VALUES (:model_name, :type_id, :vehicle_type, :doors, :transmission, :fuel, :price)
    ");

    $stmt->execute([
        ':model_name'   => $data['model_name'],
        ':type_id'      => (int)$data['type_id'],
        ':vehicle_type' => isset($data['vehicle_type']) ? $data['vehicle_type'] : null,
        ':doors'        => isset($data['doors']) ? (int)$data['doors'] : null,
        ':transmission' => isset($data['transmission']) ? $data['transmission'] : null,
        ':fuel'         => isset($data['fuel']) ? $data['fuel'] : null,
        ':price'        => isset($data['price']) ? (float)$data['price'] : null,
    ]);

    // Επιστροφή του νέου οχήματος που δημιουργήθηκε
    $newId = $pdo->lastInsertId();
    $stmt  = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->execute([$newId]);
    $vehicle = $stmt->fetch();

    http_response_code(201);
    echo json_encode($vehicle);
}

// Ενημέρωση υπάρχοντος οχήματος βάσει του ID
function updateVehicle($pdo, $id)
{
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON.']);
        return;
    }

    $errors = validateUpdateVehicle($data);

    if (count($errors) > 0) {
        http_response_code(422);
        echo json_encode(['errors' => $errors]);
        return;
    }

    // Έλεγχος αν το όχημα υπάρχει πριν προσπαθήσουμε να το ενημερώσουμε
    $check = $pdo->prepare("SELECT id FROM vehicles WHERE id = ?");
    $check->execute([$id]);

    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => "Vehicle $id not found."]);
        return;
    }

    // Ενημέρωση μόνο των πεδίων που συμπεριλήφθηκαν στο request
    $allowed = ['model_name', 'type_id', 'vehicle_type', 'doors', 'transmission', 'fuel', 'price'];
    $fields  = [];
    $params  = [':id' => $id];

    foreach ($allowed as $field) {
        if (array_key_exists($field, $data)) {
            $fields[]          = "$field = :$field";
            $params[":$field"] = $data[$field];
        }
    }

    if (count($fields) > 0) {
        $setClause = implode(', ', $fields);
        $pdo->prepare("UPDATE vehicles SET $setClause WHERE id = :id")->execute($params);
    }

    // Επιστροφή του ενημερωμένου οχήματος
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    $vehicle = $stmt->fetch();

    echo json_encode($vehicle);
}

// Διαγραφή οχήματος με βάσει του ID του
function deleteVehicle($pdo, $id)
{
    $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => "Vehicle $id not found."]);
        return;
    }

    echo json_encode(['message' => "Vehicle $id was deleted successfully."]);
}

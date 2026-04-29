<?php

// Eπικύρωση των δεδομένων κατά τη δημιουργία νέου οχήματος.
// Επιστρέφει πάντα ένα πίνακα με τα error messages. Αν είναι άδειος, τότε δεν υπάρχει κανένα σφάλμα.

function validateNewVehicle($data)
{
    $errors = [];

    if (empty($data['model_name'])) {
        $errors[] = 'model_name is required.';
    }

    if (empty($data['type_id']) || !is_numeric($data['type_id'])) {
        $errors[] = 'type_id is required and must be a number.';
    }

    if (isset($data['price']) && (!is_numeric($data['price']) || $data['price'] < 0)) {
        $errors[] = 'price must be a number and cannot be negative.';
    }

    if (isset($data['doors']) && (!is_numeric($data['doors']) || $data['doors'] <= 0)) {
        $errors[] = 'doors must be a positive number.';
    }

    if (isset($data['transmission']) && !in_array($data['transmission'], ['manual', 'automatic'])) {
        $errors[] = 'transmission must be either manual or automatic.';
    }

    if (isset($data['fuel']) && !in_array($data['fuel'], ['petrol', 'diesel', 'hybrid', 'electric'])) {
        $errors[] = 'fuel must be one of: petrol, diesel, hybrid, electric.';
    }

    return $errors;
}

// Επικύρωση των δεδομένων κατά την ενημέρωση υπάρχοντος οχήματος.
// Όλα τα πεδία είναι προεραιτικά, αλλά αν ενημερώνονται, ελέγχεται εκ νέου η εγκυρότητά τους.

function validateUpdateVehicle($data)
{
    $errors = [];

    if (isset($data['model_name']) && empty($data['model_name'])) {
        $errors[] = 'model_name cannot be empty.';
    }

    if (isset($data['type_id']) && !is_numeric($data['type_id'])) {
        $errors[] = 'type_id must be a number.';
    }

    if (isset($data['price']) && (!is_numeric($data['price']) || $data['price'] < 0)) {
        $errors[] = 'price must be a number and cannot be negative.';
    }

    if (isset($data['doors']) && (!is_numeric($data['doors']) || $data['doors'] <= 0)) {
        $errors[] = 'doors must be a positive number.';
    }

    if (isset($data['transmission']) && !in_array($data['transmission'], ['manual', 'automatic'])) {
        $errors[] = 'transmission must be either manual or automatic.';
    }

    if (isset($data['fuel']) && !in_array($data['fuel'], ['petrol', 'diesel', 'hybrid', 'electric'])) {
        $errors[] = 'fuel must be one of: petrol, diesel, hybrid, electric.';
    }

    return $errors;
}

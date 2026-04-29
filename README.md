# API Διαχείρισης Οχημάτων

Ένα απλό PHP REST API για διαχείριση οχημάτων.

---

## Πως να τρέξει

### Προαπαιτούμενα
- PHP 8.1+
- MySQL

### 1. Ρύθμιση της βάσης δεδομένων 

```bash
mysql -u root -p < schema.sql
```

Αυτο δημιουργεί το `vehicles_db`, μια βάση δεδομένων με λίγα δοκιμαστικά δεδομένα.

### 2. Ρύθμιση της σύνδεσης

Ανοίγουμε το αρχείο `db.php` για να ενημερώσουμε τα credentials αν χρειάζεται:

```php
$host = 'localhost';
$db   = 'vehicles_db';
$user = 'root';
$pass = '';
```

### 3. Εκκίνηση του server

```bash
php -S localhost:8000
```

---

## Endpoints

| Method | URL | Περιγραφή |
|--------|-----|-------------|
| GET | `/vehicles` | Λίστα όλων των οχημάτων |
| POST | `/vehicles` | Δημιουργία νέου οχήματος |
| PUT | `/vehicles/{id}` | Ενημέρωση υπάρχοντος οχήματος |
| DELETE | `/vehicles/{id}` | Διαγραφή οχήματος |

### Φίλτρα & Ταξινόμηση

```
GET /vehicles?sort=price_asc
GET /vehicles?price_min=100&price_max=200
GET /vehicles?transmission=automatic
GET /vehicles?type_id=3
GET /vehicles?type_id=2&transmission=automatic&price_min=100&sort=price_asc
```

### Παράδειγμα — Δημιουργία ενός οχήματος
 
```bash
curl -X POST http://localhost:8000/vehicles \
  -H "Content-Type: application/json" \
  -d '{
    "model_name": "Fiat Panda",
    "type_id": 2,
    "vehicle_type": "car",
    "doors": 4,
    "transmission": "manual",
    "fuel": "petrol",
    "price": 90
  }'
```



---

## Υποθέσεις

- Ως `type_id` ορίζω την κατηγορία του οχήματος (1=economy, 2=compact, 3=suv, 4=luxury, 5=van). Δεν συμπεριλήφθηκε ξεχωριστός πίνακας `vehicle_types` για λόγους απλότητας, το type_id αποθηκεύεται απλώς ως άλλη μια τιμή.
- Τα `PUT` requests ενημερώνουν μόνο τα πεδία που συμπεριλήφθηκαν στο αίτημα (μερική ενημέρωση), ώστε να μην χρειάζεται να σταλεί όλοκληρο το αντικείμενο.
- Δεν εφαρμόστηκε αυθεντικοποίηση καθώς δεν ζητήθηκε.

## Τι θεώρησα πιο σημαντικό

- **Data validation** - λανθασμένες καταχωρήσεις απορρίπτονται με ξεκάθαρα μηνύματα σφάλματος πριν φτάσουν την βάση δεδομένων.
- **Επιφύλαξη από SQL injection** - όλα τα queries χρησιμοποιούν PHP Data Objects (PDO) για προετοιμασμένα statements. Η ταξινόμηση γίνεται μόνο μέσω allowlist.
- **Ξεκάθαρη δομή** - Η λογική είναι χωρισμένη με βάση την λειτουργία: `index.php` (routing), `db.php` (σύνδεση με βάση), `validation.php` (κανόνες καταχώρησης), `vehicles.php` (CRUD).
<?php
// seller/save-pet.php  — handles both add and edit
require_once '../includes/auth_check.php';
requireRole('seller');
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: dashboard.php'); exit; }

$action   = $_POST['action'] ?? 'add';
$petId    = (int)($_POST['pet_id'] ?? 0);
$sellerId = $_SESSION['user_id'];

// ── Inputs ────────────────────────────────────
$name        = trim($_POST['name']        ?? '');
$type        = trim($_POST['type']        ?? '');
$breed       = trim($_POST['breed']       ?? '');
$age         = (float)($_POST['age_years']?? 0);
$gender      = trim($_POST['gender']      ?? '');
$city        = trim($_POST['city']        ?? '');
$description = trim($_POST['description'] ?? '');
$health      = trim($_POST['health_info'] ?? '');

// ── Validation ────────────────────────────────
$errors = [];
if (empty($name))        $errors[] = 'Pet name is required.';
if (!in_array($type, ['dog','cat','rabbit','bird','other'])) $errors[] = 'Select a valid pet type.';
if ($age < 0)            $errors[] = 'Age cannot be negative.';
if (!in_array($gender, ['male','female'])) $errors[] = 'Select a gender.';
if (empty($city))        $errors[] = 'City is required.';
if (empty($description)) $errors[] = 'Description is required.';

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old']    = compact('name','type','breed','age','gender','city','description','health');
    $back = $action === 'edit' ? "edit-pet.php?id=$petId" : 'add-pet.php';
    header("Location: $back"); exit;
}

// ── Image upload ──────────────────────────────
$imagePath = null;
if (!empty($_FILES['pet_image']['name'])) {
    $allowed = ['image/jpeg','image/png','image/webp'];
    $maxSize = 2 * 1024 * 1024;

    if (!in_array($_FILES['pet_image']['type'], $allowed)) {
        $_SESSION['errors'] = ['Only JPG, PNG, WEBP images are allowed.'];
        header('Location: ' . ($action === 'edit' ? "edit-pet.php?id=$petId" : 'add-pet.php')); exit;
    }
    if ($_FILES['pet_image']['size'] > $maxSize) {
        $_SESSION['errors'] = ['Image must be under 2MB.'];
        header('Location: ' . ($action === 'edit' ? "edit-pet.php?id=$petId" : 'add-pet.php')); exit;
    }

    $uploadDir = '../uploads/pets/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $ext      = pathinfo($_FILES['pet_image']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('pet_', true) . '.' . $ext;
    move_uploaded_file($_FILES['pet_image']['tmp_name'], $uploadDir . $filename);
    $imagePath = 'uploads/pets/' . $filename;
}

// ── Save to DB ────────────────────────────────
if ($action === 'add') {
    $img = $imagePath ?? 'images/pets/default.jpg';
    $stmt = $pdo->prepare(
        'INSERT INTO pets (name,type,breed,age_years,gender,city,description,health_info,image_path,listed_by)
         VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10)'
    );
    $stmt->execute([$name,$type,$breed,$age,$gender,$city,$description,$health,$img,$sellerId]);
    $_SESSION['success'] = "$name has been listed successfully!";

} elseif ($action === 'edit') {
    // Make sure this pet belongs to this seller
    $check = $pdo->prepare('SELECT id,image_path FROM pets WHERE id=$1 AND listed_by=$2');
    $check->execute([$petId, $sellerId]);
    $existing = $check->fetch();
    if (!$existing) { http_response_code(403); die('Forbidden'); }

    $img = $imagePath ?? $existing['image_path'];
    $stmt = $pdo->prepare(
        'UPDATE pets SET name=$1,type=$2,breed=$3,age_years=$4,gender=$5,city=$6,
         description=$7,health_info=$8,image_path=$9 WHERE id=$10 AND listed_by=$11'
    );
    $stmt->execute([$name,$type,$breed,$age,$gender,$city,$description,$health,$img,$petId,$sellerId]);
    $_SESSION['success'] = "$name has been updated!";
}

header('Location: dashboard.php');
exit;
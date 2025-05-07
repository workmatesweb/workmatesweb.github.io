<?php
require_once 'config.php';

// Initialize filters
$filters = [
    'categories' => $_GET['categories'] ?? [],
    'experience' => $_GET['experience'] ?? [],
    'salary_range' => $_GET['salary_range'] ?? [],
    'location' => $_GET['location'] ?? '',
    'gender' => $_GET['gender'] ?? '',
    'english_level' => $_GET['english_level'] ?? ''
];

// Convert comma-separated strings to arrays if needed
if (!is_array($filters['categories']) && !empty($filters['categories'])) {
    $filters['categories'] = explode(',', $filters['categories']);
}
if (!is_array($filters['experience']) && !empty($filters['experience'])) {
    $filters['experience'] = explode(',', $filters['experience']);
}
if (!is_array($filters['salary_range']) && !empty($filters['salary_range'])) {
    $filters['salary_range'] = explode(',', $filters['salary_range']);
}

// Base query
$sql = "SELECT 
            f.id,
            f.full_name,
            f.profile_picture,
            f.job_title,
            f.skills,
            f.location,
            f.expected_salary,
            f.rating,
            f.experience_level,
            f.gender,
            f.english_level,
            f.job_category
        FROM freelancers f
        WHERE f.availability = 1";

// Apply filters
$params = [];
$types = '';

// Categories filter
if (!empty($filters['categories'])) {
    $placeholders = implode(',', array_fill(0, count($filters['categories']), '?'));
    $sql .= " AND f.job_category IN ($placeholders)";
    $params = array_merge($params, $filters['categories']);
    $types .= str_repeat('s', count($filters['categories']));
}

// Experience filter
if (!empty($filters['experience'])) {
    $placeholders = implode(',', array_fill(0, count($filters['experience']), '?'));
    $sql .= " AND f.experience_level IN ($placeholders)";
    $params = array_merge($params, $filters['experience']);
    $types .= str_repeat('s', count($filters['experience']));
}

// Salary range filter
if (!empty($filters['salary_range'])) {
    $salaryConditions = [];
    foreach ($filters['salary_range'] as $range) {
        if ($range === 'under_300') {
            $salaryConditions[] = "f.expected_salary < 300000";
        } elseif ($range === '300_800') {
            $salaryConditions[] = "(f.expected_salary >= 300000 AND f.expected_salary <= 800000)";
        } elseif ($range === 'above_900') {
            $salaryConditions[] = "f.expected_salary > 900000";
        }
    }
    if (!empty($salaryConditions)) {
        $sql .= " AND (" . implode(' OR ', $salaryConditions) . ")";
    }
}

// Location filter
if (!empty($filters['location'])) {
    $sql .= " AND f.location = ?";
    $params[] = $filters['location'];
    $types .= 's';
}

// Gender filter
if (!empty($filters['gender'])) {
    $sql .= " AND f.gender = ?";
    $params[] = $filters['gender'];
    $types .= 's';
}

// English level filter
if (!empty($filters['english_level'])) {
    $sql .= " AND f.english_level = ?";
    $params[] = $filters['english_level'];
    $types .= 's';
}

// Prepare and execute query
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$freelancers = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();

// Return JSON for AJAX requests
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    echo json_encode($freelancers);
    exit();
}
?>
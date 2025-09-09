<?php
declare(strict_types=1);

$path = rtrim($path, '/') ?: '/';
$pdo = Database::connection();

function render(string $view, array $data = []): void {
    extract($data);
    include __DIR__ . '/../views/layout/header.php';
    include __DIR__ . '/../views/layout/nav.php';
    include __DIR__ . '/../views/' . $view . '.php';
    include __DIR__ . '/../views/layout/footer.php';
}

switch ($path) {
    case '/':
        auth_require();
        render('dashboard', ['title' => 'Dashboard']);
        break;
    case '/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = (string)($_POST['password'] ?? '');
            if ($username && $password && auth_login($pdo, $username, $password)) {
                header('Location: /');
                exit;
            }
            $error = 'Invalid credentials';
        }
        include __DIR__ . '/../views/auth/login.php';
        break;
    case '/logout':
        auth_logout();
        header('Location: /login');
        break;
    case '/equipment':
        auth_require();
        $stmt = $pdo->query('SELECT id, asset_tag, name, type, status, location FROM equipment ORDER BY name');
        $equipment = $stmt->fetchAll();
        render('equipment/index', ['title' => 'Equipment', 'equipment' => $equipment]);
        break;
    case '/equipment/create':
        auth_require();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
        $assetTag = trim($_POST['asset_tag'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $location = trim($_POST['location'] ?? '');
        if ($assetTag && $name && $type) {
            $stmt = $pdo->prepare('INSERT INTO equipment (asset_tag, name, type, location) VALUES (?,?,?,?)');
            try {
                $stmt->execute([$assetTag, $name, $type, $location ?: null]);
                header('Location: /equipment');
                exit;
            } catch (Throwable $e) {
                $error = 'Failed to create equipment: ' . $e->getMessage();
            }
        } else {
            $error = 'Please fill in required fields';
        }
        $stmt = $pdo->query('SELECT id, asset_tag, name, type, status, location FROM equipment ORDER BY name');
        $equipment = $stmt->fetchAll();
        render('equipment/index', ['title' => 'Equipment', 'equipment' => $equipment, 'error' => $error ?? null]);
        break;
    default:
        http_response_code(404);
        echo 'Not Found';
}


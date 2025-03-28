<?php
session_start();
require 'config.php';

// Avtorizatsiya tekshirish
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Buyurtmalarni olish
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oqtepa Lavash - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .order-card {
            transition: all 0.3s;
            margin-bottom: 15px;
        }
        .order-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .status-new { background-color: #fff3cd; }
        .status-processing { background-color: #cce5ff; }
        .status-completed { background-color: #d4edda; }
        .status-cancelled { background-color: #f8d7da; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Oqtepa Lavash Admin</a>
            <div class="navbar-nav">
                <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Chiqish</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Buyurtmalar Ro'yxati</h2>
        
        <div class="row mt-4">
            <?php foreach ($orders as $order): ?>
                <div class="col-md-6">
                    <div class="card order-card status-<?= $order['status'] ?>">
                        <div class="card-header d-flex justify-content-between">
                            <span>Buyurtma #<?= $order['id'] ?></span>
                            <span class="badge bg-secondary"><?= $order['created_at'] ?></span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= $order['customer_name'] ?></h5>
                            <p class="card-text"><?= $order['phone_number'] ?></p>
                            <p class="card-text"><strong>Manzil:</strong> <?= $order['address'] ?></p>
                            
                            <h6>Buyurtmalar:</h6>
                            <ul>
                                <?php 
                                $items = json_decode($order['order_items'], true);
                                foreach ($items as $item): ?>
                                    <li><?= $item['name'] ?> - <?= $item['quantity'] ?> x <?= $item['price'] ?> so'm</li>
                                <?php endforeach; ?>
                            </ul>
                            
                            <p class="card-text"><strong>Jami:</strong> <?= $order['total_amount'] ?> so'm</p>
                            
                            <div class="d-flex justify-content-between mt-3">
                                <div>
                                    <span class="badge bg-<?= 
                                        $order['status'] == 'new' ? 'warning' : 
                                        ($order['status'] == 'processing' ? 'primary' : 
                                        ($order['status'] == 'completed' ? 'success' : 'danger')) 
                                    ?>">
                                        <?= 
                                            $order['status'] == 'new' ? 'Yangi' : 
                                            ($order['status'] == 'processing' ? 'Jarayonda' : 
                                            ($order['status'] == 'completed' ? 'Tugallangan' : 'Bekor qilingan')) 
                                        ?>
                                    </span>
                                </div>
                                <div>
                                    <a href="change_status.php?id=<?= $order['id'] ?>&status=processing" class="btn btn-sm btn-primary">Jarayonda</a>
                                    <a href="change_status.php?id=<?= $order['id'] ?>&status=completed" class="btn btn-sm btn-success">Tugallandi</a>
                                    <a href="change_status.php?id=<?= $order['id'] ?>&status=cancelled" class="btn btn-sm btn-danger">Bekor qilish</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
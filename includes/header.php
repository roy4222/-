<?php
session_start();
$base_url = '/大二資料庫管理期末作業';
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>遊戲管理系統</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRu6w1L1n_jpEO94b80gNhWHTvkpCtCHvui2Q&s">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <a href="<?php echo $base_url; ?>/">
                    <i class="fas fa-gamepad"></i>
                    遊戲管理系統
                </a>
            </div>
            <nav class="nav-menu">
                <a href="<?php echo $base_url; ?>/" class="nav-item">
                    <i class="fas fa-home"></i>
                    首頁
                </a>
                <a href="<?php echo $base_url; ?>/games.php" class="nav-item">
                    <i class="fas fa-dice"></i>
                    遊戲列表
                </a>
                <a href="<?php echo $base_url; ?>/achievements.php" class="nav-item">
                    <i class="fas fa-trophy"></i>
                    成就系統
                </a>
                <a href="<?php echo $base_url; ?>/manufacturers.php" class="nav-item">
                    <i class="fas fa-trophy"></i>
                    遊戲廠商
                </a>
                <?php if (isset($_SESSION['PlayerID'])): ?>
                    <div class="user-menu">
                        <a href="<?php echo $base_url; ?>/profile.php" class="nav-item">
                            <i class="fas fa-user-circle"></i>
                            <?php echo htmlspecialchars($_SESSION['Name']); ?>
                        </a>
                        <a href="<?php echo $base_url; ?>/logout.php" class="nav-item">
                            <i class="fas fa-sign-out-alt"></i>
                            登出
                        </a>
                    </div>
                <?php else: ?>
                    <a href="<?php echo $base_url; ?>/login.php" class="nav-item btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i>
                        登入
                    </a>
                <?php endif; ?>
            </nav>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>
    <main class="main-content">
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const header = document.querySelector('.header');
        const scrollThreshold = 50;

        function handleScroll() {
            if (window.scrollY > scrollThreshold) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        }

        window.addEventListener('scroll', handleScroll);
    });
    </script>

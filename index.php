<?php
require_once 'includes/header.php';
?>

<div class="carousel">
    <button class="carousel-button prev">&#10094;</button>
    <button class="carousel-button next">&#10095;</button>
    <div class="carousel-container">
        <div class="carousel-slide">
            <img src="https://i.imgur.com/ug4Hz9A.png" alt="遊戲展示 1">
            <div class="carousel-content">
                <h3>探索精彩遊戲世界</h3>
                <p>發現更多有趣的遊戲，開啟你的遊戲冒險</p>
            </div>
        </div>
        <div class="carousel-slide">
            <img src="https://i.imgur.com/E7QAxRC.jpg" alt="遊戲展示 2">
            <div class="carousel-content">
                <h3>追蹤遊戲進度</h3>
                <p>記錄你的遊戲歷程，分享遊戲心得</p>
            </div>
        </div>
        <div class="carousel-slide">
            <img src="https://i.imgur.com/MIJE0ts.jpg" alt="遊戲展示 3">
            <div class="carousel-content">
                <h3>加入玩家社群</h3>
                <p>與其他玩家交流，分享遊戲體驗</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.carousel-container');
    const slides = document.querySelectorAll('.carousel-slide');
    const prevButton = document.querySelector('.carousel-button.prev');
    const nextButton = document.querySelector('.carousel-button.next');
    
    let currentIndex = 0;
    
    function updateCarousel() {
        carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
    }
    
    function nextSlide() {
        currentIndex = (currentIndex + 1) % slides.length;
        updateCarousel();
    }
    
    function prevSlide() {
        currentIndex = (currentIndex - 1 + slides.length) % slides.length;
        updateCarousel();
    }
    
    prevButton.addEventListener('click', prevSlide);
    nextButton.addEventListener('click', nextSlide);
    
    // Auto-advance every 5 seconds
    setInterval(nextSlide, 5000);
});
</script>

<div class="features">
    <div class="feature-card fade-in">
        <h2>遊戲管理</h2>
        <p>輕鬆管理你的遊戲庫，追蹤遊戲進度，掌握每款遊戲的最新動態。</p>
        <div style="margin-top: 1.5rem;">
            <a href="<?php echo $base_url; ?>/games.php" class="btn btn-secondary">查看遊戲庫</a>
        </div>
    </div>
    
    <div class="feature-card fade-in" style="animation-delay: 0.2s;">
        <h2>成就系統</h2>
        <p>解鎖成就，展示你的遊戲技巧，與其他玩家一較高下。</p>
        <div style="margin-top: 1.5rem;">
            <a href="<?php echo $base_url; ?>/achievements.php" class="btn btn-secondary">查看成就</a>
        </div>
    </div>
    
    <div class="feature-card fade-in" style="animation-delay: 0.4s;">
        <h2>遊戲廠商專區</h2>
        <p>加入玩家社群，分享遊戲體驗，結交志同道合的玩家夥伴。</p>
        <div style="margin-top: 1.5rem;">
            <a href="<?php echo $base_url; ?>/community.php" class="btn btn-secondary">查看遊戲廠商</a>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>

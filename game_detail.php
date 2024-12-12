<?php
require_once 'config/database.php';
require_once 'includes/header.php';

// 檢查是否有遊戲ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: games.php');
    exit;
}

$gameId = (int)$_GET['id'];

// 獲取遊戲詳細信息
$sql = "SELECT g.*, m.Name as ManufacturerName, m.Country as ManufacturerCountry,
               m.FoundedYear, m.Description as ManufacturerDesc, m.Website as ManufacturerWebsite,
               m.LogoURL as ManufacturerLogo
        FROM Game g
        LEFT JOIN Manufacturer m ON g.GameID = m.GameID
        WHERE g.GameID = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$gameId]);
$game = $stmt->fetch();

if (!$game) {
    header('Location: games.php');
    exit;
}

// 獲取遊戲成就
$achievementSql = "SELECT * FROM Achievement WHERE GameID = ? ORDER BY Points DESC";
$stmt = $pdo->prepare($achievementSql);
$stmt->execute([$gameId]);
$achievements = $stmt->fetchAll();

// 獲取用戶進度
$progress = null;
if (isset($_SESSION['PlayerID'])) {
    $progressSql = "SELECT * FROM GameProgress 
                    WHERE GameID = ? AND PlayerID = ?";
    $stmt = $pdo->prepare($progressSql);
    $stmt->execute([$gameId, $_SESSION['PlayerID']]);
    $progress = $stmt->fetch();
}

// 檢查是否已收藏
$isFavorite = false;
if (isset($_SESSION['PlayerID'])) {
    $favSql = "SELECT 1 FROM Favorites 
               WHERE GameID = ? AND PlayerID = ?";
    $stmt = $pdo->prepare($favSql);
    $stmt->execute([$gameId, $_SESSION['PlayerID']]);
    $isFavorite = $stmt->fetchColumn() ? true : false;
}

// 處理進度更新
if (isset($_POST['update_progress']) && isset($_SESSION['PlayerID'])) {
    $newProgress = min(100, max(0, (float)$_POST['progress']));
    
    $updateSql = "INSERT INTO GameProgress (PlayerID, GameID, ProgressPercentage) 
                  VALUES (?, ?, ?) 
                  ON DUPLICATE KEY UPDATE ProgressPercentage = ?";
    
    $stmt = $pdo->prepare($updateSql);
    $stmt->execute([$_SESSION['PlayerID'], $gameId, $newProgress, $newProgress]);
    
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
?>

<div class="container">
    <div class="game-detail">
        <!-- 遊戲基本信息 -->
        <div class="game-header">
            <div class="game-cover">
                <?php if (!empty($game['CoverImage'])): ?>
                    <img src="<?= htmlspecialchars($game['CoverImage']) ?>" 
                         alt="<?= htmlspecialchars($game['GameName']) ?>">
                <?php else: ?>
                    <div class="no-image">
                        <i class="fas fa-gamepad"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="game-info">
                <h1><?= htmlspecialchars($game['GameName']) ?></h1>
                <div class="game-meta">
                    <span class="genre">
                        <i class="fas fa-tags"></i> 
                        <?= htmlspecialchars($game['Genre']) ?>
                    </span>
                    <span class="release-date">
                        <i class="far fa-calendar-alt"></i>
                        <?= htmlspecialchars($game['ReleaseDate']) ?>
                    </span>
                </div>
                <p class="description"><?= nl2br(htmlspecialchars($game['Description'])) ?></p>
                
                <?php if (isset($_SESSION['PlayerID'])): ?>
                    <div class="game-actions">
                        <button class="favorite-btn <?= $isFavorite ? 'favorited' : '' ?>"
                                data-game-id="<?= $game['GameID'] ?>"
                                data-is-favorite="<?= $isFavorite ? '1' : '0' ?>">
                            <i class="<?= $isFavorite ? 'fas' : 'far' ?> fa-heart"></i>
                            <?= $isFavorite ? '取消收藏' : '收藏' ?>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 製作廠商信息 -->
        <div class="container">
            <h2 class="section-title">製作廠商</h2>
            <div class="manufacturer-card">
                <div class="manufacturer-header">
                    <h3><?= htmlspecialchars($game['ManufacturerName']) ?></h3>
                    <div class="manufacturer-meta">
                        <span><i class="fas fa-globe"></i> <?= htmlspecialchars($game['ManufacturerCountry']) ?></span>
                        <span><i class="fas fa-calendar"></i> 成立於 <?= htmlspecialchars($game['FoundedYear']) ?></span>
                    </div>
                    <?php if ($game['ManufacturerWebsite']): ?>
                        <a href="<?= htmlspecialchars($game['ManufacturerWebsite']) ?>" 
                           target="_blank" 
                           class="manufacturer-website">
                            <i class="fas fa-external-link-alt"></i> 訪問官網
                        </a>
                    <?php endif; ?>
                </div>
                <div class="manufacturer-body">
                    <p class="manufacturer-desc">
                        <?= nl2br(htmlspecialchars($game['ManufacturerDesc'])) ?>
                    </p>
                </div>
            </div>
        </div>
        <br>
        <!-- 遊戲進度 -->
        <?php if (isset($_SESSION['PlayerID'])): ?>
        <div class="progress-section">
            <h2>遊戲進度</h2>
            <form method="POST" class="progress-form">
                <div class="progress-bar-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $progress ? $progress['ProgressPercentage'] : 0 ?>%">
                            <span class="progress-text"><?= $progress ? $progress['ProgressPercentage'] : 0 ?>%</span>
                        </div>
                    </div>
                </div>
                <div class="progress-input">
                    <input type="number" 
                           name="progress" 
                           min="0" 
                           max="100" 
                           value="<?= $progress ? $progress['ProgressPercentage'] : 0 ?>"
                           step="0.1">
                    <button type="submit" name="update_progress" class="update-progress-btn">
                        更新進度
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- 遊戲成就 -->
        <?php if (!empty($achievements)): ?>
        <div class="container">
            <h2 class="section-title">遊戲成就</h2>
            <div class="achievements-grid">
                <?php foreach ($achievements as $achievement): ?>
                    <div class="achievement-card">
                        <div class="achievement-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="achievement-info">
                            <h3><?= htmlspecialchars($achievement['AchievementName']) ?></h3>
                            <p class="achievement-desc"><?= htmlspecialchars($achievement['Description']) ?></p>
                            <span class="achievement-points">
                                <i class="fas fa-star"></i> <?= $achievement['Points'] ?> 點
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
</div>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.game-detail {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    overflow: hidden;
}

.game-header {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 30px;
    padding: 30px;
    background: linear-gradient(to bottom, #f8f9fa, white);
}

.game-cover {
    width: 100%;
    aspect-ratio: 3/4;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
}

.game-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f5f5f5;
    font-size: 3em;
    color: #ddd;
}

.game-info h1 {
    margin: 0 0 20px 0;
    font-size: 2.5em;
    color: #333;
}

.game-meta {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    color: #666;
}

.description {
    line-height: 1.6;
    color: #555;
    margin-bottom: 20px;
}

.manufacturer-section {
    padding: 30px;
    border-top: 1px solid #eee;
    background: #fff;
}

.manufacturer-section h2 {
    font-size: 1.8em;
    color: #333;
    margin-bottom: 20px;
}

.manufacturer-info {
    display: flex;
    gap: 40px;
    padding: 30px;
    background: #f8f9fa;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.manufacturer-logo-section {
    flex: 0 0 300px;
    min-width: 300px;
}

.manufacturer-logo {
    width: 100%;
    height: auto;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.manufacturer-content {
    flex: 1;
    min-width: 0; /* 防止內容溢出 */
}

.manufacturer-content h3 {
    font-size: 1.5em;
    color: #2c3e50;
    margin-bottom: 15px;
}

.manufacturer-meta {
    display: flex;
    gap: 20px;
    color: #666;
    margin: 15px 0;
    font-size: 1.1em;
}

.manufacturer-meta span {
    display: flex;
    align-items: center;
    gap: 8px;
}

.manufacturer-website {
    display: inline-block;
    padding: 10px 20px;
    background: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    margin: 15px 0;
    transition: background-color 0.3s;
}

.manufacturer-website:hover {
    background: #45a049;
}

.manufacturer-desc {
    line-height: 1.8;
    margin: 20px 0;
    color: #444;
    font-size: 1.1em;
    text-align: justify;
    word-wrap: break-word;
    overflow-wrap: break-word;
    width: 100%;
}

.progress-bar-container {
    margin: 20px 0;
}

.progress-bar {
    height: 30px;
    background: #f5f5f5;
    border-radius: 15px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #4CAF50;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    transition: width 0.3s ease;
}

.progress-input {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-top: 10px;
}

.progress-input input {
    width: 100px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.update-progress-btn {
    padding: 8px 15px;
    background: #2196F3;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.achievements-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-top: 20px;
}

.achievement-card {
    display: flex;
    gap: 12px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.achievement-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
}

.achievement-icon {
    flex: 0 0 40px;
    width: 40px;
    height: 40px;
    border-radius: 4px;
    background: #eee;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
}

.achievement-info {
    flex: 1;
    min-width: 0;
}

.achievement-info h3 {
    margin: 0 0 4px 0;
    font-size: 1em;
    color: #333;
    font-weight: 600;
}

.achievement-desc {
    font-size: 0.9em;
    color: #666;
    margin: 0 0 8px 0;
    line-height: 1.4;
}

.achievement-points {
    display: inline-block;
    font-size: 0.9em;
    color: #f39c12;
    font-weight: 600;
}

.achievement-points i {
    margin-right: 4px;
}

@media (max-width: 768px) {
    .game-header {
        grid-template-columns: 1fr;
    }
    
    .game-cover {
        max-width: 300px;
        margin: 0 auto;
    }
    
    .manufacturer-info {
        flex-direction: column;
    }
    
    .manufacturer-logo-section {
        flex: none;
        min-width: 0;
        max-width: 250px;
        margin: 0 auto 20px;
    }
    
    .manufacturer-meta {
        flex-direction: column;
        gap: 10px;
    }
}

@media (max-width: 1024px) {
    .manufacturer-info {
        gap: 30px;
        padding: 20px;
    }
    
    .manufacturer-logo-section {
        flex: 0 0 250px;
        min-width: 250px;
    }
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.section-title {
    font-size: 24px;
    color: #333;
    margin-bottom: 20px;
    text-align: left;
}

.manufacturer-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.manufacturer-header {
    margin-bottom: 20px;
}

.manufacturer-header h3 {
    font-size: 28px;
    color: #2c3e50;
    margin: 0 0 15px 0;
}

.manufacturer-meta {
    display: flex;
    gap: 20px;
    color: #666;
    margin-bottom: 15px;
}

.manufacturer-meta span {
    display: flex;
    align-items: center;
    gap: 8px;
}

.manufacturer-website {
    display: inline-block;
    padding: 10px 20px;
    background: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.manufacturer-website:hover {
    background: #45a049;
}

.manufacturer-body {
    margin-top: 20px;
}

.manufacturer-desc {
    font-size: 16px;
    line-height: 1.8;
    color: #444;
    margin: 0;
    text-align: left;
}

@media (max-width: 768px) {
    .manufacturer-meta {
        flex-direction: column;
        gap: 10px;
    }
    
    .manufacturer-card {
        padding: 20px;
    }
}
</style>

<script>
document.querySelector('.favorite-btn')?.addEventListener('click', function() {
    const gameId = this.dataset.gameId;
    const isFavorite = this.dataset.isFavorite === '1';
    const action = isFavorite ? 'unfavorite' : 'favorite';
    
    fetch('games.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=${action}&game_id=${gameId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            this.textContent = isFavorite ? '收藏' : '取消收藏';
            this.dataset.isFavorite = isFavorite ? '0' : '1';
            this.classList.toggle('favorited');
            const icon = this.querySelector('i');
            icon.classList.toggle('fas');
            icon.classList.toggle('far');
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?> 
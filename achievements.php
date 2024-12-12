<?php
require_once 'includes/header.php';
require_once 'config/database.php';

// 獲取遊戲列表用於篩選
$stmt = $pdo->query("SELECT GameID, GameName, CoverImage FROM Game ORDER BY GameName");
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 處理遊戲篩選
$selectedGame = isset($_GET['game']) ? (int)$_GET['game'] : null;

// 構建成就查詢SQL
$sql = "SELECT a.*, g.GameName, g.CoverImage 
        FROM Achievement a 
        JOIN Game g ON a.GameID = g.GameID";
if ($selectedGame) {
    $sql .= " WHERE a.GameID = ?";
}
$sql .= " ORDER BY g.GameName, a.Points DESC";

// 執行查詢
$stmt = $selectedGame ? 
    $pdo->prepare($sql) : 
    $pdo->query($sql);

if ($selectedGame) {
    $stmt->execute([$selectedGame]);
}
$achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 如果用戶已登入,獲取已完成的成就
$userAchievements = [];
if (isset($_SESSION['user'])) {
    $stmt = $pdo->prepare("SELECT AchievementID FROM UserAchievement WHERE PlayerID = ?");
    $stmt->execute([$_SESSION['user']['id']]);
    $userAchievements = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<div class="container my-4">
    <div class="page-header fade-in">
        <h1>遊戲成就</h1>
        <p class="lead">探索各個遊戲的成就,展示你的遊戲技巧</p>
    </div>

    <!-- 遊戲選擇器 -->
    <div class="game-selector fade-in">
        <div class="games-grid">
            <?php foreach ($games as $game): ?>
                <div class="game-thumbnail" 
                     onclick="window.location.href='?game=<?= $game['GameID'] ?>'"
                     <?= $selectedGame == $game['GameID'] ? 'data-active="true"' : '' ?>>
                    <div class="game-logo-wrapper">
                        <img src="<?= htmlspecialchars($game['CoverImage']) ?>" 
                             alt="<?= htmlspecialchars($game['GameName']) ?>"
                             class="game-logo">
                    </div>
                    <h3><?= htmlspecialchars($game['GameName']) ?></h3>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 成就列表 -->
    <div class="achievements-grid">
        <?php foreach ($achievements as $index => $achievement): ?>
            <div class="achievement-card fade-in" style="animation-delay: <?= $index * 0.1 ?>s">
                <div class="achievement-header">
                    <h3><?= htmlspecialchars($achievement['AchievementName']) ?></h3>
                    <span class="points"><?= $achievement['Points'] ?>分</span>
                </div>
                <div class="achievement-content">
                    <p><?= htmlspecialchars($achievement['Description']) ?></p>
                </div>
                <div class="achievement-footer">
                    <span class="game-name"><?= htmlspecialchars($achievement['GameName']) ?></span>
                    <?php if (in_array($achievement['AchievementID'], $userAchievements)): ?>
                        <span class="achievement-status completed">已完成</span>
                    <?php else: ?>
                        <span class="achievement-status locked">未解鎖</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.page-header {
    text-align: center;
    margin-bottom: 3rem;
}

.game-selector {
    margin-bottom: 3rem;
}

.games-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 2rem;
    padding: 1rem;
    margin-bottom: 3rem;
}

.game-thumbnail {
    background: white;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: transform 0.3s, box-shadow 0.3s;
    color: #000;
    position: relative;
    overflow: hidden;
    padding-bottom: 60px;
}

.game-logo-wrapper {
    width: 100%;
    padding-top: 100%;
    position: relative;
    margin-bottom: 0;
}

.game-logo {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.game-thumbnail h3 {
    font-size: 1.2rem;
    margin: 0;
    color: #000;
    padding: 1rem;
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
}

.game-thumbnail[data-active="true"] {
    box-shadow: 0 0 0 3px #4CAF50;
}

.game-thumbnail:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.achievements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    padding: 1rem;
}

.achievement-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: transform 0.3s;
    color: #000;
}

.achievement-card:hover {
    transform: translateY(-5px);
}

.achievement-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.points {
    background: #4CAF50;
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-weight: bold;
}

.achievement-content {
    margin-bottom: 1rem;
    color: #000;
}

.achievement-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.achievement-status {
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.9rem;
}

.achievement-status.completed {
    background: #4CAF50;
    color: white;
}

.achievement-status.locked {
    background: #f5f5f5;
    color: #666;
}

.fade-in {
    opacity: 0;
    animation: fadeIn 0.5s ease-in forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.game-name {
    color: #333;
}
</style>

<?php require_once 'includes/footer.php'; ?> 
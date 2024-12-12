<?php
require_once 'config/database.php';
require_once 'includes/header.php';

// 分頁設置
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12; // 每頁顯示12個遊戲
$offset = ($page - 1) * $perPage;

// 搜索和篩選邏輯
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$genre = isset($_GET['genre']) ? trim($_GET['genre']) : '';

// 構建SQL查詢 - 修改為使用Manufacturer表
$sql = "SELECT g.*, m.Name as ManufacturerName, m.Country as ManufacturerCountry 
        FROM Game g 
        LEFT JOIN Manufacturer m ON g.GameID = m.GameID
        WHERE 1=1";
$params = array();

if ($search) {
    $sql .= " AND g.GameName LIKE ?";
    $params[] = "%$search%";
}

if ($genre) {
    $sql .= " AND g.Genre = ?";
    $params[] = $genre;
}

// 獲取總記錄數
$countSql = str_replace("g.*, m.Name as ManufacturerName, m.Country as ManufacturerCountry", "COUNT(*)", $sql);
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalGames = $stmt->fetchColumn();
$totalPages = ceil($totalGames / $perPage);

// 添加分頁限制
$sql .= " ORDER BY g.ReleaseDate DESC LIMIT $offset, $perPage";

// 執行查詢
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 獲取所有遊戲類型用於篩選
$genreSql = "SELECT DISTINCT Genre FROM Game ORDER BY Genre";
$genres = $pdo->query($genreSql)->fetchAll(PDO::FETCH_COLUMN);

// 收藏功能
if (isset($_POST['action']) && isset($_SESSION['PlayerID'])) {
    $gameId = (int)$_POST['game_id'];
    $playerId = $_SESSION['PlayerID'];
    
    if ($_POST['action'] === 'favorite') {
        $favSql = "INSERT IGNORE INTO Favorites (PlayerID, GameID, AddTime) 
                   VALUES (?, ?, NOW())";
        $stmt = $pdo->prepare($favSql);
        $stmt->execute([$playerId, $gameId]);
    } elseif ($_POST['action'] === 'unfavorite') {
        $unfavSql = "DELETE FROM Favorites 
                     WHERE PlayerID = ? AND GameID = ?";
        $stmt = $pdo->prepare($unfavSql);
        $stmt->execute([$playerId, $gameId]);
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

// 獲取當前用戶的收藏遊戲
$favorites = [];
if (isset($_SESSION['PlayerID'])) {
    $favSql = "SELECT GameID FROM Favorites WHERE PlayerID = ?";
    $stmt = $pdo->prepare($favSql);
    $stmt->execute([$_SESSION['PlayerID']]);
    $favorites = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<div class="container">
    <!-- 頁面標題 -->
    <div class="page-header">
        <h1>遊戲列表</h1>
    </div>

    <!-- 搜索和篩選表單 -->
    <div class="search-container">
        <form method="GET" action="games.php" class="search-form">
            <div class="search-input-group">
                <input type="text" 
                       name="search" 
                       value="<?= htmlspecialchars($search) ?>" 
                       placeholder="搜索遊戲..."
                       class="search-input">
                <select name="genre" class="genre-select">
                    <option value="">所有類型</option>
                    <?php foreach ($genres as $g): ?>
                        <option value="<?= htmlspecialchars($g) ?>" 
                                <?= $g === $genre ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> 搜索
                </button>
            </div>
        </form>
    </div>

    <!-- 遊戲列表 -->
    <div class="games-grid">
        <?php foreach ($games as $game): ?>
            <div class="game-card">
                <div class="game-image">
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
                    <h3 class="game-title"><?= htmlspecialchars($game['GameName']) ?></h3>
                    <div class="game-meta">
                        <span class="game-genre">
                            <i class="fas fa-tags"></i> 
                            <?= htmlspecialchars($game['Genre']) ?>
                        </span>
                        <span class="game-release">
                            <i class="far fa-calendar-alt"></i>
                            <?= htmlspecialchars($game['ReleaseDate']) ?>
                        </span>
                    </div>
                    <?php if ($game['ManufacturerName']): ?>
                        <div class="game-manufacturer">
                            <i class="fas fa-building"></i>
                            製作廠商: <?= htmlspecialchars($game['ManufacturerName']) ?>
                            (<?= htmlspecialchars($game['ManufacturerCountry']) ?>)
                        </div>
                    <?php endif; ?>
                    
                    <div class="game-actions">
                        <?php if (isset($_SESSION['PlayerID'])): ?>
                            <button class="favorite-btn <?= in_array($game['GameID'], $favorites) ? 'favorited' : '' ?>"
                                    data-game-id="<?= $game['GameID'] ?>"
                                    data-is-favorite="<?= in_array($game['GameID'], $favorites) ? '1' : '0' ?>">
                                <i class="<?= in_array($game['GameID'], $favorites) ? 'fas' : 'far' ?> fa-heart"></i>
                                <?= in_array($game['GameID'], $favorites) ? '取消收藏' : '收藏' ?>
                            </button>
                        <?php endif; ?>
                        
                        <a href="game_detail.php?id=<?= $game['GameID'] ?>" class="detail-btn">
                            <i class="fas fa-info-circle"></i> 查看詳情
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 分頁導航 -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&genre=<?= urlencode($genre) ?>" 
                   class="page-link">
                    <i class="fas fa-chevron-left"></i> 上一頁
                </a>
            <?php endif; ?>
            
            <div class="page-numbers">
                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="current-page"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&genre=<?= urlencode($genre) ?>" 
                           class="page-link"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&genre=<?= urlencode($genre) ?>" 
                   class="page-link">
                    下一頁 <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    margin-bottom: 30px;
    text-align: center;
}

.page-header h1 {
    font-size: 2.5em;
    color: white;
}

.search-container {
    margin-bottom: 30px;
}

.search-form {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.search-input-group {
    display: flex;
    gap: 10px;
    max-width: 800px;
    width: 100%;
}

.search-input,
.genre-select {
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1em;
}

.search-input {
    flex: 1;
}

.search-btn {
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.search-btn:hover {
    background-color: #45a049;
}

.games-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.game-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s;
}

.game-card:hover {
    transform: translateY(-5px);
}

.game-image {
    height: 200px;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
}

.game-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    font-size: 3em;
    color: #ddd;
}

.game-info {
    padding: 15px;
}

.game-title {
    margin: 0 0 10px 0;
    font-size: 1.2em;
    color: #333;
}

.game-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 10px;
    font-size: 0.9em;
    color: #666;
}

.game-manufacturer {
    font-size: 0.9em;
    color: #666;
    margin-bottom: 15px;
}

.game-actions {
    display: flex;
    gap: 10px;
}

.favorite-btn,
.detail-btn {
    flex: 1;
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    font-size: 0.9em;
    transition: background-color 0.3s;
}

.favorite-btn {
    background-color: #ff4081;
    color: white;
}

.favorite-btn.favorited {
    background-color: #e91e63;
}

.detail-btn {
    background-color: #2196F3;
    color: white;
}

.favorite-btn:hover {
    background-color: #e91e63;
}

.detail-btn:hover {
    background-color: #1976D2;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-top: 30px;
}

.page-numbers {
    display: flex;
    gap: 5px;
}

.page-link,
.current-page {
    padding: 8px 12px;
    border-radius: 5px;
    text-decoration: none;
}

.page-link {
    background-color: #f5f5f5;
    color: #333;
    transition: background-color 0.3s;
}

.current-page {
    background-color: #2196F3;
    color: white;
}

.page-link:hover {
    background-color: #e0e0e0;
}

@media (max-width: 768px) {
    .search-input-group {
        flex-direction: column;
    }
    
    .games-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
}
</style>

<script>
document.querySelectorAll('.favorite-btn').forEach(btn => {
    btn.addEventListener('click', function() {
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
});
</script>

<?php require_once 'includes/footer.php'; ?> 
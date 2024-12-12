<?php
require_once 'includes/header.php';
require_once 'config/database.php';

// 獲取所有廠商資料
$sql = "SELECT m.*, g.GameName, g.CoverImage, g.ReleaseDate, g.Description as GameDescription 
        FROM Manufacturer m 
        LEFT JOIN Game g ON m.GameID = g.GameID 
        ORDER BY m.Name";
$stmt = $pdo->query($sql);
$manufacturers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 將資料按廠商分組
$groupedManufacturers = [];
foreach ($manufacturers as $row) {
    $id = $row['ManufacturerID'];
    if (!isset($groupedManufacturers[$id])) {
        $groupedManufacturers[$id] = [
            'info' => [
                'Name' => $row['Name'],
                'Country' => $row['Country'],
                'FoundedYear' => $row['FoundedYear'],
                'Description' => $row['Description'],
                'LogoURL' => $row['LogoURL'],
                'Website' => $row['Website']
            ],
            'games' => []
        ];
    }
    if ($row['GameName']) {
        $groupedManufacturers[$id]['games'][] = [
            'GameName' => $row['GameName'],
            'CoverImage' => $row['CoverImage'],
            'ReleaseDate' => $row['ReleaseDate'],
            'Description' => $row['GameDescription']
        ];
    }
}
?>

<div class="container my-4">
    <div class="page-header fade-in">
        <h1>遊戲廠商</h1>
        <p class="lead">探索各大遊戲廠商及其代表作品</p>
    </div>

    <!-- 廠商縮略圖網格 -->
    <div class="manufacturer-grid">
        <?php foreach ($groupedManufacturers as $id => $manufacturer): ?>
            <div class="manufacturer-thumbnail fade-in" 
                 data-manufacturer-id="<?= $id ?>"
                 onclick="showManufacturerDetail(<?= $id ?>)">
                <img src="<?= htmlspecialchars($manufacturer['info']['LogoURL']) ?>" 
                     alt="<?= htmlspecialchars($manufacturer['info']['Name']) ?>" 
                     class="manufacturer-logo">
                <h3><?= htmlspecialchars($manufacturer['info']['Name']) ?></h3>
                <p class="manufacturer-brief">
                    <?= htmlspecialchars($manufacturer['info']['Country']) ?> | 
                    <?= $manufacturer['info']['FoundedYear'] ?>年
                </p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 廠商詳細資訊模態框 -->
    <div id="manufacturerModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <div id="manufacturerDetail"></div>
        </div>
    </div>
</div>

<style>
.manufacturer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 2rem;
    padding: 1rem;
}

.manufacturer-thumbnail {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: transform 0.3s, box-shadow 0.3s;
    color: #000;
}

.manufacturer-thumbnail:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.manufacturer-logo {
    width: 120px;
    height: 120px;
    object-fit: contain;
    margin-bottom: 1rem;
}

.manufacturer-brief {
    color: #333;
    margin-top: 0.5rem;
    font-size: 0.9rem;
}

/* 模態框樣式 */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.7);
    z-index: 1000;
    overflow-y: auto;
}

.modal-content {
    position: relative;
    background-color: white;
    margin: 50px auto;
    padding: 2rem;
    width: 90%;
    max-width: 1000px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.close-button {
    position: absolute;
    top: 1rem;
    right: 1.5rem;
    font-size: 2rem;
    cursor: pointer;
    color: #666;
}

.close-button:hover {
    color: #000;
}

/* 詳細資訊樣式 */
.manufacturer-detail {
    padding: 1rem;
    color: #000;
}

.manufacturer-header {
    display: flex;
    align-items: center;
    margin-bottom: 2rem;
}

.detail-logo {
    width: 150px;
    height: 150px;
    object-fit: contain;
    margin-right: 2rem;
}

.games-section {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #eee;
}

.games-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 1.5rem;
}

.game-card {
    background: #f8f9fa;
    border-radius: 10px;
    overflow: hidden;
    transition: transform 0.3s;
}

.game-card:hover {
    transform: translateY(-5px);
}

.game-cover {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.game-info {
    padding: 1.5rem;
    color: #000;
}

.manufacturer-meta {
    color: #333;
}

.release-date {
    color: #333;
}

@media (max-width: 768px) {
    .manufacturer-header {
        flex-direction: column;
        text-align: center;
    }

    .detail-logo {
        margin: 0 0 1rem 0;
    }

    .games-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
const manufacturers = <?= json_encode($groupedManufacturers) ?>;

function showManufacturerDetail(id) {
    const manufacturer = manufacturers[id];
    const modal = document.getElementById('manufacturerModal');
    const detailContent = document.getElementById('manufacturerDetail');
    
    let html = `
        <div class="manufacturer-detail">
            <div class="manufacturer-header">
                <img src="${manufacturer.info.LogoURL}" 
                     alt="${manufacturer.info.Name}" 
                     class="detail-logo">
                <div>
                    <h2>${manufacturer.info.Name}</h2>
                    <p class="manufacturer-meta">
                        ${manufacturer.info.Country} | 
                        成立於 ${manufacturer.info.FoundedYear}年
                    </p>
                </div>
            </div>

            <div class="manufacturer-content">
                <p class="manufacturer-description">
                    ${manufacturer.info.Description}
                </p>

                ${manufacturer.info.Website ? 
                    `<a href="${manufacturer.info.Website}" 
                        class="btn btn-outline-primary" 
                        target="_blank">
                        訪問官網
                    </a>` : 
                    ''}
            </div>`;

    if (manufacturer.games.length > 0) {
        html += `
            <div class="games-section">
                <h3>代表作品</h3>
                <div class="games-grid">
                    ${manufacturer.games.map(game => `
                        <div class="game-card">
                            <img src="${game.CoverImage}" 
                                 alt="${game.GameName}"
                                 class="game-cover">
                            <div class="game-info">
                                <h4>${game.GameName}</h4>
                                <p class="release-date">
                                    發售日期: ${new Date(game.ReleaseDate).toLocaleDateString()}
                                </p>
                                <p class="game-description">
                                    ${game.Description}
                                </p>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>`;
    }

    html += '</div>';
    
    detailContent.innerHTML = html;
    modal.style.display = 'block';
    
    // 禁止背景滾動
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('manufacturerModal');
    modal.style.display = 'none';
    
    // 恢復背景滾動
    document.body.style.overflow = 'auto';
}

// 點擊模態框外部關閉
window.onclick = function(event) {
    const modal = document.getElementById('manufacturerModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php require_once 'includes/footer.php'; ?> 
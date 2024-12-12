    </main>
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>關於我們</h3>
                <p>遊戲管理平台是一個專注於提供優質遊戲體驗的平台。</p>
            </div>
            <div class="footer-section">
                <h3>快速連結</h3>
                <ul>
                    <li><a href="<?php echo $base_url; ?>/">首頁</a></li>
                    <li><a href="<?php echo $base_url; ?>/games.php">遊戲列表</a></li>
                    <li><a href="<?php echo $base_url; ?>/achievements.php">成就系統</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>聯絡我們</h3>
                <p>Email: contact@gameplatform.com</p>
                <p>電話: (02) 1234-5678</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2023 遊戲管理平台. All rights reserved.</p>
        </div>
    </footer>

    <style>
    .footer {
        background-color: #333;
        color: #fff;
        padding: 40px 0 20px;
        margin-top: 50px;
    }

    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        padding: 0 20px;
    }

    .footer-section h3 {
        color: #fff;
        margin-bottom: 20px;
        font-size: 1.2em;
    }

    .footer-section ul {
        list-style: none;
        padding: 0;
    }

    .footer-section ul li {
        margin-bottom: 10px;
    }

    .footer-section a {
        color: #fff;
        text-decoration: none;
        transition: color 0.3s;
    }

    .footer-section a:hover {
        color: #4CAF50;
    }

    .footer-bottom {
        text-align: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #555;
    }

    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: 1fr;
            text-align: center;
        }
    }
    </style>
</body>
</html>

<?php
require_once 'includes/header.php';
require_once 'config/database.php';

if (isset($_SESSION['PlayerID'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "請填寫所有欄位";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM Player WHERE Email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['Password'])) {
            $_SESSION['PlayerID'] = $user['PlayerID'];
            $_SESSION['Name'] = $user['Name'];
            
            $updateStmt = $pdo->prepare("UPDATE Player SET LastLoginDate = CURRENT_TIMESTAMP WHERE PlayerID = ?");
            $updateStmt->execute([$user['PlayerID']]);
            
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "電子郵件或密碼錯誤";
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="auth-card">
                <div class="card-header">
                    <h3>歡迎回來</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="login.php" autocomplete="on">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="請輸入電子郵件" required autocomplete="email">
                        </div>
                        
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="請輸入密碼" required autocomplete="current-password">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> 登入
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <p>還沒有帳號？ <a href="register.php">立即註冊</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
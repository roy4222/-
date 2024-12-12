<?php
require_once 'includes/header.php';
require_once 'config/database.php';

if (isset($_SESSION['PlayerID'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['password_confirm'] ?? '';
    
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "請填寫所有欄位";
    } elseif ($password !== $confirm_password) {
        $error = "密碼不匹配";
    } elseif (strlen($password) < 6) {
        $error = "密碼長度至少需要6個字符";
    } else {
        try {
            // 檢查資料庫連接
            if (!$pdo) {
                throw new Exception("資料庫連接失敗");
            }

            // 檢查郵箱是否已存在
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Player WHERE Email = ?");
            if (!$stmt) {
                throw new Exception("SQL 準備失敗: " . print_r($pdo->errorInfo(), true));
            }
            
            $stmt->execute([$email]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                $error = "此電子郵件已被註冊";
            } else {
                // 密碼加密
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // 插入新用戶
                $sql = "INSERT INTO Player (Name, Email, Password, RegisterDate) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
                $stmt = $pdo->prepare($sql);
                if (!$stmt) {
                    throw new Exception("SQL 準備失敗: " . print_r($pdo->errorInfo(), true));
                }
                
                $result = $stmt->execute([$name, $email, $hashed_password]);
                if (!$result) {
                    throw new Exception("SQL 執行失敗: " . print_r($stmt->errorInfo(), true));
                }
                
                $success = "註冊成功！請登入";
                header("refresh:2;url=login.php");
            }
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            $error = "註冊失敗：" . $e->getMessage();
        } catch (Exception $e) {
            error_log("General Error: " . $e->getMessage());
            $error = "註冊失敗：" . $e->getMessage();
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="auth-card">
                <div class="card-header">
                    <h3>創建新帳號</h3>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="register.php" autocomplete="on">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" class="form-control" id="name" name="name" 
                                   placeholder="請輸入名稱" required autocomplete="name"
                                   value="<?php echo htmlspecialchars($name ?? ''); ?>">
                        </div>

                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="請輸入電子郵件" required autocomplete="email"
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>">
                        </div>
                        
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="請輸入密碼" required autocomplete="new-password">
                        </div>
                        
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                                   placeholder="請再次輸入密碼" required autocomplete="new-password">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> 註冊
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <p>已經有帳號？ <a href="login.php">立即登入</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
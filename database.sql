-- 創建資料庫
CREATE DATABASE IF NOT EXISTS game_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE game_platform;

-- 創建玩家表
CREATE TABLE Player (
    PlayerID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    RegisterDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    LastLoginDate DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 創建遊戲表
CREATE TABLE Game (
    GameID INT AUTO_INCREMENT PRIMARY KEY,
    GameName VARCHAR(100) NOT NULL,
    Genre VARCHAR(50) NOT NULL,
    ReleaseDate DATE NOT NULL,
    Description TEXT,
    CoverImage VARCHAR(255),
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 創建製造商表
CREATE TABLE Manufacturer (
    ManufacturerID INT AUTO_INCREMENT PRIMARY KEY,
    GameID INT UNIQUE,
    Name VARCHAR(100) NOT NULL,
    Country VARCHAR(50),
    FoundedYear INT,
    Description TEXT,
    LogoURL VARCHAR(255),
    Website VARCHAR(255),
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (GameID) REFERENCES Game(GameID) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 創建成就表
CREATE TABLE Achievement (
    AchievementID INT AUTO_INCREMENT PRIMARY KEY,
    GameID INT,
    AchievementName VARCHAR(100) NOT NULL,
    Description TEXT,
    Points INT DEFAULT 0,
    IconURL VARCHAR(255),
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (GameID) REFERENCES Game(GameID) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 創建遊戲進度表
CREATE TABLE GameProgress (
    PlayerID INT,
    GameID INT,
    ProgressPercentage DECIMAL(5,2) DEFAULT 0.00,
    LastPlayedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (PlayerID, GameID),
    FOREIGN KEY (PlayerID) REFERENCES Player(PlayerID) ON DELETE CASCADE,
    FOREIGN KEY (GameID) REFERENCES Game(GameID) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 創建收藏表
CREATE TABLE Favorites (
    PlayerID INT,
    GameID INT,
    AddedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (PlayerID, GameID),
    FOREIGN KEY (PlayerID) REFERENCES Player(PlayerID) ON DELETE CASCADE,
    FOREIGN KEY (GameID) REFERENCES Game(GameID) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 添加索引
ALTER TABLE Manufacturer ADD INDEX idx_name (Name);
ALTER TABLE Manufacturer ADD INDEX idx_country (Country);
ALTER TABLE Achievement ADD INDEX idx_game_id (GameID);

-- 插入測試數據
-- 1. 插入玩家數據
INSERT INTO Player (Name, Email, Password) VALUES
('測試玩家1', 'test1@example.com', 'password123'),
('測試玩家2', 'test2@example.com', 'password456'),
('測試玩家3', 'test3@example.com', 'password789'),
('測試玩家4', 'test4@example.com', 'password101'),
('測試玩家5', 'test5@example.com', 'password102');

-- 2. 插入遊戲數據
INSERT INTO Game (GameName, Genre, ReleaseDate, Description) VALUES
('薩爾達傳說王國之淚', '動作冒險', '2023-05-12', 
 '《薩爾達傳說王國之淚》是任天堂出品的開放世界動作冒險遊戲。作為《曠野之息》的續作，遊戲加入了空島探索等垂直元素，擴展了海拉魯王國的冒險範圍。'),

('幻獸帕魯', '生存冒險', '2024-01-19', 
 '《幻獸帕魯》是一款開放世界生存建造遊戲，融合了寶可夢式的生物收集元素。玩家可以在廣闊的世界中探索、建造、戰鬥，並捕捉各種奇特的帕魯。'),

('艾爾登法環', '動作角色扮演', '2022-02-25', 
 '《艾爾登法環》是FromSoftware開發的開放世界動作角色扮演遊戲。遊戲以其廣闊的世界觀、具有挑戰性的戰鬥系統和深邃的劇情設定而聞名。');

-- 3. 插入製造商數據
INSERT INTO Manufacturer (Name, Country, FoundedYear, Description, Website, GameID) VALUES
('任天堂', '日本', 1889, '日本最大的電子遊戲公司之一，以創新的遊戲設計和優質的遊戲體驗聞名。', 'https://www.nintendo.com', 1),
('Pocketpair', '日本', 2015, '日本新興遊戲開發商，開發了幻獸帕魯等遊戲。', 'https://www.pocketpair.jp', 2),
('FromSoftware', '日本', 1986, '日本知名遊戲開發商，以魂系列遊戲聞名。', 'https://www.fromsoftware.jp', 3);

-- 4. 插入成就數據
-- 幻獸帕魯的成就
INSERT INTO Achievement (GameID, AchievementName, Description, Points) VALUES 
-- 捕捉帕魯類
(2, '傳說的開始', '第一次抓到了帕魯', 10),
(2, '初級帕魯訓練家', '抓到了10種帕魯', 20),
(2, '中級帕魯訓練家', '抓到了20種帕魯', 30),
(2, '高級帕魯訓練家', '抓到了50種帕魯', 40),
(2, '專家級帕魯訓練家', '抓到了90種帕魯', 50),
(2, '超級帕魯訓練家', '抓到了140種帕魯', 60),

-- 擊敗塔主類
(2, '丘陵統治者', '打敗了佐伊 & 暴電熊', 30),
(2, '小雪山統治者', '打敗了莉莉 & 百合女王', 35),
(2, '火山統治者', '打敗了阿克塞爾 & 波魯傑克斯', 40),
(2, '沙漠統治者', '打敗了馬庫斯 & 荷魯斯', 45),
(2, '靈峰統治者', '打敗了維克托 & 異構格里芬', 50),
(2, '櫻花島統治者', '打敗了紗夜 & 輝月伊', 55),

-- 特殊Boss
(2, '狂亂淑女', '打敗了召喚出的貝菈諾娃', 60),
(2, '幽玄魔女', '打敗了召喚出的貝菈露潔', 70),
(2, '永炎現世', '打敗了召喚出的殁殃', 80),

-- 收集/製作類
(2, '濫捕', '捕獲總數達到1000只', 40),
(2, '上級冒險家', '通關20次地下城', 45),
(2, '漂流者的足跡', '獲得40個手記', 35),
(2, '島嶼萬事通', '獲得255個翠葉鼠雕像', 50),
(2, '帕魯球工匠', '製造了2000個帕魯球', 40),
(2, '鐵之心', '製造了10000個鑄塊', 45),
(2, '鐵幕', '製造了20000發彈藥', 45),
(2, 'All for One', '將一只帕魯升到最高階級', 50),
(2, '怨念的呻吟', '將5只帕魯升到最高階級', 70),

-- 神獸類
(2, '傳說中的冰天馬', '抓到了喚冬獸', 65),
(2, '傳說中的流星龍', '抓到了空渦龍', 65),
(2, '傳奇的聖騎士', '抓到了聖光騎士', 65),
(2, '傳奇的黑騎士', '抓到了混沌騎士', 65),

-- 其他特殊成就
(2, '非人道行徑', '抓到了人類', 40),
(2, '海上霸主', '占領油田要塞', 60),
(2, '帕洛斯群島的霸主', '擊敗6位高塔頭目', 100);

-- 艾爾登法環的完整成就 (GameID = 3)
INSERT INTO Achievement (GameID, AchievementName, Description, Points) VALUES 
(3, '圓桌廳堂', '到達圓桌廳堂', 20),
(3, '「惡兆妖鬼」瑪爾基特', '擊敗「惡兆妖鬼」瑪爾基特', 25),
(3, '「碎片君王」葛瑞克', '擊敗「碎片君王」葛瑞克', 30),
(3, '大盧恩', '獲得大盧恩', 30),
(3, '拉達岡的紅狼', '擊敗拉達岡的紅狼', 25),
(3, '「滿月女王」蕾娜菈', '擊敗「滿月女王」蕾娜菈', 30),
(3, '獅子混種', '擊敗獅子混種', 25),
(3, '禁衛騎士羅蕾塔', '擊敗禁衛騎士羅蕾塔', 30),
(3, '「碎片君王」拉塔恩', '擊敗「碎片君王」拉塔恩', 35),
(3, '仿身淚滴', '獲得仿身淚滴', 25),
(3, '「初始之王」葛孚雷', '擊敗「初始之王」葛孚雷', 35),
(3, '「碎片君王」蒙葛特', '擊敗「碎片君王」蒙葛特', 35),
(3, '「熔岩土龍」馬卡爾', '擊敗「熔岩土龍」馬卡爾', 30),
(3, '弒神武器', '將任一武器強化至上限', 40),
(3, '火焰巨人', '擊敗火焰巨人', 35),
(3, '老將尼奧', '擊敗老將尼奧', 35),
(3, '黃金樹祝融', '獲得黃金樹祝融', 35),
(3, '「黑暗棄子」艾絲緹', '擊敗「黑暗棄子」艾絲緹', 35),
(3, '「碎片君王」拉卡德', '擊敗「碎片君王」拉卡德', 35),
(3, '神皮雙人組', '擊敗神皮雙人組', 35),
(3, '神皮貴族', '擊敗神皮貴族', 30),
(3, '祖靈', '擊敗祖靈', 30),
(3, '「黑劍」瑪利喀斯', '擊敗「黑劍」瑪利喀斯', 40),
(3, '「碎片君王」蒙格', '擊敗「碎片君王」蒙格', 40),
(3, '戰士荷萊・露', '擊敗戰士荷萊・露', 30),
(3, '英雄石像鬼', '擊敗英雄石像鬼', 30),
(3, '「聖樹騎士」羅蕾塔', '擊敗「聖樹騎士」羅蕾塔', 35),
(3, '「鐵棘」艾隆梅爾', '擊敗「鐵棘」艾隆梅爾', 35),
(3, '諾克史黛拉的龍人士兵', '擊敗諾克史黛拉的龍人士兵', 30),
(3, '祖靈之王', '擊敗祖靈之王', 35),
(3, '「碎片君王」瑪蓮妮亞', '擊敗「碎片君王」瑪蓮妮亞', 50),
(3, '「惡兆之子」蒙格', '擊敗「惡兆之子」蒙格', 40),
(3, '「龍王」普拉頓桑克斯', '擊敗「龍王」普拉頓桑克斯', 45),
(3, '「死龍」弗爾桑克斯', '擊敗「死龍」弗爾桑克斯', 45),
(3, '星星時代', '完成星星時代', 50),
(3, '艾爾登之王', '成為艾爾登之王', 60),
(3, '傳說中的武器', '獲得所有傳說中的武器', 70),
(3, '傳說中的骨灰', '獲得所有傳說中的骨灰', 70),
(3, '傳說中的護符', '獲得所有傳說中的護符', 70),
(3, '傳說中的魔法、禱告', '獲得所有傳說中的魔法、禱告', 70),
(3, '癲火之王', '成為癲火之王', 80),
(3, '艾爾登法環', '獲得所有成就', 100);

-- 插入更多遊戲進度數據
INSERT INTO GameProgress (PlayerID, GameID, ProgressPercentage) VALUES
(1, 1, 75.5),  -- 玩家1的薩爾達進度
(1, 2, 45.0),  -- 玩家1的帕魯進度
(1, 3, 90.0),  -- 玩家1的艾爾登法環進度
(2, 1, 95.0),  -- 玩家2的薩爾達進度
(2, 2, 30.0),  -- 玩家2的帕魯進度
(3, 1, 60.0),  -- 玩家3的薩爾達進度
(4, 2, 85.0),  -- 玩家4的帕魯進度
(5, 3, 100.0); -- 玩家5的艾爾登法環進度

-- 插入更多收藏數據
INSERT INTO Favorites (PlayerID, GameID) VALUES
(1, 1),  -- 玩家1收藏薩爾達
(1, 3),  -- 玩家1收藏艾爾登法環
(2, 1),  -- 玩家2收藏薩爾達
(2, 2),  -- 玩家2收藏帕魯
(3, 1),  -- 玩家3收藏薩爾達
(4, 2),  -- 玩家4收藏帕魯
(5, 3);  -- 玩家5收藏艾爾登法環

-- 為薩爾達添加成就數據
INSERT INTO Achievement (GameID, AchievementName, Description, Points) VALUES
(1, '初次啟程', '開始你的冒險', 10),
(1, '空島探索者', '首次到達空島', 20),
(1, '地下城冒險家', '完成第一個地下城', 30),
(1, '神廟挑戰者', '完成所有神廟', 50),
(1, '林克的覺醒', '獲得所有能力', 40),
(1, '海拉魯英雄', '擊敗最終Boss', 100);

-- 補充遊戲封面圖片
UPDATE Game SET 
CoverImage = CASE 
    WHEN GameID = 1 THEN 'https://i.imgur.com/ug4Hz9A.png'  -- 薩爾達傳說王國之淚
    WHEN GameID = 2 THEN 'https://i.imgur.com/MIJE0ts.jpg'  -- 幻獸帕魯
    WHEN GameID = 3 THEN 'https://i.imgur.com/E7QAxRC.jpg'  -- 艾爾登法環
END
WHERE GameID IN (1, 2, 3);

-- 補充製造商Logo
UPDATE Manufacturer 
SET LogoURL = CASE 
    WHEN ManufacturerID = 1 THEN 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0d/Nintendo.svg/1200px-Nintendo.svg.png'
    WHEN ManufacturerID = 2 THEN 'https://yt3.googleusercontent.com/cpwl0N5ciropbA_BemYq_42kUbPHS0e1IrQGYvXx5AUX05JjHPFciFCVItnWrvQjJaGDxvlfy0g=s900-c-k-c0x00ffffff-no-rj'
    WHEN ManufacturerID = 3 THEN 'https://www.justpushstart.com/wp-content/uploads/2017/12/From_Logo.jpg'
END
WHERE ManufacturerID IN (1, 2, 3);

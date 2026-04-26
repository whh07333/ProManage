<?php
/**
 * 禅道命令行安装脚本
 */

// 加载框架
require_once 'framework/router.class.php';
require_once 'framework/control.class.php';
require_once 'framework/model.class.php';
require_once 'framework/helper.class.php';

// 实例化应用
$app = router::createApp('pms', dirname(__FILE__), 'router');

// 加载配置
$app->loadConfig();

// 加载安装模型
$app->loadModel('install');
$app->loadModel('installZen');

// 检查系统环境
echo "检查系统环境...\n";
$phpVersion = $app->installZen->getPHPVersion();
echo "PHP版本: {$phpVersion}\n";

$phpResult = $app->installZen->checkPHPVersion();
echo "PHP版本检查: {$phpResult}\n";

$pdoResult = $app->installZen->checkPDO();
echo "PDO检查: {$pdoResult}\n";

$pdoMySQLResult = $app->installZen->checkPDOMySQL();
echo "PDO MySQL检查: {$pdoMySQLResult}\n";

// 连接数据库
echo "\n连接数据库...\n";
$dbh = $app->install->connectDB();
if(is_string($dbh)) {
    echo "数据库连接失败: {$dbh}\n";
    exit(1);
}
echo "数据库连接成功\n";

// 创建数据库表
echo "\n创建数据库表...\n";
$app->install->createTable(true);
echo "数据库表创建成功\n";

// 执行安装前SQL
echo "\n执行安装前SQL...\n";
$app->install->execPreInstallSQL();
echo "安装前SQL执行成功\n";

// 创建公司和管理员账户
echo "\n创建公司和管理员账户...\n";
$data = new stdclass();
$data->company = '禅道科技';
$data->account = 'admin';
$data->password = '123456';

$result = $app->install->grantPriv($data);
if($result) {
    echo "管理员账户创建成功\n";
} else {
    echo "管理员账户创建失败: " . implode(', ', dao::getError()) . "\n";
    exit(1);
}

// 更新语言
echo "\n更新语言设置...\n";
$app->install->updateLang();
echo "语言设置更新成功\n";

// 导入演示数据
echo "\n导入演示数据...\n";
$app->install->importDemoData();
echo "演示数据导入成功\n";

// 执行安装后SQL
echo "\n执行安装后SQL...\n";
$app->install->execPostInstallSQL();
echo "安装后SQL执行成功\n";

// 开启缓存
echo "\n开启缓存...\n";
$app->install->enableCache();
echo "缓存开启成功\n";

// 更新数据库序列
echo "\n更新数据库序列...\n";
$app->install->updateDbSeq();
echo "数据库序列更新成功\n";

// 标记安装完成
echo "\n标记安装完成...\n";
$app->config->installed = true;

// 保存配置
$configFile = dirname(__FILE__) . '/config/my.php';
$configContent = "<?php\n";
$configContent .= "\$config->installed = true;\n";
$configContent .= "\$config->db->host = '{$app->config->db->host}';\n";
$configContent .= "\$config->db->port = '{$app->config->db->port}';\n";
$configContent .= "\$config->db->name = '{$app->config->db->name}';\n";
$configContent .= "\$config->db->user = '{$app->config->db->user}';\n";
$configContent .= "\$config->db->password = '{$app->config->db->password}';\n";
$configContent .= "\$config->db->prefix = '{$app->config->db->prefix}';\n";
$configContent .= "\$config->db->encoding = '{$app->config->db->encoding}';\n";
file_put_contents($configFile, $configContent);
echo "配置保存成功\n";

echo "\n安装完成！\n";
echo "管理员账户: admin\n";
echo "管理员密码: 123456\n";
echo "请登录系统后修改密码\n";

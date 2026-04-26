<?php
echo "PHP版本: " . phpversion() . "\n";
echo "SQLite支持: " . (extension_loaded('sqlite3') ? '已启用' : '未启用') . "\n";
echo "PDO支持: " . (extension_loaded('pdo') ? '已启用' : '未启用') . "\n";
echo "PDO SQLite支持: " . (extension_loaded('pdo_sqlite') ? '已启用' : '未启用') . "\n";

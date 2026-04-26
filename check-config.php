<?php
/**
 * 检查禅道配置
 */

// 加载配置文件
require_once 'config/config.php';

// 检查配置是否加载成功
echo "配置文件加载成功！\n";
echo "系统版本: {$config->version}\n";
echo "数据库配置: {$config->db->host}:{$config->db->port}\n";
echo "数据库名称: {$config->db->name}\n";
echo "系统状态: " . ($config->installed ? '已安装' : '未安装') . "\n";

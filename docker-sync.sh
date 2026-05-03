#!/bin/bash
# ProManage - Targeted sync of custom modules only
# NEVER sync framework/, www/, or full config/ — easysoft image has custom patches.
# Only sync specific custom files to avoid breaking the base installation.

SRC="/app"
DST="/apps/zentao"

echo "[ProManage] Starting targeted sync of custom modules..."

# Custom modules (create destination dirs first — they don't exist in base image)
mkdir -p "$DST/module/chat"  "$DST/module/devws"
cp -ru "$SRC/module/chat/"*  "$DST/module/chat/"  2>/dev/null && echo "[ProManage] Synced module/chat"
cp -ru "$SRC/module/devws/"* "$DST/module/devws/" 2>/dev/null && echo "[ProManage] Synced module/devws"

# Custom overrides in existing modules
cp -u  "$SRC/module/my/control.php"    "$DST/module/my/control.php"    2>/dev/null && echo "[ProManage] Synced module/my/control.php"
cp -u  "$SRC/module/user/zen.php"      "$DST/module/user/zen.php"      2>/dev/null && echo "[ProManage] Synced module/user/zen.php"

# Custom ZIN widget override
cp -u  "$SRC/lib/zin/wg/header/v1.php" "$DST/lib/zin/wg/header/v1.php" 2>/dev/null && echo "[ProManage] Synced lib/zin/wg/header/v1.php"

# Config — only sync zentaopms.php (TABLE_ constants), NOT my.php
cp -u  "$SRC/config/zentaopms.php"     "$DST/config/zentaopms.php"     2>/dev/null && echo "[ProManage] Synced config/zentaopms.php"

# ZIN UI framework overrides (navGroup, oldPages, menu hide CSS)
cp -u  "$SRC/module/common/lang/menu.php"      "$DST/module/common/lang/menu.php"       2>/dev/null && echo "[ProManage] Synced module/common/lang/menu.php"
cp -u  "$SRC/module/index/config.php"          "$DST/module/index/config.php"           2>/dev/null && echo "[ProManage] Synced module/index/config.php"
cp -u  "$SRC/module/index/ui/index.html.php"   "$DST/module/index/ui/index.html.php"    2>/dev/null && echo "[ProManage] Synced module/index/ui/index.html.php"

echo "[ProManage] Targeted sync complete."

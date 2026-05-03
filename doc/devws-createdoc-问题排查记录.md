# 如何排除各种问题在禅道下用自己的页面生成新建文档的功能

## 问题描述

在开发工作台(devws 模块)中点击"新建文档"按钮，页面显示空白，无法加载创建文档的表单。

## 环境信息

- ZenTao PMS 22.1（开源版）
- Docker 部署（easysoft/zentao:latest）
- Apache DocumentRoot: `/apps/zentao/www`
- PHP 8.x + OPcache 开启
- requestType: GET（URL 格式为 `?m=module&f=method`）

---

## 踩坑记录

### 坑 1：权限检查拦截（checkPriv）

**现象**：点击"新建文档"后页面空白，URL 显示 `?m=devws&f=index`。

**排查过程**：
1. 在 `www/index.php` 中跟踪请求流程：
   - `parseRequest()` → 解析 URL 得到模块和方法
   - `checkPriv()` → 检查用户权限
   - `checkIframe()` → 检查是否在 iframe 中打开
   - `loadModule()` → 实际执行方法

2. 发现 `checkPriv()` 在 `module/common/model.php:1202` 调用 `hasPriv()`
3. `hasPriv()` 首先检查 `$config->{module}->groupPrivs[{method}]` 映射
4. 新方法 `devws.createDoc` 没有配置权限映射，导致权限检查失败
5. `deny()` 被调用，重定向到 `user-deny` 页面

**解决方案**：
在 `module/devws/config.php` 中添加权限映射：

```php
$config->devws->groupPrivs['createdoc'] = 'task';
```

这样 `hasPriv('devws', 'createDoc')` 会被映射为 `hasPriv('devws', 'task')`，复用已有的 `devws.task` 权限，无需在数据库中为新方法分配权限。

---

### 坑 2：渲染路径错误（oldPages 白名单）

**现象**：权限问题解决后，页面仍然空白。

**排查过程**：
1. `display()` 方法（`framework/base/control.class.php:952`）根据 `$config->index->oldPages` 数组选择渲染路径：
   - 在 oldPages 中 → 使用 `parse()`（旧式视图渲染，查找 `view/` 目录）
   - 不在 oldPages 中 → 使用 `render()`（ZIN UI 渲染，查找 `ui/` 目录）

2. 其他 devws 方法（`task`、`create`、`assignTo`）都已经在 oldPages 中
3. `createDoc` 是新增方法，不在 oldPages 中，导致走了 ZIN UI 渲染路径
4. ZIN 渲染找不到对应的 UI 视图文件，抛出异常

**解决方案**：
在 `module/index/config.php` 中添加：

```php
$config->index->oldPages[] = 'devws-createdoc';
```

强制使用旧式视图渲染，查找 `module/devws/view/createdoc.html.php`。

---

### 坑 3：OPcache 缓存导致配置不生效

**现象**：配置文件和代码修改后，页面仍然空白，日志显示修改后的代码未被执行。

**排查过程**：
1. 在 `createDoc` 方法开头添加 `file_put_contents()` 日志，发现日志文件从未被创建
2. 确认方法未被执行，请求在到达方法前已被拦截
3. 进一步在 `www/index.php` 顶部添加日志，同样未生成日志
4. 最终发现 Docker 架构的特殊性：
   - Apache DocumentRoot 是 `/apps/zentao/www`（容器内部路径）
   - 代码挂在 `/app`（Docker volume 从宿主机映射）
   - `docker-sync.sh` 负责在容器启动时将 `/app` 中的指定文件同步到 `/apps/zentao`
   - **运行时修改的文件在 `/app` 中，但 Apache 读取的是 `/apps/zentao` 中的文件！**
5. 而且 PHP OPcache 缓存了旧版本的编译结果

**解决方案**：
1. 手动同步文件到 Apache 实际使用的路径：
   ```bash
   cp -ru /app/module/devws/* /apps/zentao/module/devws/
   cp -u /app/module/index/config.php /apps/zentao/module/index/config.php
   ```
2. 清除 OPcache：
   ```php
   // 通过 Web 请求执行
   opcache_reset();
   ```

**关键发现**：`docker-sync.sh` 定义了哪些文件会被同步到实际运行环境，且只在容器启动时执行一次。`www/` 目录和 `framework/` 目录**不会**被同步（因为 easysoft 镜像有定制补丁）。

---

### 坑 4：iframe 高度为 0（CSS 百分比高度问题）

**现象**：经过以上修复后，请求能正常到达 `createDoc` 方法并渲染视图，但在 iframe 中仍然看不到内容（一片空白）。

**排查过程**：
1. 浏览器开发者工具检查发现 iframe 实际上加载了内容
2. 但 iframe 的高度为 0，导致内容不可见
3. 原因：`#createdoc-content` 使用了 `height: 100%`
4. 父容器 `.content` 使用 `flex: 1`（flex 布局中的剩余空间分配）
5. **CSS 规范中，百分比高度要求在父元素上有明确的高度定义**，而 `flex: 1` 计算出的高度不被视为"明确高度"
6. 因此 `height: 100%` 解析为 0

**解决方案**：
将 iframe 容器改为固定视口高度计算 + 绝对定位：

```css
#createdoc-content {
    display: none;
    position: relative;
    height: calc(100vh - 160px);
    min-height: 500px;
}
#createdoc-content .content-frame {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
}
```

---

### 坑 5：文件同步时机（开发效率陷阱）

**现象**：多次修改代码后页面行为无变化，误判为代码逻辑问题。

**排查过程**：
1. 反复修改 `control.php` 和视图文件
2. 通过 docker 日志确认文件确实被修改
3. 最终发现是因为只修改了 `/app` 下的文件，但没有同步到 `/apps/zentao`

**解决方案**：
每次修改代码后执行完整的同步和缓存清理：
```bash
docker exec promanage-web bash -c "
  cp -ru /app/module/devws/* /apps/zentao/module/devws/
  cp -u /app/module/index/config.php /apps/zentao/module/index/config.php
  curl -s http://localhost/oclear.php  # 清除 OPcache
"
```

---

## 最终解决方案总结

要在开发工作台（devws 模块）中正常显示新建文档页面，需要以下 5 个步骤：

### 1. 控制层 — 添加方法
`module/devws/control.php` 中添加 `createDoc()` 方法，负责获取文档库列表、渲染表单、处理 POST 提交。

### 2. 视图层 — 创建模板文件
`module/devws/view/createdoc.html.php` 中编写表单 HTML，包含：
- 文档库选择器
- 文档标题输入框
- 关键词输入框
- ZenEditor 富文本编辑器
- 权限控制（ACL）单选按钮
- AJAX 表单提交

### 3. 权限映射
`module/devws/config.php` 中添加：
```php
$config->devws->groupPrivs['createdoc'] = 'task';
```

### 4. 渲染路径
`module/index/config.php` 中添加：
```php
$config->index->oldPages[] = 'devws-createdoc';
```

### 5. 前端集成
`module/devws/view/index.html.php` 中添加：
- 用于加载创建文档页面的 iframe
- `openCreateDoc()` 和 `cancelCreateDoc()` JavaScript 函数
- 确保 iframe 容器使用绝对定位或固定高度（不能用百分比高度）

---

## ZenTao 关键架构要点

### 请求处理流程
```
www/index.php
  ├── parseRequest()     ← 解析 URL，设置模块和方法名
  ├── checkPriv()         ← 权限检查（checkPriv → hasPriv → groupPrivs）
  ├── checkIframe()       ← iframe 检测（防止页面在 iframe 外打开）
  └── loadModule()        ← 加载控制层，执行方法
      └── control::display()
          ├── render()    ← ZIN UI 渲染（oldPages 不包含时）
          └── parse()     ← 旧式视图渲染（oldPages 包含时）
```

### 渲染路径选择
`display()` 方法根据 `$config->index->oldPages` 决定渲染方式：
- `render()`：ZIN UI 方式，查找 `module/{mod}/ui/{method}.html.php`
- `parse()`：旧式方式，查找 `module/{mod}/view/{method}.html.php`

### Docker 部署架构
```
宿主机: ./ (项目目录)
  └─ Docker volume → /app  (容器内)
       └─ docker-sync.sh (仅在启动时执行)
            └─ cp -ru → /apps/zentao (Apache 实际读取路径)
```

### 权限检查流程
```
checkPriv(module, method)
  └─ hasPriv(module, method)
       ├─ groupPrivs[method] 存在？ → 映射到另一个权限重新检查
       └─ 不存在？ → getUserPriv()
            ├─ 管理员？ → true
            └─ 普通用户 → 检查 $rights[module][method]
```

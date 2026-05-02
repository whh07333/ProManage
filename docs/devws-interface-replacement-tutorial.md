# 禅道开发者工作台界面替换完整教程

## 一、背景说明

禅道PMS 22.1版本使用了ZIN UI框架，这是一个现代化的前端框架。我们的目标是：
- 为产品经理、测试、开发角色创建一个全新的开发者工作台界面
- 完全替换掉禅道默认的左侧菜单和内容区
- 实现左右两栏布局：左边是自定义菜单，右边是内容区

## 二、技术架构分析

### 2.1 禅道ZIN UI框架的页面结构

禅道的主界面由以下几个核心元素组成：

```
<body class="show-menu">
    <!-- 左侧菜单栏 -->
    <div id="menu" style="left: 0; width: 120px;">
        <!-- 菜单内容 -->
    </div>
    
    <!-- 内容区域（iframe容器） -->
    <div id="apps" style="left: 120px; right: 0;">
        <iframe src="模块页面"></iframe>
    </div>
    
    <!-- 底部工具栏 -->
    <div id="appsBar" style="left: 120px; bottom: 0;">
        <!-- 标签页等 -->
    </div>
</body>
```

### 2.2 关键CSS变量

```css
:root {
    --zt-menu-width: 120px;        /* 菜单展开宽度 */
    --zt-menu-fold-width: 64px;    /* 菜单折叠宽度 */
    --zt-apps-bar-height: 40px;    /* 底部工具栏高度 */
}
```

### 2.3 菜单显示/隐藏机制

禅道通过以下方式控制菜单的显示和隐藏：

1. **Cookie机制**：`hideMenu` cookie
   - 值为 `1` 或 `true`：隐藏菜单
   - 值为 `false` 或不存在：显示菜单

2. **Body Class机制**：
   ```php
   // module/index/ui/index.html.php
   set::bodyClass($this->cookie->hideMenu ? 'hide-menu' : 'show-menu');
   ```

3. **CSS样式控制**：
   ```css
   /* 展开状态 */
   body.show-menu #menu { width: 120px; }
   body.show-menu #apps { left: 120px; }
   
   /* 折叠状态 */
   body.hide-menu #menu { width: 64px; }
   body.hide-menu #apps { left: 64px; }
   ```

**重要发现**：禅道的 `hide-menu` 状态只是**折叠菜单**，而不是**完全隐藏菜单**！

## 三、实现步骤详解

### 步骤1：创建开发者工作台模块

创建 `module/devws` 模块，包含以下文件：

```
module/devws/
├── control.php          # 控制器
├── model.php            # 模型
├── lang/
│   └── zh-cn.php        # 语言文件
└── view/
    └── index.html.php   # 视图模板
```

**control.php** 核心代码：
```php
<?php
class devws extends control
{
    public function index()
    {
        $this->view->title = $this->lang->devws->common;
        
        // 加载数据
        $this->loadModel('devws');
        $this->view->tasks = $this->devws->getMyTasks();
        $this->view->bugs = $this->devws->getMyBugs();
        $this->view->projects = $this->devws->getMyProjects();
        $this->view->docs = $this->devws->getMyDocs();
        
        $this->display();
    }
}
```

**view/index.html.php** 核心布局：
```php
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $title;?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        
        /* 工作区容器 */
        .workspace { display: flex; height: 100vh; background: #f5f5f5; }
        
        /* 左侧菜单栏 */
        .sidebar { width: 220px; background: #fff; border-right: 1px solid #e8e8e8; display: flex; flex-direction: column; }
        
        /* 右侧内容区 */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
    </style>
</head>
<body>
    <div class="workspace">
        <div class="sidebar">
            <!-- 菜单内容 -->
        </div>
        <div class="main-content">
            <!-- 工作内容 -->
        </div>
    </div>
</body>
</html>
```

### 步骤2：实现角色判断和跳转

修改 `module/my/control.php`，在用户登录后判断角色并跳转：

```php
public function index()
{
    /* 产品经理、测试、开发跳转到开发者工作台 */
    $devwsRoles = array('po', 'qa', 'dev');
    if(in_array($this->app->user->role, $devwsRoles))
    {
        /* 设置cookie隐藏ZIN UI的左侧菜单 */
        setcookie('hideMenu', '1', 0, $this->config->webRoot);
        $this->locate($this->createLink('devws', 'index'));
    }

    /* 其他角色显示默认界面 */
    $this->view->title = $this->lang->my->common;
    echo $this->fetch('block', 'dashboard', 'dashboard=my');
}
```

**关键点**：
- 使用 `setcookie('hideMenu', '1', ...)` 设置cookie
- 使用 `$this->locate()` 进行跳转

### 步骤3：注册导航组

修改 `module/common/lang/menu.php`，注册devws模块的导航组：

```php
$lang->navGroup->devws = 'my';
```

**为什么要这样做？**
- 禅道通过 `navGroup` 来确定模块属于哪个导航分组
- 如果不注册，登录后跳转到devws会被重定向回my/index
- 设置为 'my' 表示devws属于"我的地盘"分组

### 步骤4：配置ZIN UI兼容性

修改 `module/index/config.php`，将devws页面添加到旧页面列表：

```php
$config->index->oldPages = array(
    'devws-index',  // 添加这一行
    // ... 其他页面
);
```

**为什么要这样做？**
- ZIN UI框架默认使用新的渲染方式
- devws使用传统的HTML模板，需要告诉ZIN UI按旧方式渲染
- 否则会出现JavaScript错误和空白页面

## 四、遇到的问题及解决方案

### 问题1：三栏布局问题

**现象**：
```
登录后显示：[禅道菜单 120px] + [devws菜单 220px] + [内容区]
```

**原因分析**：
1. 设置了 `hideMenu` cookie
2. ZIN UI读取cookie，设置body class为 `hide-menu`
3. **但是** `hide-menu` 只是折叠菜单，宽度变为64px，不是完全隐藏

**CSS证据**：
```css
/* module/index/css/index.ui.css */
.hide-menu #menu { width: 64px; }           /* 只是折叠 */
.hide-menu #apps { left: 64px; }            /* 内容区还是留了64px */
.hide-menu #appsBar { left: 64px; }         /* 工具栏还是留了64px */
```

### 问题2：左侧空白区域问题

**现象**：
```
虽然菜单隐藏了，但左侧还有64px的空白区域
```

**根本原因**：
- `#menu` 元素虽然 `display: none`，但 `#apps` 和 `#appsBar` 的 `left` 属性仍然是64px
- CSS选择器优先级问题：`.hide-menu #apps` 的优先级高于行内样式

**第一次尝试（失败）**：
```php
// module/index/ui/index.html.php
$hideMenuCSS = "
#menu { display: none !important; }
#apps { left: 0 !important; }
#appsBar { left: 0 !important; }
";
```

**失败原因**：
- 虽然添加了CSS，但 `.hide-menu #apps` 的选择器优先级更高
- 行内样式被覆盖

### 问题3：最终解决方案

**方案一：修改CSS文件（推荐）**

修改 `module/index/css/index.ui.css`：

```css
/* 原有代码 */
#apps {position: fixed; left: var(--zt-menu-width); top: 0; bottom: var(--zt-apps-bar-height); right: 0; background-color: var(--zt-page-bg); transition: left .1s;}
.hide-menu #apps {left: var(--zt-menu-fold-width)!important;}

#appsBar {display: flex; flex-wrap: nowrap; align-items: center; position: fixed; left: var(--zt-menu-width); bottom: 0; right: 0; height: var(--zt-apps-bar-height); padding: 0 4px; background: var(--zt-apps-bar-bg); box-shadow: 0 -2px 12px rgba(0,0,0,.02); transition: left .1s; border-top-width: 1px;}
.hide-menu #appsBar {left: var(--zt-menu-fold-width)!important;}

/* 添加以下代码 */
/* devws: 完全隐藏菜单时，apps全屏显示 */
#menu[style*="display: none"] ~ #apps,
#menu[style*="display:none"] ~ #apps {left: 0 !important;}

/* devws: 完全隐藏菜单时，appsBar全屏显示 */
#menu[style*="display: none"] ~ #appsBar,
#menu[style*="display:none"] ~ #appsBar {left: 0 !important;}
```

**方案二：在PHP中注入CSS（更直接）**

修改 `module/index/ui/index.html.php`：

```php
<?php
namespace zin;

$this->app->loadConfig('message');

/* 产品经理、测试、开发强制隐藏菜单 */
$devwsRoles = array('po', 'qa', 'dev');
$forceHideMenu = in_array($this->app->user->role, $devwsRoles);

$hideMenuCSS = '';
if($forceHideMenu)
{
    $hideMenuCSS = "
    #menu { display: none !important; }
    body.hide-menu #apps { left: 0 !important; }
    body.hide-menu #appsBar { left: 0 !important; }
    ";
}

h::css("
#versionTitle {background-image: url('{$config->webRoot}theme/default/images/main/version-upgrade.svg');}
.icon-version {width: 20px; height: 24px; margin: -4px 3px 0px 0px; background-image: url('{$config->webRoot}theme/default/images/main/version-new.svg');}
.icon-version:before {content:'';}
$hideMenuCSS
");
```

**为什么这样能成功？**
1. `body.hide-menu #apps` 选择器优先级足够高
2. `!important` 确保覆盖其他样式
3. 在页面渲染时直接注入CSS，确保生效

## 五、完整修改文件清单

### 5.1 新增文件

```
module/devws/
├── control.php          # 控制器
├── model.php            # 数据模型
├── lang/
│   └── zh-cn.php        # 中文语言包
└── view/
    └── index.html.php   # 视图模板
```

### 5.2 修改文件

1. **module/my/control.php**
   - 添加角色判断和跳转逻辑
   - 设置hideMenu cookie

2. **module/common/lang/menu.php**
   - 注册devws导航组

3. **module/index/config.php**
   - 添加devws到oldPages列表

4. **module/index/ui/index.html.php**
   - 添加角色判断
   - 注入隐藏菜单的CSS

5. **module/index/css/index.ui.css**（可选）
   - 添加菜单完全隐藏时的样式规则

## 六、技术要点总结

### 6.1 禅道菜单隐藏的三种状态

```css
/* 状态1：菜单展开 */
body.show-menu #menu { width: 120px; }
body.show-menu #apps { left: 120px; }

/* 状态2：菜单折叠（hide-menu） */
body.hide-menu #menu { width: 64px; }
body.hide-menu #apps { left: 64px; }

/* 状态3：菜单完全隐藏（我们的目标） */
#menu { display: none; }
#apps { left: 0; }
```

### 6.2 CSS选择器优先级

```
!important > 行内样式 > ID选择器 > 类选择器 > 元素选择器
```

我们的解决方案使用了：
```css
body.hide-menu #apps { left: 0 !important; }
```

优先级计算：
- `body.hide-menu #apps` = 1个元素 + 1个类 + 1个ID = 0-1-1-1
- 加上 `!important` 后，优先级最高

### 6.3 为什么一开始会有空白区域

**问题根源**：
1. ZIN UI的 `hide-menu` 类只是折叠菜单，不是隐藏菜单
2. CSS中 `.hide-menu #apps { left: 64px !important; }` 的优先级很高
3. 我们添加的行内样式 `#apps { left: 0 !important; }` 被覆盖

**解决思路**：
1. 使用更高优先级的选择器：`body.hide-menu #apps`
2. 加上 `!important` 确保生效
3. 或者修改CSS文件，添加兄弟选择器规则

### 6.4 关键技术点

1. **Cookie设置时机**：在跳转前设置，确保跳转后立即生效
2. **Body Class控制**：ZIN UI根据cookie设置body class
3. **CSS优先级**：理解选择器优先级是解决样式覆盖的关键
4. **ZIN UI兼容**：通过oldPages配置确保传统模板正常渲染
5. **导航组注册**：确保模块路由正常工作

## 七、测试验证

使用Playwright进行自动化测试：

```javascript
const { chromium } = require('playwright');

async function test() {
    const browser = await chromium.launch({ headless: false, devtools: true });
    const context = await browser.newContext();
    const page = await context.newPage();
    
    // 清除cookie
    await context.clearCookies();
    
    // 登录
    await page.goto('http://localhost:8080/');
    await page.fill('#account', 'dev1');
    await page.fill('#password', 'password');
    await page.click('#submit');
    await page.waitForNavigation();
    
    // 检查菜单是否隐藏
    const menuDisplay = await page.$eval('#menu', el => 
        window.getComputedStyle(el).display
    );
    console.log('Menu display:', menuDisplay); // 应该是 'none'
    
    // 检查内容区left值
    const appsLeft = await page.$eval('#apps', el => 
        window.getComputedStyle(el).left
    );
    console.log('Apps left:', appsLeft); // 应该是 '0px'
    
    await browser.close();
}

test();
```

## 八、注意事项

1. **OPcache清理**：修改PHP文件后需要清理OPcache
   ```bash
   docker exec zentao-web php -r "opcache_reset();"
   ```

2. **浏览器缓存**：修改CSS后需要强制刷新浏览器（Ctrl+F5）

3. **角色判断**：确保角色字段值正确（'po', 'qa', 'dev'）

4. **权限配置**：确保devws模块有正确的访问权限

5. **测试覆盖**：测试不同角色登录后的界面显示

## 九、扩展建议

1. **可配置化**：将需要隐藏菜单的角色配置到config文件中
2. **响应式设计**：为不同屏幕尺寸优化布局
3. **性能优化**：减少不必要的CSS和JavaScript加载
4. **用户体验**：添加菜单切换动画和过渡效果

## 十、总结

禅道ZIN UI框架的菜单隐藏机制需要深入理解：
- `hide-menu` 类只是折叠菜单，不是完全隐藏
- 需要通过CSS强制覆盖来实现完全隐藏
- 理解CSS选择器优先级是解决样式问题的关键
- Cookie和Body Class的配合使用是控制菜单状态的核心

通过本教程，你应该能够：
1. 理解禅道ZIN UI的页面结构
2. 掌握菜单隐藏的完整实现流程
3. 解决CSS样式覆盖问题
4. 实现自定义界面的完全替换

---

**文档版本**：1.0  
**最后更新**：2026-04-26  
**适用版本**：禅道PMS 22.1+

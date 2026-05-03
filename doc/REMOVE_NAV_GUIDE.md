# 去掉禅道顶部导航栏的几种方法

## 方法一：iframe + CSS/JS 注入（devws 编辑项目页面使用）

在 devws 模块的视图模板中，将禅道原始页面嵌入 iframe，通过 `onload` 注入样式和脚本。

### 关键步骤

1. **父页面**：放置 iframe，URL 加上 `onlybody=yes`
   ```php
   <iframe id="myIframe" src="<?php echo helper::createLink('module', 'method', $params, '', true);?>"></iframe>
   ```
   第5个参数 `true` 会在 URL 添加 `onlybody=yes`。

2. **`onload` 中注入 CSS** 到 iframe 的 `<head>`
   ```javascript
   iframe.onload = function() {
       var doc = iframe.contentDocument || iframe.contentWindow.document;
       var style = doc.createElement('style');
       style.textContent = '#mainNavbar,#header,#menu,#mainMenu,#footer,.navbar,.breadcrumb,.page-actions,.page-title,#heading{display:none!important}';
       doc.head.appendChild(style);
   };
   ```

3. **注入 JS 定时清理**（Zin AJAX 会重新渲染导航元素）
   ```javascript
   var script = doc.createElement('script');
   script.textContent = '(function(){' +
   'var S=["#mainNavbar","#header","#menu","#mainMenu","#footer",".navbar",".breadcrumb"];' +
   'function K(){S.forEach(function(s){document.querySelectorAll(s).forEach(function(e){' +
   'e.style.setProperty("display","none","important")})})}K();setInterval(K,200);' +
   '})()';
   doc.head.appendChild(script);
   ```

### 注意事项

- **`<?php helper::createLink()?>` 必须加 PHP 标签**——在 `.html.php` 文件中，`<script>` 内的内容属于 HTML 模式，PHP 函数必须用 `<?php echo ... ?>` 包裹
- **不要用数组 `.join()` + PHP 拼接的方式**——JavaScript 的 `.` 是属性访问运算符，不是字符串连接符；PHP 的 `.` 是字符串连接符，两者混用会产生 JS 语法错误
- **Zin AJAX 会重新渲染导航**——CSS 注入 `<head>` 可以持久化，但 Zin AJAX 响应中可能包含自己的 `<style>` 覆盖你的规则。需要用 `setInterval` 或 `MutationObserver` 反复清除
- **`onlybody=yes` 不是万能**——部分页面可能不完全遵守此参数，导航仍会出现

## 方法二：直接调用禅道 Kernel API（更彻底，但更重）

在 devws 的 control 中直接调用禅道数据模型，自行渲染表单：

```php
public function editProject($projectID)
{
    $projectModel = $this->loadModel('project');
    $project      = $projectModel->getById($projectID);
    /* 自行渲染表单，完全控制 HTML 输出 */
    $this->view->project = $project;
    $this->display();
}
```

优点：完全自主控制样式和功能。
缺点：需要重建大量表单逻辑（验证、联动、权限等）。

## 方法三：在 Zin 层面禁用导航（框架级）

修改 `lib/zin/wg/mainnavbar/v1.php` 或通过 Zin 的渲染选项禁用导航渲染。此方法影响全局，慎用。

## 推荐方案：方法一

对于 devws 模块的"包装已有禅道页面"场景，方法一（iframe + 注入）是最务实的：
- 保留原始页面的全部字段和交互逻辑
- 通过 CSS/JS 注入覆盖样式
- 工作量大，维护成本低

## 已知问题

- **Zin AJAX 覆盖**：Zin 框架的 AJAX 页面加载会重新注入样式，需要用 `setInterval` 持续清理（每 200ms 执行一次）
- **`zui.Modal.hide` 拦截**：表单提交成功后禅道会调用 `zui.Modal.hide()` 关闭弹窗。需要重写此方法，重定向到目标页面而非关闭
- **`window.beforePageLoad`**：禅道页面加载前会检查此函数。返回 `false` 可取消 AJAX 加载，避免重定向后页面被刷新

## 排查步骤

如果导航栏未被隐藏：

1. **确认 onload 是否触发**：先在父页面直接修改 iframe 的边框颜色
2. **确认 contentDocument 可访问**：同源策略限制，确保 iframe 和父页面同域名
3. **确认 CSS 注入生效**：注入 `body{outline:5px solid red!important}` 观察效果
4. **确认 JS 注入无语法错误**：检查浏览器控制台
5. **确认 PHP 标签正确**：`<script>` 内的 PHP 代码必须用 `<?php echo ?>` 包裹

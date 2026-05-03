# ProManage 项目分析报告

> 生成日期：2026-05-02
> 分析工具：Claude Code
> 项目版本：v0.2（基于 ZenTao PMS 22.1）

---

## 一、项目概况

| 项目 | 信息 |
|------|------|
| **项目名称** | ProManage - 禅道项目管理增强版 |
| **基础系统** | ZenTao PMS 22.1（开源版） |
| **开发语言** | PHP 8.1 + JavaScript |
| **Web 服务器** | Apache + RoadRunner |
| **数据库** | MySQL 8.0 / SQLite |
| **前端框架** | ZIN UI（禅道内置 UI 框架） |
| **后端框架** | ZenTaoPHP（MVC） |
| **容器化** | Docker + docker-compose |
| **版本管理** | Git（GitHub: whh07333/ProManage） |
| **当前版本** | v0.2 |

---

## 二、技术架构

### 2.1 整体架构

```
┌─────────────────────────────────────────────────────────┐
│                      浏览器客户端                        │
│   禅道页面  │  聊天窗口(Modal)  │  开发者工作台(devws)   │
└──────┬────────────────┬───────────────────┬──────────────┘
       │ AJAX/轮询       │ AJAX              │ PHP渲染
┌──────┴────────────────┴───────────────────┴──────────────┐
│            Docker 容器 (zentao-web)                       │
│  ┌────────────────────────────────────────────────────┐  │
│  │        Apache + PHP 8.1 / RoadRunner               │  │
│  │  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌─────────┐  │  │
│  │  │ user │ │ chat │ │devws │ │jenkins│ │ 109个   │  │  │
│  │  │ 模块 │ │ 模块 │ │ 模块 │ │ 模块  │ │ 标准模块 │  │  │
│  │  └──┬───┘ └──┬───┘ └──┬───┘ └──┬───┘ └────┬────┘  │  │
│  └─────┼────────┼────────┼────────┼──────────┼────────┘  │
│        └────────┴────────┴────────┴──────────┘           │
│              ZenTao Framework (DAO/Control/Model/View/ZIN)│
└────────────────────────────┬─────────────────────────────┘
                             │ MySQL
┌────────────────────────────┼─────────────────────────────┐
│       Docker 容器 (zentao-mysql)                          │
│  ┌─────────────────────────┴──────────────────────────┐  │
│  │               MySQL 8.0                            │  │
│  │  zt_user / zt_task / zt_bug / zt_story / zt_doc   │  │
│  │  zt_chatroom / zt_chatmessage / zt_chatroommember  │  │
│  │  zt_jenkins / zt_jenkinsbuild / zt_jenkinsbuildrel │  │
│  └────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────┘
```

### 2.2 核心技术栈

| 层次 | 技术 | 版本 |
|------|------|------|
| Web 服务器 | Apache + PHP-FPM | PHP 8.1 |
| 高性能服务 | RoadRunner (Spiral) | v2.0 |
| 数据库 | MySQL | 8.0 |
| 缓存 | APCu / Redis | - |
| 前端 | ZIN UI + jQuery | - |
| PHP 依赖 | Composer | - |
| 异步任务 | RoadRunner Jobs | ^3.0 |
| 测试 | Playwright | ^1.59.1 |

---

## 三、目录结构

```
ProManage/
├── api/                    # REST API 入口（v1 版本）
│   └── v1/entries/         # 100+ API 端点定义
├── bin/                    # 可执行文件（RoadRunner 二进制）
├── build/                  # 构建脚本
├── config/                 # 配置文件
│   ├── config.php          # 主配置
│   ├── db.php              # 数据库配置（默认 SQLite）
│   └── my.php              # 自定义配置（覆盖默认值）
├── db/                     # SQL 脚本
│   ├── install.sql         # 初始化安装
│   ├── chat.sql            # 聊天模块表结构
│   ├── jenkins.sql         # Jenkins 集成表结构
│   └── update*.sql         # 历史升级脚本
├── doc/                    # 项目文档
├── docs/                   # 技术文档
├── extension/              # 扩展模块（biz 商业版扩展）
├── framework/              # 框架核心
│   └── zand/               # ZenTaoPHP 框架 + RoadRunner
├── hook/                   # Git 钩子
├── lib/                    # 第三方库
│   ├── vendor/             # Composer 依赖
│   ├── sqlparser/          # SQL 解析器
│   └── zin/                # ZIN UI 框架
├── misc/                   # 杂项（CI Dockerfile 等）
├── module/                 # 核心业务模块（109个）
│   ├── chat/               # ✨ 聊天模块（自研）
│   ├── devws/              # ✨ 开发者工作台（自研）
│   ├── user/               # 用户模块
│   ├── project/            # 项目管理
│   ├── task/               # 任务管理
│   ├── bug/                # 缺陷管理
│   ├── story/              # 需求管理
│   └── ...                 # 其他标准模块
├── roadrunner/             # RoadRunner 配置
├── sdk/                    # SDK
├── www/                    # Web 根目录（静态资源）
├── docker-compose.yml      # Docker 编排
├── package.json            # Node.js 依赖（Playwright 测试）
└── VERSION                 # 版本号 22.1
```

---

## 四、自研模块分析

### 4.1 聊天模块（chat）

**功能概述**：集成即时通讯功能，支持群聊和私聊。

| 功能 | 状态 |
|------|------|
| 聊天入口（chatBar） | ✅ 完成 |
| 模式化聊天窗口 | ✅ 完成 |
| 聊天室列表 / 联系人列表 | ✅ 完成 |
| 创建聊天室 / 添加成员 | ✅ 完成 |
| 私聊功能 | ✅ 完成 |
| 消息收发（AJAX 轮询 5s） | ✅ 完成 |
| 未读消息角标 / 已读标记 | ✅ 完成 |
| 权限配置 | ✅ 完成 |

**关键文件**：
- [control.php](module/chat/control.php) — 12 个 API 接口
- [model.php](module/chat/model.php) — 数据库操作层

**数据库表**：zt_chatroom、zt_chatmessage、zt_chatroommember、zt_chatmention

**待优化**：轮询 → WebSocket 实时推送

### 4.2 开发者工作台（devws）

**功能概述**：为开发人员提供定制化的工作界面，集中展示任务/缺陷/需求/文档/项目。

| 功能 | 状态 |
|------|------|
| 三栏布局（工作/文档/项目） | ✅ 完成 |
| 我的工作（任务/缺陷/需求/待办） | ✅ 完成 |
| 我的文档 / 我的项目 | ✅ 完成 |
| Tab 切换 | ✅ 完成 |
| 登录后默认跳转 | ❌ 未完成 |
| 隐藏旧导航入口 | ❌ 未完成 |

**关键文件**：
- [control.php](module/devws/control.php) — 控制器
- [model.php](module/devws/model.php) — 数据访问层
- [config.php](module/devws/config.php) — 模块配置

### 4.3 Jenkins 集成模块（规划中）

**数据库表已创建**：zt_jenkins、zt_jenkinsbuild、zt_jenkinsbuildrelation

---

## 五、API 接口

项目提供了完整的 REST API（v1 版本），包含 100+ 个端点，覆盖：

- 用户与权限：user, group, department
- 项目管理：project, execution, program
- 需求与任务：story, task, bug
- 产品管理：product, productplan, release
- 文档管理：doc, doclib
- CI/CD：pipeline, job, build
- 代码仓库：repo, mr
- 测试管理：testcase, testsuite, testtask
- 自研模块：chat 相关接口

---

## 六、当前问题与风险

### 严重问题

| # | 问题 | 影响 | 状态 |
|---|------|------|------|
| 1 | 用户登录失败 | 所有用户无法登录系统 | 未解决 |
| 2 | PHP Segmentation Fault | 容器内 PHP 语法检查崩溃 | 未解决 |

**登录问题根因**：禅道使用 `md5(md5(password) + rand)` 双重加密，数据库中密码 hash 值可能不匹配。

### 中等问题

| # | 问题 | 影响 |
|---|------|------|
| 3 | 容器双目录同步 | /app 挂载 vs /apps/zentao 运行目录不一致 |
| 4 | devws 登录重定向未生效 | 因登录问题无法验证 |

---

## 七、数据库状态

### 核心业务表（禅道标准）
zt_user, zt_task, zt_bug, zt_story, zt_project, zt_product, zt_doc, zt_execution 等

### 自研模块表
| 表名 | 用途 |
|------|------|
| zt_chatroom | 聊天室 |
| zt_chatmessage | 聊天消息 |
| zt_chatroommember | 聊天室成员 |
| zt_chatmention | 消息提及 |
| zt_jenkins | Jenkins 服务器 |
| zt_jenkinsbuild | Jenkins 构建记录 |
| zt_jenkinsbuildrelation | 构建关联关系 |

---

## 八、部署架构

### 当前 Docker 配置

| 服务 | 镜像 | 端口 | 说明 |
|------|------|------|------|
| zentao-mysql | mysql:8.0 | 3306 | 数据库，含健康检查 |
| zentao-web | easysoft/zentao:latest | 8080→80 | 禅道应用 |

**数据卷**：
- mysql-data → MySQL 数据持久化
- zentao-data → /data/zentao 应用数据
- session-data → /data/php/session 会话数据
- ./:/app → 源码挂载（存在双目录问题）

---

## 九、迭代进展

| 迭代 | 目标 | 状态 |
|------|------|------|
| 迭代1（v0.1） | 聊天模块 + 基础设施 | ✅ 已完成 |
| 迭代1.5（v0.2） | 开发者工作台 + devws 界面完善 | ⚠️ 部分完成 |
| 迭代2 | 数据可视化 + 移动端 | 🔜 待启动 |
| 迭代3 | AI 辅助 + 企业级权限 | 🔜 待启动 |
| 迭代4 | 方法论融合 + 知识管理 | 🔜 待启动 |

---

## 十、建议与下一步

### 紧急（P0）
1. **修复登录问题** — 重置 admin 密码 hash，修复 zt_usergroup 表
2. **解决 PHP Segfault** — 检查容器 PHP 配置（OPCache/JIT）
3. **统一容器目录** — 解决 /app 与 /apps/zentao 的双目录问题

### 重要（P1）
4. **优化 Dockerfile** — 构建自定义镜像，去除双目录依赖
5. **聊天 WebSocket 升级** — 替换 5s 轮询为 WebSocket 实时推送
6. **devws 登录重定向** — 登录修复后完成非 admin 用户跳转

### 一般（P2）
7. **Jenkins CI/CD 集成** — 数据库表已就绪，开发集成功能
8. **聊天窗口响应式** — 适配移动端和小屏幕
9. **devws 数据缓存** — 减少数据库查询压力

<?php
/**
 * The language file of git module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2026 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      ZenTao Team
 * @package     git
 * @link        https://www.zentao.net
 */
$lang->git = new stdclass();
$lang->git->common = '代码仓库';
$lang->git->index = '代码仓库首页';
$lang->git->webhook = 'Webhook配置';
$lang->git->gitlabWebhook = 'GitLab Webhook';
$lang->git->githubWebhook = 'GitHub Webhook';
$lang->git->webhookUrl = 'Webhook URL';
$lang->git->webhookDesc = '将此URL配置到GitLab或GitHub的Webhook设置中，当代码提交时，系统将自动关联提交与任务。';
$lang->git->webhookSetup = '设置步骤：1. 登录GitLab/GitHub；2. 进入仓库设置；3. 找到Webhook设置；4. 粘贴上面的URL；5. 选择触发事件为Push；6. 保存设置。';
$lang->git->commitFormat = '提交信息格式';
$lang->git->commitFormatDesc = '为了使代码提交与任务自动关联，请按照以下格式编写提交信息：';
$lang->git->commitFormatExample = '任务 #123: 完成登录功能\n\n详细描述...';
$lang->git->commits = '代码提交';
$lang->git->commit = '提交';
$lang->git->author = '作者';
$lang->git->date = '日期';
$lang->git->message = '提交信息';
$lang->git->relatedCommits = '相关提交';

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
$lang->git->common = 'Code Repository';
$lang->git->index = 'Code Repository Home';
$lang->git->webhook = 'Webhook Configuration';
$lang->git->gitlabWebhook = 'GitLab Webhook';
$lang->git->githubWebhook = 'GitHub Webhook';
$lang->git->webhookUrl = 'Webhook URL';
$lang->git->webhookDesc = 'Configure this URL in GitLab or GitHub Webhook settings. When code is committed, the system will automatically link commits with tasks.';
$lang->git->webhookSetup = 'Setup steps: 1. Log in to GitLab/GitHub; 2. Go to repository settings; 3. Find Webhook settings; 4. Paste the URL above; 5. Select Push as the trigger event; 6. Save settings.';
$lang->git->commitFormat = 'Commit Message Format';
$lang->git->commitFormatDesc = 'To automatically link code commits with tasks, please follow this format for commit messages:';
$lang->git->commitFormatExample = 'Task #123: Complete login functionality\n\nDetailed description...';
$lang->git->commits = 'Code Commits';
$lang->git->commit = 'Commit';
$lang->git->author = 'Author';
$lang->git->date = 'Date';
$lang->git->message = 'Commit Message';
$lang->git->relatedCommits = 'Related Commits';

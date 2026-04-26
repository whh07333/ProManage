<?php
if($extView = $this->getExtViewFile(__FILE__)){include $extView; return helper::cd();}
?>
<?php include '../../common/view/header.html.php';?>

<div id='mainContent' class='main-content fadeIn'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo $lang->git->common;?></h2>
    </div>
    
    <div class='panel panel-default'>
      <div class='panel-heading'>
        <h3 class='panel-title'><?php echo $lang->git->webhook;?></h3>
      </div>
      <div class='panel-body'>
        <div class='row'>
          <div class='col-md-6'>
            <h4><?php echo $lang->git->gitlabWebhook;?></h4>
            <p><?php echo $lang->git->webhookDesc;?></p>
            <div class='form-group'>
              <label for='gitlabWebhookUrl'><?php echo $lang->git->webhookUrl;?></label>
              <input type='text' id='gitlabWebhookUrl' class='form-control' value='<?php echo helper::createLink('git', 'gitlabWebhook', '', '', true);?>' readonly>
            </div>
            <p><?php echo $lang->git->webhookSetup;?></p>
          </div>
          <div class='col-md-6'>
            <h4><?php echo $lang->git->githubWebhook;?></h4>
            <p><?php echo $lang->git->webhookDesc;?></p>
            <div class='form-group'>
              <label for='githubWebhookUrl'><?php echo $lang->git->webhookUrl;?></label>
              <input type='text' id='githubWebhookUrl' class='form-control' value='<?php echo helper::createLink('git', 'githubWebhook', '', '', true);?>' readonly>
            </div>
            <p><?php echo $lang->git->webhookSetup;?></p>
          </div>
        </div>
      </div>
    </div>
    
    <div class='panel panel-default'>
      <div class='panel-heading'>
        <h3 class='panel-title'><?php echo $lang->git->commitFormat;?></h3>
      </div>
      <div class='panel-body'>
        <p><?php echo $lang->git->commitFormatDesc;?></p>
        <pre><?php echo $lang->git->commitFormatExample;?></pre>
      </div>
    </div>
  </div>
</div>

<?php include '../../common/view/footer.html.php';?>

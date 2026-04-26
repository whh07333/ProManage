<?php
if($extView = $this->getExtViewFile(__FILE__)){include $extView; return helper::cd();}
?>
<?php include '../../common/view/header.html.php';?>

<div id='mainContent' class='main-content fadeIn'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo $lang->jenkins->common;?></h2>
      <div class='actions'>
        <a href='<?php echo helper::createLink('jenkins', 'create');?>' class='btn btn-primary'><i class='icon icon-plus'></i> <?php echo $lang->jenkins->create;?></a>
      </div>
    </div>
    
    <?php if(empty($servers)):?>
      <div class='empty-tip'>
        <p><?php echo $lang->jenkins->noServer;?></p>
        <a href='<?php echo helper::createLink('jenkins', 'create');?>' class='btn btn-primary'><i class='icon icon-plus'></i> <?php echo $lang->jenkins->create;?></a>
      </div>
    <?php else:?>
      <table class='table table-hover table-striped tablesorter'>
        <thead>
          <tr>
            <th class='w-200px'><?php echo $lang->jenkins->name;?></th>
            <th class='w-400px'><?php echo $lang->jenkins->url;?></th>
            <th class='w-150px'><?php echo $lang->jenkins->username;?></th>
            <th class='w-150px'><?php echo $lang->jenkins->createdDate;?></th>
            <th class='w-100px'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($servers as $server):?>
            <tr>
              <td><?php echo $server->name;?></td>
              <td><?php echo html::a($server->url, $server->url, '_blank');?></td>
              <td><?php echo $server->username;?></td>
              <td><?php echo $server->createdDate;?></td>
              <td>
                <div class='actions'>
                  <?php echo html::a(helper::createLink('jenkins', 'edit', "id=$server->id"), '<i class="icon icon-edit"></i>', '', "class='btn btn-xs' title='{$lang->edit}'");?>
                  <?php echo html::a(helper::createLink('jenkins', 'delete', "id=$server->id"), '<i class="icon icon-trash"></i>', 'hiddenwin', "title='{$lang->delete}' class='btn btn-xs btn-danger'" , "{$lang->confirmDelete}");?>
                </div>
              </td>
            </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    <?php endif;?>
  </div>
</div>

<?php include '../../common/view/footer.html.php';?>

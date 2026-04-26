<?php
if($extView = $this->getExtViewFile(__FILE__)){include $extView; return helper::cd();}
?>
<?php include '../../common/view/header.html.php';?>

<div id='mainContent' class='main-content fadeIn'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo $lang->jenkins->edit;?></h2>
    </div>
    
    <form method='post' class='form-condensed' id='jenkinsForm'>
      <table class='table table-form'>
        <tr>
          <th class='w-100px'><?php echo $lang->jenkins->name;?> <span class='required'>*</span></th>
          <td class='w-500px'>
            <input type='text' name='name' id='name' class='form-control' value='<?php echo $server->name;?>' required>
          </td>
          <td></td>
        </tr>
        <tr>
          <th><?php echo $lang->jenkins->url;?> <span class='required'>*</span></th>
          <td>
            <input type='url' name='url' id='url' class='form-control' value='<?php echo $server->url;?>' required placeholder='https://jenkins.example.com'>
          </td>
          <td></td>
        </tr>
        <tr>
          <th><?php echo $lang->jenkins->username;?> <span class='required'>*</span></th>
          <td>
            <input type='text' name='username' id='username' class='form-control' value='<?php echo $server->username;?>' required>
          </td>
          <td></td>
        </tr>
        <tr>
          <th><?php echo $lang->jenkins->token;?> <span class='required'>*</span></th>
          <td>
            <input type='text' name='token' id='token' class='form-control' value='<?php echo $server->token;?>' required placeholder='Jenkins API token'>
          </td>
          <td></td>
        </tr>
        <tr>
          <th><?php echo $lang->jenkins->testConnection;?></th>
          <td>
            <button type='button' class='btn btn-primary' id='testConnectionBtn'><i class='icon icon-refresh'></i> <?php echo $lang->jenkins->testConnection;?></button>
            <span id='connectionResult' class='ml-2'></span>
          </td>
          <td></td>
        </tr>
        <tr>
          <th></th>
          <td colspan='2'>
            <div class='form-actions'>
              <button type='submit' class='btn btn-primary'><i class='icon icon-save'></i> <?php echo $lang->save;?></button>
              <button type='button' class='btn btn-default' onclick='history.back()'><i class='icon icon-back'></i> <?php echo $lang->cancel;?></button>
            </div>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>

<script>
$(function() {
  // 测试连接
  $('#testConnectionBtn').click(function() {
    var url = $('#url').val();
    var username = $('#username').val();
    var token = $('#token').val();
    
    if(!url || !username || !token) {
      $('#connectionResult').html('<span class="text-danger">请填写所有必填字段</span>');
      return;
    }
    
    $('#connectionResult').html('<span class="text-info">正在测试连接...</span>');
    
    $.ajax({
      url: '<?php echo helper::createLink('jenkins', 'testConnection');?>',
      type: 'POST',
      data: {url: url, username: username, token: token},
      dataType: 'json',
      success: function(response) {
        if(response.status == 'success') {
          $('#connectionResult').html('<span class="text-success">连接成功</span>');
        } else {
          $('#connectionResult').html('<span class="text-danger">连接失败</span>');
        }
      },
      error: function() {
        $('#connectionResult').html('<span class="text-danger">连接失败</span>');
      }
    });
  });
});
</script>

<?php include '../../common/view/footer.html.php';?>

<?php
/**
 * Custom create task view for devws drawer.
 */
$postUrl = helper::createLink('devws', 'create');
?>
<div class="create-container" style="padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;color:#333;max-width:560px;">

    <div class="create-heading" style="padding:20px 24px 16px;border-bottom:1px solid #f0f0f0;">
        <div style="font-size:18px;font-weight:600;color:#1a1a2e;"><?php echo $lang->task->create;?></div>
    </div>

    <form method="post" id="createForm" action="<?php echo $postUrl;?>">
        <div style="padding:20px 24px;">
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:500;margin-bottom:8px;"><?php echo $lang->task->execution;?> <span style="color:#ff4d4f;">*</span></label>
                <select name="execution" id="executionSelect" style="width:100%;height:38px;border:1px solid #d9d9d9;border-radius:6px;padding:0 12px;font-size:14px;background:#fff;">
                    <option value=""><?php echo $lang->devws->pleaseSelect ?? '请选择';?></option>
                    <?php foreach($executions as $exec):?>
                    <option value="<?php echo $exec->id;?>" <?php echo ($exec->id == $executionID) ? 'selected' : '';?>><?php echo $exec->name;?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:500;margin-bottom:8px;"><?php echo $lang->task->name;?> <span style="color:#ff4d4f;">*</span></label>
                <input type="text" name="name" style="width:100%;height:38px;border:1px solid #d9d9d9;border-radius:6px;padding:0 12px;font-size:14px;box-sizing:border-box;" autocomplete="off">
            </div>
            <div style="display:flex;gap:16px;margin-bottom:20px;">
                <div style="flex:1;">
                    <label style="display:block;font-size:13px;font-weight:500;margin-bottom:8px;"><?php echo $lang->task->type;?> <span style="color:#ff4d4f;">*</span></label>
                    <select name="type" style="width:100%;height:38px;border:1px solid #d9d9d9;border-radius:6px;padding:0 12px;font-size:14px;background:#fff;">
                        <?php foreach($lang->task->typeList as $key => $label):?>
                        <option value="<?php echo $key;?>" <?php echo $key == 'development' ? 'selected' : '';?>><?php echo $label;?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div style="flex:1;">
                    <label style="display:block;font-size:13px;font-weight:500;margin-bottom:8px;"><?php echo $lang->task->pri;?></label>
                    <select name="pri" style="width:100%;height:38px;border:1px solid #d9d9d9;border-radius:6px;padding:0 12px;font-size:14px;background:#fff;">
                        <?php foreach($lang->task->priList as $key => $label):?>
                        <option value="<?php echo $key;?>" <?php echo $key == '3' ? 'selected' : '';?>><?php echo $label;?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:500;margin-bottom:8px;"><?php echo $lang->task->assignedTo;?></label>
                <select name="assignedTo" style="width:100%;height:38px;border:1px solid #d9d9d9;border-radius:6px;padding:0 12px;font-size:14px;background:#fff;">
                    <option value=""><?php echo $lang->devws->pleaseSelect ?? '请选择';?></option>
                    <?php foreach($users as $account => $realname):?>
                    <option value="<?php echo $account;?>"><?php echo $realname;?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:500;margin-bottom:8px;"><?php echo $lang->task->estimate;?></label>
                <div style="position:relative;display:flex;align-items:center;">
                    <input type="number" name="estimate" step="0.01" min="0" value="0" style="width:100%;height:38px;border:1px solid #d9d9d9;border-radius:6px;padding:0 36px 0 12px;font-size:14px;box-sizing:border-box;">
                    <span style="position:absolute;right:12px;font-size:13px;color:#8c8c8c;pointer-events:none;">h</span>
                </div>
            </div>
            <div style="display:flex;gap:16px;margin-bottom:20px;">
                <div style="flex:1;">
                    <label style="display:block;font-size:13px;font-weight:500;margin-bottom:8px;"><?php echo $lang->task->estStarted;?></label>
                    <input type="date" name="estStarted" style="width:100%;height:38px;border:1px solid #d9d9d9;border-radius:6px;padding:0 12px;font-size:14px;box-sizing:border-box;">
                </div>
                <div style="flex:1;">
                    <label style="display:block;font-size:13px;font-weight:500;margin-bottom:8px;"><?php echo $lang->task->deadline;?></label>
                    <input type="date" name="deadline" style="width:100%;height:38px;border:1px solid #d9d9d9;border-radius:6px;padding:0 12px;font-size:14px;box-sizing:border-box;">
                </div>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:500;margin-bottom:8px;"><?php echo $lang->task->desc;?></label>
                <textarea name="desc" id="desc" style="display:none;"></textarea>
                <zen-editor name="desc" id="zenEditor" style="width:100%;height:200px;"></zen-editor>
            </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:10px;padding:16px 24px;border-top:1px solid #f0f0f0;">
            <button type="button" onclick="parent.closeDrawer && parent.closeDrawer()" style="padding:8px 24px;border-radius:6px;font-size:14px;cursor:pointer;border:1px solid #d9d9d9;background:#fff;color:#595959;"><?php echo $lang->cancel;?></button>
            <button type="submit" style="padding:8px 24px;border-radius:6px;font-size:14px;cursor:pointer;border:1px solid #1890ff;background:#1890ff;color:#fff;"><?php echo $lang->save;?></button>
        </div>
    </form>
</div>

<style>#header, #menu, #appsBar, .navbar, .breadcrumb, #footer { display: none !important; }</style>

<script src="/js/jquery/lib.js"></script>
<script type="module">
import '/js/zui3/zen-editor/zen-editor.esm.js';
console.log('zen-editor ESM loaded');
</script>

<script>
(function() {
    var cf = document.getElementById('createForm');
    if(cf) {
        cf.addEventListener('submit', function(e) {
            e.preventDefault();
            var ze = document.getElementById('zenEditor');
            var ta = document.getElementById('desc');

            var postUrl = cf.getAttribute('action');

            function doSubmit() {
                var btn = cf.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.textContent = '提交中...';
                var formData = new FormData(cf);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', postUrl);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.onload = function() {
                    var d = null;
                    try { d = JSON.parse(xhr.responseText); } catch(e) { d = null; }
                    if(d && d.result === 'success') {
                        parent.location.reload();
                    } else if(d) {
                        var m = d.message || '创建失败';
                        if(typeof m === 'object') { var a=[]; for(var k in m) a.push(m[k]); m=a.join('\n'); }
                        alert(m);
                        btn.disabled = false;
                        btn.textContent = '<?php echo $lang->save;?>';
                    } else {
                        alert('服务器返回格式错误: ' + (xhr.responseText || '空').substring(0, 300));
                        btn.disabled = false;
                        btn.textContent = '<?php echo $lang->save;?>';
                    }
                };
                xhr.onerror = function() { alert('网络错误'); btn.disabled = false; btn.textContent = '<?php echo $lang->save;?>'; };
                xhr.send(formData);
            }

            if(ze && typeof ze.getHTML === 'function') {
                ze.getHTML().then(function(html) {
                    ta.value = html;
                    console.log('zen-editor HTML:', html);
                    doSubmit();
                }).catch(function(err) {
                    console.error('getHTML error:', err);
                    doSubmit();
                });
            } else {
                doSubmit();
            }
        });
    }

    var execSel = document.getElementById('executionSelect');
    if(execSel) {
        execSel.addEventListener('change', function() {
            if(this.value) {
                window.location.href = '<?php echo helper::createLink('devws', 'create', 'executionID=%s');?>'.replace('%s', this.value);
            }
        });
    }
})();
</script>

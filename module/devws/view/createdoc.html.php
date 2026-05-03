<?php
/**
 * Custom create doc view for devws main content area.
 */
$postUrl = helper::createLink('devws', 'createDoc');
?>
<div class="create-container" style="padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;color:#333;max-width:700px;margin:0 auto;display:flex;flex-direction:column;height:100%;">

    <div class="create-heading" style="padding:16px 24px 12px;border-bottom:1px solid #f0f0f0;flex-shrink:0;">
        <div style="font-size:18px;font-weight:600;color:#1a1a2e;"><?php echo $lang->devws->createDoc;?></div>
    </div>

    <form method="post" id="createDocForm" action="<?php echo $postUrl;?>" style="flex:1;display:flex;flex-direction:column;min-height:0;">
        <div style="padding:16px 24px;flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:12px;">
            <!-- 访问控制放在最上面 -->
            <div style="flex-shrink:0;">
                <label style="display:block;font-size:13px;font-weight:500;margin-bottom:6px;"><?php echo $lang->doclib->control;?></label>
                <div style="display:flex;gap:20px;padding-top:2px;">
                    <label style="font-size:14px;cursor:pointer;display:flex;align-items:center;gap:6px;">
                        <input type="radio" name="acl" value="open" checked> <?php echo $lang->doc->aclList['open'] ?? '公开';?>
                    </label>
                    <label style="font-size:14px;cursor:pointer;display:flex;align-items:center;gap:6px;">
                        <input type="radio" name="acl" value="private"> <?php echo $lang->doc->aclList['private'] ?? '私有';?>
                    </label>
                </div>
            </div>

            <div style="flex-shrink:0;">
                <label style="display:block;font-size:13px;font-weight:500;margin-bottom:6px;"><?php echo $lang->doc->lib;?> <span style="color:#ff4d4f;">*</span></label>
                <select name="lib" id="libSelect" style="width:100%;height:36px;border:1px solid #d9d9d9;border-radius:6px;padding:0 12px;font-size:14px;background:#fff;">
                    <option value=""><?php echo $lang->devws->pleaseSelect ?? '请选择';?></option>
                    <?php foreach($libs as $id => $name):?>
                    <option value="<?php echo $id;?>" <?php echo ($id == $libID) ? 'selected' : '';?>><?php echo $name;?></option>
                    <?php endforeach;?>
                </select>
                <?php if(empty($libs)):?>
                <div style="margin-top:6px;font-size:12px;color:#999;">暂无可用文档库，请联系管理员创建。</div>
                <?php endif;?>
            </div>

            <div style="flex-shrink:0;">
                <label style="display:block;font-size:13px;font-weight:500;margin-bottom:6px;"><?php echo $lang->doc->title;?> <span style="color:#ff4d4f;">*</span></label>
                <input type="text" name="title" style="width:100%;height:36px;border:1px solid #d9d9d9;border-radius:6px;padding:0 12px;font-size:14px;box-sizing:border-box;" autocomplete="off">
            </div>

            <div style="flex-shrink:0;">
                <label style="display:block;font-size:13px;font-weight:500;margin-bottom:6px;"><?php echo $lang->doc->keywords;?></label>
                <input type="text" name="keywords" style="width:100%;height:36px;border:1px solid #d9d9d9;border-radius:6px;padding:0 12px;font-size:14px;box-sizing:border-box;" autocomplete="off" placeholder="<?php echo $lang->doc->keywordsTips ?? '关键词';?>">
            </div>

            <div style="flex:1;display:flex;flex-direction:column;min-height:200px;">
                <label style="display:block;font-size:13px;font-weight:500;margin-bottom:6px;flex-shrink:0;"><?php echo $lang->doc->content;?> <span style="color:#ff4d4f;">*</span></label>
                <textarea name="content" id="desc" style="display:none;"></textarea>
                <zen-editor name="content" id="zenEditor" style="width:100%;flex:1;min-height:200px;"></zen-editor>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;padding:12px 24px;border-top:1px solid #f0f0f0;flex-shrink:0;">
            <button type="button" onclick="parent.cancelCreateDoc && parent.cancelCreateDoc()" style="padding:8px 24px;border-radius:6px;font-size:14px;cursor:pointer;border:1px solid #d9d9d9;background:#fff;color:#595959;"><?php echo $lang->cancel;?></button>
            <button type="submit" style="padding:8px 24px;border-radius:6px;font-size:14px;cursor:pointer;border:1px solid #1890ff;background:#1890ff;color:#fff;"><?php echo $lang->save;?></button>
        </div>
    </form>
</div>

<style>#header, #menu, #appsBar, .navbar, .breadcrumb, #footer { display: none !important; }</style>

<script src="/js/jquery/lib.js"></script>
<script type="module">
import '/js/zui3/zen-editor/zen-editor.esm.js';
console.log('zen-editor ESM loaded for doc');
</script>

<script>
(function() {
    var cf = document.getElementById('createDocForm');
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
                        if(parent.sessionStorage) parent.sessionStorage.setItem('devws_section', 'doc');
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
})();
</script>

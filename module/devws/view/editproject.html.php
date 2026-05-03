<?php
/**
 * Edit project wrapper view for devws main content area.
 * Wraps the original ZenTao project edit form with devws styling
 * via CSS/JS injection into the inner iframe.
 */
?>
<?php if(!$project):?>
<div class="ep-error" style="padding:60px 20px;text-align:center;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;color:#333;">
    <div style="font-size:48px;color:#ff4d4f;margin-bottom:16px;">!</div>
    <div style="font-size:16px;color:#595959;margin-bottom:24px;">项目不存在</div>
    <a href="javascript:;" onclick="parent.cancelProjectView && parent.cancelProjectView()" style="display:inline-block;padding:8px 24px;border-radius:6px;font-size:14px;border:1px solid #d9d9d9;background:#fff;color:#595959;text-decoration:none;">返回项目列表</a>
</div>
<?php else:?>
<div style="display:flex;flex-direction:column;height:100%;background:#f5f7fa;">
    <iframe id="editProjectIframe" src="<?php echo $editUrl;?>" style="flex:1;width:100%;border:none;background:#f5f7fa;"></iframe>
</div>

<script>
var iframe = document.getElementById('editProjectIframe');
var redirectUrl = '<?php echo helper::createLink('devws', 'project', "projectID=$project->id");?>';

iframe.onload = function()
{
    var doc;
    try {
        doc = iframe.contentDocument || iframe.contentWindow.document;
    } catch(e) { return; }

    /* Inject devws-style CSS overrides into iframe <head> */
    var style = doc.createElement('style');
    style.id = 'devws-edit-style';
    style.textContent = [
        '#header,#menu,#appsBar,#mainNavbar,#mainMenu,#footer,',
        '.navbar,.breadcrumb,.page-actions,.zin-page-actions,',
        '.main-header,.table-header,.page-title,#sidebar,',
        '.main-navbar,.nav-tabs,.module-menu,#topnav,',
        '.nav-bar,.app-bar,#heading,.heading,.page-header,',
        '.pageNavbar,.nav-secondary,.zin-page-header,',
        '#mainContainer>.page-actions,#mainNavbar,.breadcrumb { display: none !important; }',

        'body {',
        '  font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif !important;',
        '  background: #f5f7fa !important;',
        '  color: #333 !important;',
        '  margin: 0 !important;',
        '  padding: 0 !important;',
        '}',
        '.panel-form,.panel,.form-grid-panel,form.panel-form {',
        '  background: #fff !important;',
        '  border-radius: 8px !important;',
        '  box-shadow: 0 1px 3px rgba(0,0,0,0.06) !important;',
        '  border: 1px solid #e8e8e8 !important;',
        '  padding: 24px 32px !important;',
        '  max-width: 960px !important;',
        '  margin: 20px auto !important;',
        '}',
        '.panel-heading,.form-panel-heading,.panel-header,',
        '.panel-title,h2,h3,.form-title {',
        '  font-size: 16px !important;',
        '  font-weight: 600 !important;',
        '  color: #1a1a2e !important;',
        '  border-bottom: 1px solid #e8e8e8 !important;',
        '  padding-bottom: 16px !important;',
        '  margin-bottom: 20px !important;',
        '  background: none !important;',
        '}',
        'label,.form-label,.form-group>label,.control-label {',
        '  font-size: 14px !important;',
        '  color: #595959 !important;',
        '  font-weight: 500 !important;',
        '  padding-top: 4px !important;',
        '}',
        'input[type="text"],input[type="date"],input[type="number"],',
        'select,textarea,.form-control,input[type="url"],input[type="email"] {',
        '  border-radius: 6px !important;',
        '  border: 1px solid #d9d9d9 !important;',
        '  padding: 6px 12px !important;',
        '  font-size: 14px !important;',
        '  color: #333 !important;',
        '  background: #fff !important;',
        '  box-shadow: none !important;',
        '  transition: border-color .2s,box-shadow .2s !important;',
        '}',
        'input:focus,select:focus,textarea:focus,.form-control:focus {',
        '  border-color: #1890ff !important;',
        '  box-shadow: 0 0 0 2px rgba(24,144,255,0.2) !important;',
        '  outline: none !important;',
        '}',
        '.btn-primary {',
        '  background: #1890ff !important;',
        '  border-color: #1890ff !important;',
        '  border-radius: 6px !important;',
        '  color: #fff !important;',
        '  font-size: 14px !important;',
        '  padding: 6px 20px !important;',
        '  border: 1px solid transparent !important;',
        '  cursor: pointer !important;',
        '  font-weight: 500 !important;',
        '}',
        '.btn-primary:hover { background: #40a9ff !important; border-color: #40a9ff !important; }',
        '.btn {',
        '  border-radius: 6px !important;',
        '  font-size: 14px !important;',
        '  padding: 6px 16px !important;',
        '  cursor: pointer !important;',
        '  font-weight: 500 !important;',
        '}',
        '.btn-secondary,.btn-default {',
        '  background: #fff !important;',
        '  border: 1px solid #d9d9d9 !important;',
        '  color: #595959 !important;',
        '}',
        '.form-actions,.form-group.no-label,.panel-footer {',
        '  border-top: 1px solid #e8e8e8 !important;',
        '  padding-top: 16px !important;',
        '  margin-top: 20px !important;',
        '  background: none !important;',
        '}',
        'input[type="checkbox"],input[type="radio"] { accent-color: #1890ff !important; }',
        '.help-text,.text-muted,.form-text,.help { color: #999 !important; font-size: 12px !important; }',
        '.required:after,.form-required:after { color: #ff4d4f !important; }',
        '.alert,.alert-danger,.alert-warning { border-radius: 6px !important; border: 1px solid transparent !important; }',
        '.alert-danger { background: #fff2f0 !important; border-color: #ffccc7 !important; color: #cf1322 !important; }',
    ].join('\n');
    doc.head.appendChild(style);

    /* Inject JS: brute-force hide nav + redirect on save */
    var script = doc.createElement('script');
    script.textContent = '(function(){if(window._dew)return;window._dew=true;' +
    'var S=["#mainNavbar","#header","#menu","#mainMenu","#footer",' +
    '".navbar",".breadcrumb",".page-actions",".zin-page-actions",' +
    '".page-title","#heading",".heading",".page-header","#sidebar",' +
    '".main-navbar",".nav-tabs",".module-menu","#appsBar","#topnav"];' +
    'function K(){S.forEach(function(s){document.querySelectorAll(s).forEach(function(e){' +
    'e.style.setProperty("display","none","important")})})}K();' +
    'setInterval(K,200);' +
    'try{if(typeof zui!="undefined"&&zui.Modal&&zui.Modal.hide){' +
    'var _z=zui.Modal.hide;zui.Modal.hide=function(){' +
    'window._r=true;try{window.parent.location.href="' + redirectUrl + '"}catch(e){}' +
    'return _z?_z.apply(this,arguments):undefined};' +
    'window.beforePageLoad=function(o){return window._r?false:o}}' +
    '}catch(e){setTimeout(arguments.callee,500)}})()';
    doc.head.appendChild(script);
};
</script>

<style>
body { margin: 0; padding: 0; background: #f5f7fa !important; }
</style>
<?php endif;?>

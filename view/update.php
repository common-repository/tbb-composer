<div class="wrap">

        <h2><?php _e('Update Project',TBB_COMPOSER_SLUG) ?></h2>
		<?php echo $messages ?>
		<h4><?php _e('Configuration',TBB_COMPOSER_SLUG) ?></h4>
		<form id="" method="post">
		
		<div id="editor" style="width: 600px; height: 400px;border: 1px #aaa solid;"><?php echo $json ?></div>
		<textarea name="composer" style="display: none;"><?php echo $json ?></textarea> 
		<input type="hidden" name="action" value="update" autofocus="autofocus">

		<script>
		    var editor = ace.edit("editor");
			var textarea = jQuery('textarea[name="composer"]');
		    editor.setTheme("ace/theme/textmate");
		    editor.getSession().setMode("ace/mode/json");
			editor.on("input", function () {
			    textarea.val(editor.getSession().getValue());
			});
		</script>
		<p><input name="update" type="submit" class="button button-primary button-large" id="update" accesskey="u" value="<?php _e('Update',TBB_COMPOSER_SLUG) ?>"></p>
		</form>
    </div>
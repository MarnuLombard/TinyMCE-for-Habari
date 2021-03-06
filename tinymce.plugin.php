<?php

class TinyMCE extends Plugin {

	/**
	 * Required Plugin Information
	 **/
	/*function info()
	{
		return array(
			'name' => 'TinyMCE',
			'license' => 'Apache License 2.0',
			'url' => 'http://twofishcreative.com/TinyMCE/',
			'author' => 'Michael C. Harris',
			'authorurl' => 'http://twofishcreative.com/michael/blog/',
			'version' => '0.6-0.4',
			'description' => 'Publish posts using the TinyMCE editor.',
			'copyright' => '2008'
		);
	}*/

	/**
	 * Respond to the submitted configure form
	 * @param FormUI $form The form that was submitted
	 * @return boolean Whether to save the returned values.
	 **/
	public function updated_config($form)
	{
		// set the options
		$editor = $form->controls['editor']->value;
		$options = array();
		$options[] = 'mode: "textareas"';
		if ( $editor == null || $editor == 'simple') {
			$options[] = 'theme: "simple"';
		}
		elseif ( $editor == 'advanced' ) {
			$options[] = 'theme: "advanced"';
		}
		elseif ( $editor == 'resizable' ) {
			$options[] = 'theme: "advanced"';
			// Add extra configuration options
			$options[] = 'theme_advanced_statusbar_location : "bottom",
								theme_advanced_resize_horizontal : false,
								theme_advanced_resizing : true';
		}
		Options::set(strtolower(get_class($this)) . ':options_' . User::identify()->id, implode($options, ','));
		// No need to save input values
		return false;
	}

	/**
	 * Add actions to the plugin page for this plugin
	 * @param array $actions An array of actions that apply to this plugin
	 * @param string $plugin_id The string id of a plugin, generated by the system
	 * @return array The array of actions to attach to the specified $plugin_id
	 **/
	public function filter_plugin_config($actions, $plugin_id)
	{
		if ( $plugin_id == $this->plugin_id() ) {
			$actions[] = 'Configure';
		}

		return $actions;
	}

	/**
	 * Respond to the user selecting an action on the plugin page
	 * @param string $plugin_id The string id of the acted-upon plugin
	 * @param string $action The action string supplied via the filter_plugin_config hook
	 **/
	public function action_plugin_ui($plugin_id, $action)
	{
		if ( $plugin_id == $this->plugin_id() ) {
			switch ( $action ) {
				case 'Configure' :
					// Add extra configuration options
					$form = new FormUI(strtolower(get_class($this)));
					$form->append('select', 'editor', 'null', _t('Editor theme:'));
					$form->editor->options = array(
																		'modern' => 'Modern Theme'
																	 );
					$form->append( 'submit', 'save', _t( 'Save' ) );
					$form->on_success(array($this, 'updated_config'));
					$form->out();
					break;
			}
		}
	}

	public function action_admin_header($theme)
	{
		if ( $theme->page == 'publish' ) {
			Stack::add(
				'admin_header_javascript',// where to add
				$this->get_url() . '/tinymce/tiny_mce.js', // what to add
				'tinymce_habari',// what it's called
				'jquery' // what it depends upon
				);
		}
	}

	/*public function action_admin_header($theme)
	{
		if ( $theme->page == 'publish' ) {
			Stack::add('admin_header_javascript', $this->get_url() . '/tiny_mce/tiny_mce.js');
		}
	}*/

	public function action_admin_footer($theme)
	{
		if ( $theme->page == 'publish' ) {
			// This is how you fetch the options submitted by action_plugin_ui
			//$options = Options::get(strtolower(get_class($this) . ':options_' . User::identify()->id));

			// but we'll set up options manually for now -- while in dev
				/*$options = '
					selector: "#content",
			    theme: "modern",
			    plugins: "autolink lists link print preview wordcount code nonbreaking save table directionality paste",
			    toolbar1: "undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link",
			    toolbar2: "print preview"
					';*/

					$options = "
					selector: '#content',
			    mode : 'textareas',
					theme : 'ribbon',
					plugins : 'tabfocus,advimagescale,loremipsum,image_tools,embed,tableextras,style,table,inlinepopups,searchreplace,contextmenu,paste,wordcount,advlist,autosave',
					inlinepopups_skin : 'ribbon_popup'
					";
			echo <<<TINYMCE
			<script type="text/javascript">
			tinyMCE.init({
				{$options}
			});

			habari.editor = {
				insertSelection: function(value) {
					tinyMCE.activeEditor.selection.setContent(tinyMCE.activeEditor.selection.getContent() + value);
				}
			}
			</script>
TINYMCE;
		}
	}

	/*public function action_update_check()
	{
		Update::add( 'TinyMCE', 'f729425d-21d2-4760-b20f-c3904c484603',  $this->info->version );
	}*/
}

?>

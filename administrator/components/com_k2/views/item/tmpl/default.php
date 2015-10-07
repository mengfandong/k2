<?php
/**
 * @version		2.7.x
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2014 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;

$document = JFactory::getDocument();
$document->addScriptDeclaration("
	Joomla.submitbutton = function(pressbutton){
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		if (\$K2.trim(\$K2('#title').val()) == '') {
			alert( '".JText::_('K2_ITEM_MUST_HAVE_A_TITLE', true)."' );
		}
		else if (\$K2.trim(\$K2('#catid').val()) == '0') {
			alert( '".JText::_('K2_PLEASE_SELECT_A_CATEGORY', true)."' );
		}
		else {
			syncExtraFieldsEditor();
			var validation = validateExtraFields();
			if(validation === true) {
				\$K2('#selectedTags option').attr('selected', 'selected');
				submitform( pressbutton );
			}
		}
	};
");

?>

<form action="index.php" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm">

	<?php if($this->mainframe->isSite()): ?>
	<!-- Frontend Editing -->
	<div id="k2FrontendContainer">
		<div id="k2Frontend">

			<table class="k2FrontendToolbar" cellpadding="2" cellspacing="4">
				<tr>
					<td id="toolbar-save" class="button">
						<a class="toolbar" href="#" onclick="Joomla.submitbutton('save'); return false;"> <span title="<?php echo JText::_('K2_SAVE'); ?>" class="icon-32-save icon-save"></span> <?php echo JText::_('K2_SAVE'); ?> </a>
					</td>
					<td id="toolbar-cancel" class="button">
						<a class="toolbar" href="#"> <span title="<?php echo JText::_('K2_CANCEL'); ?>" class="icon-32-cancel icon-cancel"></span> <?php echo JText::_('K2_CLOSE'); ?> </a>
					</td>
				</tr>
			</table>
			<div id="k2FrontendEditToolbar">
				<h2 class="header icon-48-k2">
					<?php echo (JRequest::getInt('cid')) ? JText::_('K2_EDIT_ITEM') : JText::_('K2_ADD_ITEM'); ?>
				</h2>
			</div>
			<div class="clr"></div>
			<hr class="sep" />
			<?php if(!$this->permissions->get('publish')): ?>
			<div id="k2FrontendPermissionsNotice">
				<p><?php echo JText::_('K2_FRONTEND_PERMISSIONS_NOTICE'); ?></p>
			</div>
			<?php endif; ?>

	<?php endif; ?>



	<!-- Top Nav Tabs START here -->
	<div id="k2FormTopNav" class="k2Tabs">

		<?php if($this->row->id): ?>
		<div id="k2ID"><strong><?php echo JText::_('K2_ID'); ?></strong> <?php echo $this->row->id; ?></div>
		<?php endif; ?>

		<ul class="k2NavTabs">
			<li id="tabContent"><a href="#k2TabBasic"><i class="fa fa-home"></i><?php echo JText::_('K2_BASIC'); ?></a></li>
			<li id="tabContent"><a href="#k2TabPubAndMeta"><i class="fa fa-info-circle"></i><?php echo JText::_('K2_PUBLISHING_AND_METADATA'); ?></a></li>
			<?php if($this->mainframe->isAdmin()): ?>
			<li id="tabContent"><a href="#k2TabDisplaySet"><i class="fa fa-desktop"></i><?php echo JText::_('K2_DISPLAY_SETTINGS'); ?></a></li>
			<?php endif; ?>
		</ul>

		<!-- Top Nav Tabs content -->
		<div class="k2NavTabContent" id="k2TabBasic">

			<div class="k2Table">
				<div class="k2TableLabel">
					<label for="title"><?php echo JText::_('K2_TITLE'); ?></label>
				</div>
				<div class="k2TableValue">
					<input class="text_area k2TitleBox" type="text" name="title" id="title" maxlength="250" value="<?php echo $this->row->title; ?>" />
				</div>

				<div class="k2TableLabel">
					<label for="alias"><?php echo JText::_('K2_TITLE_ALIAS'); ?></label>
				</div>
				<div class="k2TableValue">
					<input class="text_area k2TitleAliasBox" type="text" name="alias" id="alias" maxlength="250" value="<?php echo $this->row->alias; ?>" />
				</div>

				<div class="k2TableLabel">
					<label><?php echo JText::_('K2_CATEGORY'); ?></label>
				</div>
				<div class="k2TableValue">
					<?php echo $this->lists['categories']; ?>

					<?php if($this->mainframe->isAdmin() || ($this->mainframe->isSite() && $this->permissions->get('publish'))): ?>
					<div class="k2SubTable k2TableRight">
						<div class="k2SubTableLabel">
							<label for="featured"><?php echo JText::_('K2_IS_IT_FEATURED'); ?></label>
						</div>
						<div class="k2SubTableValue">
							<?php echo $this->lists['featured']; ?>
						</div>

						<div class="k2SubTableLabel">
							<label><?php echo JText::_('K2_PUBLISHED'); ?></label>
						</div>
						<div class="k2SubTableValue">
							<?php echo $this->lists['published']; ?>
						</div>
					</div>
					<?php endif; ?>
				</div>

				<div class="k2TableLabel">
					<label for="tags"><?php echo JText::_('K2_TAGS'); ?></label>
				</div>
				<div class="k2TableValue">
					<?php if($this->params->get('taggingSystem')): ?>
					<!-- Free tagging -->
					<ul class="tags">
						<?php if(isset($this->row->tags) && count($this->row->tags)): ?>
						<?php foreach($this->row->tags as $tag): ?>
						<li class="tagAdded">
							<?php echo $tag->name; ?>
							<span title="<?php echo JText::_('K2_CLICK_TO_REMOVE_TAG'); ?>" class="tagRemove">&times;</span>
							<input type="hidden" name="tags[]" value="<?php echo $tag->name; ?>" />
						</li>
						<?php endforeach; ?>
						<?php endif; ?>
						<li class="tagAdd">
							<input type="text" id="search-field" />
						</li>
						<li class="clr"></li>
					</ul>
					<p class="k2TagsNotice">
						<?php echo JText::_('K2_WRITE_A_TAG_AND_PRESS_RETURN_OR_COMMA_TO_ADD_IT'); ?>
					</p>
					<?php else: ?>
					<!-- Selection based tagging -->
					<?php if( !$this->params->get('lockTags') || $this->user->gid>23): ?>
					<div style="float:left;">
						<input type="text" name="tag" id="tag" />
						<input type="button" id="newTagButton" value="<?php echo JText::_('K2_ADD'); ?>" />
					</div>
					<div id="tagsLog"></div>
					<div class="clr"></div>
					<span class="k2Note">
						<?php echo JText::_('K2_WRITE_A_TAG_AND_PRESS_ADD_TO_INSERT_IT_TO_THE_AVAILABLE_TAGS_LISTNEW_TAGS_ARE_APPENDED_AT_THE_BOTTOM_OF_THE_AVAILABLE_TAGS_LIST_LEFT'); ?>
					</span>
					<?php endif; ?>
					<table cellspacing="0" cellpadding="0" border="0" id="tagLists">
						<tr>
							<td id="tagListsLeft">
								<span><?php echo JText::_('K2_AVAILABLE_TAGS'); ?></span> <?php echo $this->lists['tags'];	?>
							</td>
							<td id="tagListsButtons">
								<input type="button" id="addTagButton" value="<?php echo JText::_('K2_ADD'); ?> &raquo;" />
								<br />
								<br />
								<input type="button" id="removeTagButton" value="&laquo; <?php echo JText::_('K2_REMOVE'); ?>" />
							</td>
							<td id="tagListsRight">
								<span><?php echo JText::_('K2_SELECTED_TAGS'); ?></span> <?php echo $this->lists['selectedTags']; ?>
							</td>
						</tr>
					</table>
					<?php endif; ?>
				</div>

				<div class="k2TableLabel">
					<label><?php echo JText::_('K2_AUTHOR'); ?></label>
				</div>
				<div class="k2TableValue">
					<div class="k2SubTable k2TableInline">
						<div class="k2SubTableValue">
							<span id="k2Author"><?php echo $this->row->author; ?></span>
							<?php if($this->mainframe->isAdmin() || ($this->mainframe->isSite() && $this->permissions->get('editAll'))): ?>
							<a class="modal k2Selector" rel="{handler:'iframe', size: {x: 800, y: 460}}" href="index.php?option=com_k2&amp;view=users&amp;task=element&amp;tmpl=component">
								<i class="fa fa-pencil"></i>
							</a>
							<input type="hidden" name="created_by" value="<?php echo $this->row->created_by; ?>" />
							<?php endif; ?>
						</div>

						<div class="k2SubTableLabel">
							<label><?php echo JText::_('K2_AUTHOR_ALIAS'); ?></label>
						</div>
						<div class="k2SubTableValue">
							<input class="text_area" type="text" name="created_by_alias" maxlength="250" value="<?php echo $this->row->created_by_alias; ?>" />
						</div>
					</div>

					<div class="k2SubTable k2TableRight">
						<div class="k2SubTableLabel">
							<label><?php echo JText::_('K2_ACCESS_LEVEL'); ?></label>
						</div>
						<div class="k2SubTableValue">
							<?php echo $this->lists['access']; ?>
						</div>
						<?php if(isset($this->lists['language'])): ?>
						<div class="k2SubTableLabel">
							<label><?php echo JText::_('K2_LANGUAGE'); ?></label>
						</div>
						<div class="k2SubTableValue">
							<?php echo $this->lists['language']; ?>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<!-- Required extra field warning -->
			<div id="k2ExtraFieldsValidationResults">
				<h3><?php echo JText::_('K2_THE_FOLLOWING_FIELDS_ARE_REQUIRED'); ?></h3>
				<ul id="k2ExtraFieldsMissing">
					<li><?php echo JText::_('K2_LOADING'); ?></li>
				</ul>
			</div>

			<!-- Tabs start here -->
			<div class="k2Tabs" id="k2Tabs">
				<ul class="k2TabsNavigation">
					<li id="tabContent"><a href="#k2TabContent"><i class="fa fa-file-text-o"></i><?php echo JText::_('K2_CONTENT'); ?></a></li>
					<?php if ($this->params->get('showImageTab')): ?>
					<li id="tabImage"><a href="#k2TabImage"><i class="fa fa-camera"></i><?php echo JText::_('K2_IMAGE'); ?></a></li>
					<?php endif; ?>
					<?php if ($this->params->get('showImageGalleryTab')): ?>
					<li id="tabImageGallery"><a href="#k2TabImageGallery"><i class="fa fa-file-image-o"></i><?php echo JText::_('K2_IMAGE_GALLERY'); ?></a></li>
					<?php endif; ?>
					<?php if ($this->params->get('showVideoTab')): ?>
					<li id="tabVideo"><a href="#k2TabMedia"><i class="fa fa-file-video-o"></i><?php echo JText::_('K2_MEDIA'); ?></a></li>
					<?php endif; ?>
					<?php if ($this->params->get('showExtraFieldsTab')): ?>
					<li id="tabExtraFields"><a href="#k2TabExtraFields"><i class="fa fa-gear"></i><?php echo JText::_('K2_EXTRA_FIELDS'); ?></a></li>
					<?php endif; ?>
					<?php if ($this->params->get('showAttachmentsTab')): ?>
					<li id="tabAttachments"><a href="#k2TabAttachments"><i class="fa fa-file-o"></i><?php echo JText::_('K2_ATTACHMENTS'); ?></a></li>
					<?php endif; ?>
					<?php if(count(array_filter($this->K2PluginsItemOther)) && $this->params->get('showK2Plugins')): ?>
					<li id="tabPlugins"><a href="#k2TabPlugins"><i class="fa fa-wrench"></i><?php echo JText::_('K2_PLUGINS'); ?></a></li>
					<?php endif; ?>
				</ul>

				<!-- Tab content -->
				<div class="k2TabsContent" id="k2TabContent">
					<?php if($this->params->get('mergeEditors')): ?>
					<div class="k2ItemFormEditor">
						<?php echo $this->text; ?>
						<div class="dummyHeight"></div>
						<div class="clr"></div>
					</div>
					<?php else: ?>
					<div class="k2ItemFormEditor">
						<span class="k2ItemFormEditorTitle"><?php echo JText::_('K2_INTROTEXT_TEASER_CONTENTEXCERPT'); ?></span>
						<?php echo $this->introtext; ?>
						<div class="dummyHeight"></div>
						<div class="clr"></div>
					</div>
					<div class="k2ItemFormEditor">
						<span class="k2ItemFormEditorTitle"><?php echo JText::_('K2_FULLTEXT_MAIN_CONTENT'); ?></span>
						<?php echo $this->fulltext; ?>
						<div class="dummyHeight"></div>
						<div class="clr"></div>
					</div>
					<?php endif; ?>
					<?php if (count($this->K2PluginsItemContent)): ?>
					<div class="itemPlugins">
						<?php foreach($this->K2PluginsItemContent as $K2Plugin): ?>
						<?php if(!is_null($K2Plugin)): ?>
						<fieldset>
							<legend><?php echo $K2Plugin->name; ?></legend>
							<?php echo $K2Plugin->fields; ?>
						</fieldset>
						<?php endif; ?>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
					<div class="clr"></div>
				</div>
				<?php if ($this->params->get('showImageTab')): ?>
				<!-- Tab image -->
				<div class="k2TabsContent" id="k2TabImage">
					<table class="admintable table">
						<tr>
							<td align="right" class="key">
								<?php echo JText::_('K2_ITEM_IMAGE'); ?>
							</td>
							<td>
								<input type="file" name="image" class="fileUpload" />
								<i>(<?php echo JText::_('K2_MAX_UPLOAD_SIZE'); ?>: <?php echo ini_get('upload_max_filesize'); ?>)</i>
								<br />
								<br />
								<?php echo JText::_('K2_OR'); ?>
								<br />
								<br />
								<input type="text" name="existingImage" id="existingImageValue" class="text_area" readonly />
								<input type="button" value="<?php echo JText::_('K2_BROWSE_SERVER'); ?>" id="k2ImageBrowseServer"  />
								<br />
								<br />
							</td>
						</tr>
						<tr>
							<td align="right" class="key">
								<?php echo JText::_('K2_ITEM_IMAGE_CAPTION'); ?>
							</td>
							<td>
								<input type="text" name="image_caption" size="30" class="text_area" value="<?php echo $this->row->image_caption; ?>" />
							</td>
						</tr>
						<tr>
							<td align="right" class="key">
								<?php echo JText::_('K2_ITEM_IMAGE_CREDITS'); ?>
							</td>
							<td>
								<input type="text" name="image_credits" size="30" class="text_area" value="<?php echo $this->row->image_credits; ?>" />
							</td>
						</tr>
						<?php if (!empty($this->row->image)): ?>
						<tr>
							<td align="right" class="key">
								<?php echo JText::_('K2_ITEM_IMAGE_PREVIEW'); ?>
							</td>
							<td>
								<a class="modal" rel="{handler: 'image'}" href="<?php echo $this->row->image; ?>" title="<?php echo JText::_('K2_CLICK_ON_IMAGE_TO_PREVIEW_IN_ORIGINAL_SIZE'); ?>">
									<img alt="<?php echo $this->row->title; ?>" src="<?php echo $this->row->thumb; ?>" class="k2AdminImage" />
								</a>
								<input type="checkbox" name="del_image" id="del_image" />
								<label for="del_image"><?php echo JText::_('K2_CHECK_THIS_BOX_TO_DELETE_CURRENT_IMAGE_OR_JUST_UPLOAD_A_NEW_IMAGE_TO_REPLACE_THE_EXISTING_ONE'); ?></label>
							</td>
						</tr>
						<?php endif; ?>
					</table>
					<?php if (count($this->K2PluginsItemImage)): ?>
					<div class="itemPlugins">
						<?php foreach($this->K2PluginsItemImage as $K2Plugin): ?>
						<?php if(!is_null($K2Plugin)): ?>
						<fieldset>
							<legend><?php echo $K2Plugin->name; ?></legend>
							<?php echo $K2Plugin->fields; ?>
						</fieldset>
						<?php endif; ?>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>


				<?php if ($this->params->get('showImageGalleryTab')): ?>
				<!-- Tab image gallery -->
				<div class="k2TabsContent" id="k2TabImageGallery">
					<?php if ($this->lists['checkSIG']): ?>
					<table class="admintable table" id="item_gallery_content">
						<tr>
							<td align="right" valign="top" class="key">
								<?php echo JText::_('K2_COM_BE_ITEM_ITEM_IMAGE_GALLERY'); ?>
							</td>
							<td valign="top">
								<?php if($this->sigPro): ?>
								<a class="modal" rel="{handler: 'iframe', size: {x: 940, y: 560}}" href="index.php?option=com_sigpro&view=galleries&task=create&newFolder=<?php echo $this->sigProFolder; ?>&type=k2&tmpl=component"><?php echo JText::_('K2_COM_BE_ITEM_SIGPRO_UPLOAD'); ?></a> <i>(<?php echo JText::_('K2_COM_BE_ITEM_SIGPRO_UPLOAD_NOTE'); ?>)</i>
								<input name="sigProFolder" type="hidden" value="<?php echo $this->sigProFolder; ?>" />
								<br />
								<br />
								<?php echo JText::_('K2_OR'); ?>
								<?php endif; ?>
								<?php echo JText::_('K2_UPLOAD_A_ZIP_FILE_WITH_IMAGES'); ?> <input type="file" name="gallery" class="fileUpload" /> <span class="hasTip k2GalleryNotice" title="<?php echo JText::_('K2_UPLOAD_A_ZIP_FILE_HELP_HEADER'); ?>::<?php echo JText::_('K2_UPLOAD_A_ZIP_FILE_HELP_TEXT'); ?>"><?php echo JText::_('K2_UPLOAD_A_ZIP_FILE_HELP'); ?></span> <i>(<?php echo JText::_('K2_MAX_UPLOAD_SIZE'); ?>: <?php echo ini_get('upload_max_filesize'); ?>)</i>
								<br />
								<br />
								<?php echo JText::_('K2_OR_ENTER_A_FLICKR_SET_URL'); ?><?php echo JText::_('K2_OR_ENTER_A_FLICKR_SET_URL'); ?>
								<input type="text" name="flickrGallery" size="50" value="<?php echo ($this->row->galleryType == 'flickr') ? $this->row->galleryValue : ''; ?>" /> <span class="hasTip k2GalleryNotice" title="<?php echo JText::_('K2_VALID_FLICK_API_KEY_HELP_HEADER'); ?>::<?php echo JText::_('K2_VALID_FLICK_API_KEY_HELP_TEXT'); ?>"><?php echo JText::_('K2_UPLOAD_A_ZIP_FILE_HELP'); ?></span>

								<?php if (!empty($this->row->gallery)): ?>
								<!-- Preview -->
								<div id="itemGallery">
									<?php echo $this->row->gallery; ?>
									<br />
									<input type="checkbox" name="del_gallery" id="del_gallery" />
									<label for="del_gallery"><?php echo JText::_('K2_CHECK_THIS_BOX_TO_DELETE_CURRENT_IMAGE_GALLERY_OR_JUST_UPLOAD_A_NEW_IMAGE_GALLERY_TO_REPLACE_THE_EXISTING_ONE'); ?></label>
								</div>
								<?php endif; ?>
							</td>
						</tr>
					</table>
					<?php else: ?>
						<?php if (K2_JVERSION == '15'): ?>
						<dl id="system-message">
							<dt class="notice"><?php echo JText::_('K2_NOTICE'); ?></dt>
							<dd class="notice message fade">
								<ul>
									<li><?php echo JText::_('K2_NOTICE_PLEASE_INSTALL_JOOMLAWORKS_SIMPLE_IMAGE_GALLERY_PRO_PLUGIN_IF_YOU_WANT_TO_USE_THE_IMAGE_GALLERY_FEATURES_OF_K2'); ?></li>
								</ul>
							</dd>
						</dl>
						<?php elseif(K2_JVERSION == '25'): ?>
						<div id="system-message-container">
							<dl id="system-message">
								<dt class="notice"><?php echo JText::_('K2_NOTICE'); ?></dt>
								<dd class="notice message">
									<ul>
										<li><?php echo JText::_('K2_NOTICE_PLEASE_INSTALL_JOOMLAWORKS_SIMPLE_IMAGE_GALLERY_PRO_PLUGIN_IF_YOU_WANT_TO_USE_THE_IMAGE_GALLERY_FEATURES_OF_K2'); ?></li>
									</ul>
								</dd>
							</dl>
						</div>
						<?php else: ?>
						<div class="alert">
							<h4 class="alert-heading"><?php echo JText::_('K2_NOTICE'); ?></h4>
							<div><p><?php echo JText::_('K2_NOTICE_PLEASE_INSTALL_JOOMLAWORKS_SIMPLE_IMAGE_GALLERY_PRO_PLUGIN_IF_YOU_WANT_TO_USE_THE_IMAGE_GALLERY_FEATURES_OF_K2'); ?></p></div>
						</div>
						<?php endif; ?>
					<?php endif; ?>
					<?php if (count($this->K2PluginsItemGallery)): ?>
					<div class="itemPlugins">
						<?php foreach($this->K2PluginsItemGallery as $K2Plugin): ?>
						<?php if(!is_null($K2Plugin)): ?>
						<fieldset>
							<legend><?php echo $K2Plugin->name; ?></legend>
							<?php echo $K2Plugin->fields; ?>
						</fieldset>
						<?php endif; ?>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<?php if ($this->params->get('showVideoTab')): ?>
				<!-- Tab video -->
				<div class="k2TabsContent" id="k2TabMedia">
					<?php if ($this->lists['checkAllVideos']): ?>
					<table class="admintable table" id="item_video_content">
						<tr>
							<td align="right" class="key">
								<?php echo JText::_('K2_MEDIA_SOURCE'); ?>
							</td>
							<td>
								<div id="k2MediaTabs" class="k2Tabs">
									<ul class="k2TabsNavigation">
										<li><a href="#k2MediaTab1"><?php echo JText::_('K2_UPLOAD'); ?></a></li>
										<li><a href="#k2MediaTab2"><?php echo JText::_('K2_BROWSE_SERVERUSE_REMOTE_MEDIA'); ?></a></li>
										<li><a href="#k2MediaTab3"><?php echo JText::_('K2_MEDIA_USE_ONLINE_VIDEO_SERVICE'); ?></a></li>
										<li><a href="#k2MediaTab4"><?php echo JText::_('K2_EMBED'); ?></a></li>
									</ul>
									<div id="k2MediaTab1" class="k2TabsContent">
										<div class="panel" id="Upload_video">
											<input type="file" name="video" class="fileUpload" />
											<i>(<?php echo JText::_('K2_MAX_UPLOAD_SIZE'); ?>: <?php echo ini_get('upload_max_filesize'); ?>)</i></div>
									</div>
									<div id="k2MediaTab2" class="k2TabsContent">
										<div class="panel" id="Remote_video"> <a id="k2MediaBrowseServer" href="index.php?option=com_k2&view=media&type=video&tmpl=component&fieldID=remoteVideo"><?php echo JText::_('K2_BROWSE_VIDEOS_ON_SERVER')?></a> <?php echo JText::_('K2_OR'); ?> <?php echo JText::_('K2_PASTE_REMOTE_VIDEO_URL'); ?>
											<br />
											<br />
											<input type="text" size="50" name="remoteVideo" id="remoteVideo" value="<?php echo $this->lists['remoteVideo'] ?>" />
										</div>
									</div>
									<div id="k2MediaTab3" class="k2TabsContent">
										<div class="panel" id="Video_from_provider"> <?php echo JText::_('K2_SELECT_VIDEO_PROVIDER'); ?> <?php echo $this->lists['providers']; ?> <br/><br/> <?php echo JText::_('K2_AND_ENTER_VIDEO_ID'); ?>
											<input type="text" size="50" name="videoID" value="<?php echo $this->lists['providerVideo'] ?>" />
											<br />
											<br />
											<a class="modal" rel="{handler: 'iframe', size: {x: 990, y: 600}}" href="http://www.joomlaworks.net/allvideos-documentation"><?php echo JText::_('K2_READ_THE_ALLVIDEOS_DOCUMENTATION_FOR_MORE'); ?></a> </div>
									</div>
									<div id="k2MediaTab4" class="k2TabsContent">
										<div class="panel" id="embedVideo">
											<?php echo JText::_('K2_PASTE_HTML_EMBED_CODE_BELOW'); ?>
											<br />
											<textarea name="embedVideo" rows="5" cols="50" class="textarea"><?php echo $this->lists['embedVideo']; ?></textarea>
										</div>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td align="right" class="key">
								<?php echo JText::_('K2_MEDIA_CAPTION'); ?>
							</td>
							<td>
								<input type="text" name="video_caption" size="50" class="text_area" value="<?php echo $this->row->video_caption; ?>" />
							</td>
						</tr>
						<tr>
							<td align="right" class="key">
								<?php echo JText::_('K2_MEDIA_CREDITS'); ?>
							</td>
							<td>
								<input type="text" name="video_credits" size="50" class="text_area" value="<?php echo $this->row->video_credits; ?>" />
							</td>
						</tr>
						<?php if($this->row->video): ?>
						<tr>
							<td align="right" class="key">
								<?php echo JText::_('K2_MEDIA_PREVIEW'); ?>
							</td>
							<td>
								<?php echo $this->row->video; ?>
								<br />
								<input type="checkbox" name="del_video" id="del_video" />
								<label for="del_video"><?php echo JText::_('K2_CHECK_THIS_BOX_TO_DELETE_CURRENT_VIDEO_OR_USE_THE_FORM_ABOVE_TO_REPLACE_THE_EXISTING_ONE'); ?></label>
							</td>
						</tr>
						<?php endif; ?>
					</table>
					<?php else: ?>
						<?php if (K2_JVERSION == '15'): ?>
						<dl id="system-message">
							<dt class="notice"><?php echo JText::_('K2_NOTICE'); ?></dt>
							<dd class="notice message fade">
								<ul>
									<li><?php echo JText::_('K2_NOTICE_PLEASE_INSTALL_JOOMLAWORKS_ALLVIDEOS_PLUGIN_IF_YOU_WANT_TO_USE_THE_FULL_VIDEO_FEATURES_OF_K2'); ?></li>
								</ul>
							</dd>
						</dl>
						<?php elseif(K2_JVERSION == '25'): ?>
						<div id="system-message-container">
							<dl id="system-message">
								<dt class="notice"><?php echo JText::_('K2_NOTICE'); ?></dt>
								<dd class="notice message">
									<ul>
										<li><?php echo JText::_('K2_NOTICE_PLEASE_INSTALL_JOOMLAWORKS_ALLVIDEOS_PLUGIN_IF_YOU_WANT_TO_USE_THE_FULL_VIDEO_FEATURES_OF_K2'); ?></li>
									</ul>
								</dd>
							</dl>
						</div>
						<?php else: ?>
						<div class="alert">
							<h4 class="alert-heading"><?php echo JText::_('K2_NOTICE'); ?></h4>
							<div><p><?php echo JText::_('K2_NOTICE_PLEASE_INSTALL_JOOMLAWORKS_ALLVIDEOS_PLUGIN_IF_YOU_WANT_TO_USE_THE_FULL_VIDEO_FEATURES_OF_K2'); ?></p></div>
						</div>
						<?php endif; ?>
					<table class="admintable table" id="item_video_content">
						<tr>
							<td align="right" class="key">
								<?php echo JText::_('K2_MEDIA_SOURCE'); ?>
							</td>
							<td>
								<div id="k2MediaTabs" class="k2Tabs">
									<ul class="k2TabsNavigation">
										<li><a href="#k2MediaTab4"><?php echo JText::_('K2_EMBED'); ?></a></li>
									</ul>
									<div class="k2TabsContent" id="k2MediaTab4">
										<div class="panel" id="embedVideo">
											<?php echo JText::_('K2_PASTE_HTML_EMBED_CODE_BELOW'); ?>
											<br />
											<textarea name="embedVideo" rows="5" cols="50" class="textarea"><?php echo $this->lists['embedVideo']; ?></textarea>
										</div>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td align="right" class="key">
								<?php echo JText::_('K2_MEDIA_CAPTION'); ?>
							</td>
							<td>
								<input type="text" name="video_caption" size="50" class="text_area" value="<?php echo $this->row->video_caption; ?>" />
							</td>
						</tr>
						<tr>
							<td align="right" class="key">
								<?php echo JText::_('K2_MEDIA_CREDITS'); ?>
							</td>
							<td>
								<input type="text" name="video_credits" size="50" class="text_area" value="<?php echo $this->row->video_credits; ?>" />
							</td>
						</tr>
						<?php if($this->row->video): ?>
						<tr>
							<td align="right" class="key">
								<?php echo JText::_('K2_MEDIA_PREVIEW'); ?>
							</td>
							<td>
								<?php echo $this->row->video; ?>
								<br />
								<input type="checkbox" name="del_video" id="del_video" />
								<label for="del_video"><?php echo JText::_('K2_USE_THE_FORM_ABOVE_TO_REPLACE_THE_EXISTING_VIDEO_OR_CHECK_THIS_BOX_TO_DELETE_CURRENT_VIDEO'); ?></label>
							</td>
						</tr>
						<?php endif; ?>
					</table>
					<?php endif; ?>
					<?php if (count($this->K2PluginsItemVideo)): ?>
					<div class="itemPlugins">
						<?php foreach($this->K2PluginsItemVideo as $K2Plugin): ?>
						<?php if(!is_null($K2Plugin)): ?>
						<fieldset>
							<legend><?php echo $K2Plugin->name; ?></legend>
							<?php echo $K2Plugin->fields; ?>
						</fieldset>
						<?php endif; ?>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<?php if ($this->params->get('showExtraFieldsTab')): ?>
				<!-- Tab extra fields -->
				<div class="k2TabsContent" id="k2TabExtraFields">
					<div id="extraFieldsContainer">
						<?php if (count($this->extraFields)): ?>
						<table class="admintable table" id="extraFields">
							<?php foreach($this->extraFields as $extraField): ?>
							<tr>
								<?php if($extraField->type == 'header'): ?>
								<td colspan="2" ><h4 class="k2ExtraFieldHeader"><?php echo $extraField->name; ?></h4></td>
								<?php else: ?>
								<td align="right" class="key">
									<label for="K2ExtraField_<?php echo $extraField->id; ?>"><?php echo $extraField->name; ?></label>
								</td>
								<td>
									<?php echo $extraField->element; ?>
								</td>
								<?php endif; ?>
							</tr>
							<?php endforeach; ?>
						</table>
						<?php else: ?>
							<?php if (K2_JVERSION == '15'): ?>
								<dl id="system-message">
									<dt class="notice"><?php echo JText::_('K2_NOTICE'); ?></dt>
									<dd class="notice message fade">
										<ul>
											<li><?php echo JText::_('K2_PLEASE_SELECT_A_CATEGORY_FIRST_TO_RETRIEVE_ITS_RELATED_EXTRA_FIELDS'); ?></li>
										</ul>
									</dd>
								</dl>
							<?php elseif (K2_JVERSION == '25'): ?>
							<div id="system-message-container">
								<dl id="system-message">
									<dt class="notice"><?php echo JText::_('K2_NOTICE'); ?></dt>
									<dd class="notice message">
										<ul>
											<li><?php echo JText::_('K2_PLEASE_SELECT_A_CATEGORY_FIRST_TO_RETRIEVE_ITS_RELATED_EXTRA_FIELDS'); ?></li>
										</ul>
									</dd>
								</dl>
							</div>
							<?php else: ?>
							<div class="alert">
								<h4 class="alert-heading"><?php echo JText::_('K2_NOTICE'); ?></h4>
								<div>
									<p><?php echo JText::_('K2_PLEASE_SELECT_A_CATEGORY_FIRST_TO_RETRIEVE_ITS_RELATED_EXTRA_FIELDS'); ?></p>
								</div>
							</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<?php if (count($this->K2PluginsItemExtraFields)): ?>
					<div class="itemPlugins">
						<?php foreach($this->K2PluginsItemExtraFields as $K2Plugin): ?>
						<?php if(!is_null($K2Plugin)): ?>
						<fieldset>
							<legend><?php echo $K2Plugin->name; ?></legend>
							<?php echo $K2Plugin->fields; ?>
						</fieldset>
						<?php endif; ?>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<?php if ($this->params->get('showAttachmentsTab')): ?>
				<!-- Tab attachements -->
				<div class="k2TabsContent" id="k2TabAttachments">
					<div class="itemAttachments">
						<?php if (count($this->row->attachments)): ?>
						<table class="adminlist table">
							<tr>
								<th>
									<?php echo JText::_('K2_FILENAME'); ?>
								</th>
								<th>
									<?php echo JText::_('K2_TITLE'); ?>
								</th>
								<th>
									<?php echo JText::_('K2_TITLE_ATTRIBUTE'); ?>
								</th>
								<th>
									<?php echo JText::_('K2_DOWNLOADS'); ?>
								</th>
								<th>
									<?php echo JText::_('K2_OPERATIONS'); ?>
								</th>
							</tr>
							<?php foreach($this->row->attachments as $attachment): ?>
							<tr>
								<td class="attachment_entry">
									<?php echo $attachment->filename; ?>
								</td>
								<td>
									<?php echo $attachment->title; ?>
								</td>
								<td>
									<?php echo $attachment->titleAttribute; ?>
								</td>
								<td>
									<?php echo $attachment->hits; ?>
								</td>
								<td>
									<a href="<?php echo $attachment->link; ?>"><?php echo JText::_('K2_DOWNLOAD'); ?></a> <a class="deleteAttachmentButton" href="<?php echo JURI::base(true); ?>/index.php?option=com_k2&amp;view=item&amp;task=deleteAttachment&amp;id=<?php echo $attachment->id?>&amp;cid=<?php echo $this->row->id; ?>"><?php echo JText::_('K2_DELETE'); ?></a>
								</td>
							</tr>
							<?php endforeach; ?>
						</table>
						<?php endif; ?>
					</div>
					<div id="addAttachment">
						<input type="button" id="addAttachmentButton" class="k2Selector" value="<?php echo JText::_('K2_ADD_ATTACHMENT_FIELD'); ?>" />
						<i>(<?php echo JText::_('K2_MAX_UPLOAD_SIZE'); ?>: <?php echo ini_get('upload_max_filesize'); ?>)</i>
					</div>
					<div id="itemAttachments"></div>
					<?php if (count($this->K2PluginsItemAttachments)): ?>
					<div class="itemPlugins">
						<?php foreach($this->K2PluginsItemAttachments as $K2Plugin): ?>
						<?php if(!is_null($K2Plugin)): ?>
						<fieldset>
							<legend><?php echo $K2Plugin->name; ?></legend>
							<?php echo $K2Plugin->fields; ?>
						</fieldset>
						<?php endif; ?>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<?php if(count(array_filter($this->K2PluginsItemOther)) && $this->params->get('showK2Plugins')): ?>
				<!-- Tab other plugins -->
				<div class="k2TabsContent" id="k2TabPlugins">
					<div class="itemPlugins">
						<?php foreach($this->K2PluginsItemOther as $K2Plugin): ?>
						<?php if(!is_null($K2Plugin)): ?>
						<fieldset>
							<legend><?php echo $K2Plugin->name; ?></legend>
							<?php echo $K2Plugin->fields; ?>
						</fieldset>
						<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>
			</div>
			<!-- Tabs end here -->

			<input type="hidden" name="isSite" value="<?php echo (int)$this->mainframe->isSite(); ?>" />
			<?php if($this->mainframe->isSite()): ?>
			<input type="hidden" name="lang" value="<?php echo JRequest::getCmd('lang'); ?>" />
			<?php endif; ?>
			<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="com_k2" />
			<input type="hidden" name="view" value="item" />
			<input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>" />
			<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid'); ?>" />
			<?php echo JHTML::_('form.token'); ?>

		</div>
		<div class="k2NavTabContent" id="k2TabPubAndMeta">

			<ul class="k2ScrollSpyMenu">
				<?php if($this->row->id): ?>
				<li><a href="#iteminfo"><?php echo JText::_('K2_ITEM_INFO'); ?></a></li>
				<?php endif; ?>
				<li><a href="#publishing"><?php echo JText::_('K2_PUBLISHING'); ?></a></li>
				<li><a href="#metadata"><?php echo JText::_('K2_METADATA'); ?></a></li>
			</ul>

			<div class="k2ScrollingContent">

				<?php if($this->row->id): ?>
				<a id="iteminfo"></a>
				<h3><?php echo JText::_('K2_ITEM_INFO'); ?></h3>
				<table class="sidebarDetails table">
					<tr>
						<td>
							<strong><?php echo JText::_('K2_ITEM_ID'); ?></strong>
						</td>
						<td>
							<?php echo $this->row->id; ?>
						</td>
					</tr>
					<tr>
						<td>
							<strong><?php echo JText::_('K2_PUBLISHED'); ?></strong>
						</td>
						<td>
							<?php echo ($this->row->published > 0) ? JText::_('K2_YES') : JText::_('K2_NO'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<strong><?php echo JText::_('K2_FEATURED'); ?></strong>
						</td>
						<td>
							<?php echo ($this->row->featured > 0) ? JText::_('K2_YES'):	JText::_('K2_NO'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<strong><?php echo JText::_('K2_CREATED_DATE'); ?></strong>
						</td>
						<td>
							<?php echo $this->lists['created']; ?>
						</td>
					</tr>
					<tr>
						<td>
							<strong><?php echo JText::_('K2_CREATED_BY'); ?></strong>
						</td>
						<td>
							<?php echo $this->row->author; ?>
						</td>
					</tr>
					<tr>
						<td>
							<strong><?php echo JText::_('K2_MODIFIED_DATE'); ?></strong>
						</td>
						<td>
							<?php echo $this->lists['modified']; ?>
						</td>
					</tr>
					<tr>
						<td>
							<strong><?php echo JText::_('K2_MODIFIED_BY'); ?></strong>
						</td>
						<td>
							<?php echo $this->row->moderator; ?>
						</td>
					</tr>
					<tr>
						<td>
							<strong><?php echo JText::_('K2_HITS'); ?></strong>
						</td>
						<td>
							<?php echo $this->row->hits; ?>
							<?php if($this->row->hits): ?>
							<input id="resetHitsButton" type="button" value="<?php echo JText::_('K2_RESET'); ?>" class="button" name="resetHits" />
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td>
							<strong><?php echo JText::_('K2_RATING'); ?></strong>
						</td>
						<td>
							<?php echo $this->row->ratingCount; ?> <?php echo JText::_('K2_VOTES'); ?>
							<?php if($this->row->ratingCount): ?>
							<br />
							(<?php echo JText::_('K2_AVERAGE_RATING'); ?>: <?php echo number_format(($this->row->ratingSum/$this->row->ratingCount),2); ?>/5.00)
							<?php endif; ?>
							<input id="resetRatingButton" type="button" value="<?php echo JText::_('K2_RESET'); ?>" class="button" name="resetRating" />
						</td>
					</tr>
				</table>
				<?php endif; ?>



				<div class="xmlParamsFields">
					<a id="publishing"></a>
					<h3><?php echo JText::_('K2_PUBLISHING'); ?></h3>
					<ul class="adminformlist">				
						<li>
						    <div class="paramLabel">
								<label><?php echo JText::_('K2_CREATION_DATE'); ?></label>
							</div>
							<div class="paramValue">
								<?php echo $this->lists['createdCalendar']; ?>
							</div>
						<li>
						    <div class="paramLabel">
								<label><?php echo JText::_('K2_START_PUBLISHING'); ?></label>
							</div>
							<div class="paramValue">
								<?php echo $this->lists['publish_up']; ?>
							</div>
						</li>
						<li>
						    <div class="paramLabel">
								<label><?php echo JText::_('K2_FINISH_PUBLISHING'); ?></label>
							</div>
							<div class="paramValue">
								<?php echo $this->lists['publish_down']; ?>
							</div>
						</li>
					</ul>
				
					<div class="clr"></div>
					<a id="metadata"></a>

					<h3><?php echo JText::_('K2_METADATA'); ?></h3>
					<ul class="adminformlist">				
						<li>
							<div class="paramLabel">
								<label><?php echo JText::_('K2_DESCRIPTION'); ?></label>
							</div>
							<div class="paramValue">
								<textarea name="metadesc" rows="5" cols="20"><?php echo $this->row->metadesc; ?></textarea>
							</div>
						</li>

						<li>
							<div class="paramLabel">
								<label><?php echo JText::_('K2_KEYWORDS'); ?></label>
							</div>
							<div class="paramValue">
								<textarea name="metakey" rows="5" cols="20"><?php echo $this->row->metakey; ?></textarea>
							</div>
						</li>
						<li>
							<div class="paramLabel">
								<label><?php echo JText::_('K2_ROBOTS'); ?></label>
							</div>
							<div class="paramValue">
								<input type="text" name="meta[robots]" value="<?php echo $this->lists['metadata']->get('robots'); ?>" />
							</div>
						</li>
						<li>
							<div class="paramLabel">
								<label><?php echo JText::_('K2_AUTHOR'); ?></label>
							</div>
							<div class="paramValue">
								<input type="text" name="meta[author]" value="<?php echo $this->lists['metadata']->get('author'); ?>" />
							</div>
						</li>
					<ul>
				</div>
			</div>
		</div>

		<?php if($this->mainframe->isAdmin()): ?>
		<div class="k2NavTabContent" id="k2TabDisplaySet">

			<ul class="k2ScrollSpyMenu">
				<li><a href="#catViewOptions"><?php echo JText::_('K2_ITEM_VIEW_OPTIONS_IN_CATEGORY_LISTINGS'); ?></a></li>
				<li><a href="#itemViewOptions"><?php echo JText::_('K2_ITEM_VIEW_OPTIONS'); ?></a></li>
				<?php if($this->aceAclFlag): ?>
				<!-- AceACL -->
				<li><a href="#aceACLOptions"><?php echo JText::_('AceACL') . ' ' . JText::_('COM_ACEACL_COMMON_PERMISSIONS'); ?></a></li>
				<?php endif; ?>
			</ul>

			<div class="k2ScrollingContent">
				<a id="catViewOptions"></a>
				<h3><?php echo JText::_('K2_ITEM_VIEW_OPTIONS_IN_CATEGORY_LISTINGS'); ?></h3>
				<div class="xmlParamsFields">
					<fieldset class="panelform">
						<ul class="adminformlist">
							<?php if(version_compare( JVERSION, '1.6.0', 'ge' )): ?>
							<?php foreach($this->form->getFieldset('item-view-options-listings') as $field): ?>
							<li>
								<?php if($field->type=='header'): ?>
								<div class="paramValueHeader"><?php echo $field->input; ?></div>
								<?php elseif($field->type=='Spacer'): ?>
								<div class="paramValueSpacer">&nbsp;</div>
								<div class="clr"></div>
								<?php else: ?>
								<div class="paramLabel"><?php echo $field->label; ?></div>
								<div class="paramValue"><?php echo $field->input; ?></div>
								<div class="clr"></div>
								<?php endif; ?>
							</li>
							<?php endforeach; ?>
							<?php else: ?>
							<?php foreach($this->form->getParams('params', 'item-view-options-listings') as $param): ?>
							<li>
								<?php if((string)$param[1]=='' || $param[5] == ''): ?>
								<div class="paramValueHeader"><?php echo $param[1]; ?></div>
								<?php else: ?>
								<div class="paramLabel"><?php echo $param[0]; ?></div>
								<div class="paramValue"><?php echo $param[1]; ?></div>
								<div class="clr"></div>
								<?php endif; ?>
							</li>
							<?php endforeach; ?>
							<?php endif; ?>
						</ul>
					</fieldset>
				</div>
				<a id="itemViewOptions"></a>
				<h3><?php echo JText::_('K2_ITEM_VIEW_OPTIONS'); ?></h3>
				<div class="xmlParamsFields">
					<fieldset class="panelform">
						<ul class="adminformlist">
							<?php if(version_compare( JVERSION, '1.6.0', 'ge' )): ?>
							<?php foreach($this->form->getFieldset('item-view-options') as $field): ?>
							<li>
								<?php if($field->type=='header'): ?>
								<div class="paramValueHeader"><?php echo $field->input; ?></div>
								<?php elseif($field->type=='Spacer'): ?>
								<div class="paramValueSpacer">&nbsp;</div>
								<div class="clr"></div>
								<?php else: ?>
								<div class="paramLabel"><?php echo $field->label; ?></div>
								<div class="paramValue"><?php echo $field->input; ?></div>
								<div class="clr"></div>
								<?php endif; ?>
							</li>
							<?php endforeach; ?>
							<?php else: ?>
							<?php foreach($this->form->getParams('params', 'item-view-options') as $param): ?>
							<li>
								<?php if((string)$param[1]=='' || $param[5] == ''): ?>
								<div class="paramValueHeader"><?php echo $param[1]; ?></div>
								<?php else: ?>
								<div class="paramLabel"><?php echo $param[0]; ?></div>
								<div class="paramValue"><?php echo $param[1]; ?></div>
								<div class="clr"></div>
								<?php endif; ?>
							</li>
							<?php endforeach; ?>
							<?php endif; ?>
						</ul>
					</fieldset>
				</div>

				<?php if($this->aceAclFlag): ?>
				<!-- AceACL -->
				<a id="aceACLOptions"></a>
				<h3><?php echo JText::_('AceACL') . ' ' . JText::_('COM_ACEACL_COMMON_PERMISSIONS'); ?></h3>
				<div><?php AceaclApi::getWidget('com_k2.item.'.$this->row->id, true); ?></div>
				<?php endif; ?>
			</div>

		</div>
		<?php endif; ?>
	</div>
	<!-- Top Nav Tabs END here -->

	<?php if($this->mainframe->isSite()): ?>
		<!-- Frontend Editing -->
		</div>
	</div>
	<?php endif; ?>

</form>

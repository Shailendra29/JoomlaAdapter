<?php
/* Portions copyright © 2013, TIBCO Software Inc.
 * All rights reserved.
 */
?>
<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
include_once JPATH_BASE . "/includes/api.php";
$k = $p1 = 0;
$params = $this->tmpl_params['list'];
$core = array('type_id' => 'Type', 'user_id','','','','','','','','', );
JHtml::_('dropdown.init');
$exclude = $params->get('tmpl_params.field_id_exclude');
settype($exclude, 'array');
foreach ($exclude as &$value) {
	$value = $this->fields_keys_by_id[$value];
}
$this->items = array_filter($this->items,"getEnvironmentRecords");
?>
<?php if($params->get('tmpl_core.show_title_index')):?>
	<h2><?php echo JText::_('CONTHISPAGE')?></h2>
	<ul>
		<?php foreach ($this->items AS $item):?>
			<li><a href="#record<?php echo $item->id?>"><?php echo $item->title?></a></li>
		<?php endforeach;?>
	</ul>
<?php endif;?>

<style>
.fields-list {
	padding: 5px 5px 5px 25px !important;
}
.relative_ctrls {
	position: relative;
}
.user-ctrls {
	position: absolute;
	top:-8px;
	right: 0;
}
</style>
<table class="table table-striped">
	<thead>
		<tr>
			<?php if($params->get('tmpl_core.item_title')):?>
				<th><?php echo JText::_('CTITLE');?></th>
			<?php endif;?>

			<?php if($params->get('tmpl_core.item_rating')):?>
				<th>
					<?php echo JText::_('CRATING');?>
				</th>
			<?php endif;?>

			<?php if($params->get('tmpl_core.item_author_avatar')):?>
				<th>
					<?php echo JText::_('CAVATAR');?>
				</th>
			<?php endif;?>

			<?php if($params->get('tmpl_core.item_author') == 1):?>
				<th>
					<?php echo JText::_('CAUTHOR');?>
				</th>
			<?php endif;?>


			<?php if($params->get('tmpl_core.item_type') == 1):?>
				<th>
					<?php echo JText::_('CTYPE')?>
				</th>
			<?php endif;?>

			<?php foreach ($this->total_fields_keys AS $field):?>
				<?php if(in_array($field->key, $exclude)) continue; ?>
				<th width="1%" nowrap="nowrap">
					<?php echo JText::_($field->label);?></th>
			<?php endforeach;?>

			<?php if($params->get('tmpl_core.item_user_categories') == 1 && $this->section->params->get('personalize.pcat_submit')):?>
				<th nowrap="nowrap">
					<?php echo JText::_('CCATEGORY');?>
				</th>
			<?php endif;?>

			<?php if($params->get('tmpl_core.item_categories') == 1 && $this->section->categories ):?>
				<th nowrap="nowrap">
					<?php echo JText::_('CCATEGORY');?>
				</th>
			<?php endif;?>

			<?php if($params->get('tmpl_core.item_ctime') == 1):?>
				<th nowrap="nowrap">
					<?php echo JText::_('CCREATED');?>
				</th>
			<?php endif;?>

			<?php if($params->get('tmpl_core.item_mtime') == 1):?>
				<th nowrap="nowrap">
					<?php echo JText::_('CCHANGED');?>
				</th>
			<?php endif;?>

			<?php if($params->get('tmpl_core.item_extime') == 1):?>
				<th nowrap="nowrap">
					<?php echo JText::_('CEXPIRE');?>
				</th>
			<?php endif;?>

			<?php if($params->get('tmpl_core.item_comments_num') == 1):?>
				<th nowrap="nowrap">
					<span rel="tooltip" data-original-title="<?php echo JText::_('CCOMMENTS');?>"><?php echo JString::substr(JText::_('CCOMMENTS'), 0, 1)?></span>
				</th>
			<?php endif;?>

			<?php if($params->get('tmpl_core.item_favorite_num') == 1):?>
				<th nowrap="nowrap">
					<span rel="tooltip" data-original-title="<?php echo JText::_('CFAVORITE');?>"><?php echo JString::substr(JText::_('CFAVORITE'), 0, 1)?></span>
				</th>
			<?php endif;?>

			<?php if($params->get('tmpl_core.item_vote_num') == 1):?>
				<th nowrap="nowrap">
					<span rel="tooltip" data-original-title="<?php echo JText::_('CVOTES');?>"><?php echo JString::substr(JText::_('CVOTES'), 0, 1)?></span>
				</th>
			<?php endif;?>

			<?php if($params->get('tmpl_core.item_follow_num') == 1):?>
				<th nowrap="nowrap">
					<span rel="tooltip" data-original-title="<?php echo JText::_('CFOLLOWERS');?>"><?php echo JString::substr(JText::_('CFOLLOWERS'), 0, 1)?></span>
				</th>
			<?php endif;?>

			<?php if($params->get('tmpl_core.item_hits') == 1):?>
				<th nowrap="nowrap" width="1%">
					<span rel="tooltip" data-original-title="<?php echo JText::_('CHITS');?>"><?php echo JString::substr(JText::_('CHITS'), 0, 1)?></span>
				</th>
			<?php endif;?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($this->items AS $item):?>
			<tr class="<?php
			if($item->featured)
			{
				echo ' success';
			}
			elseif($item->expired)
			{
				echo ' error';
			}
			elseif($item->future)
			{
				echo ' warning';
			}
			?>">
				<?php if($params->get('tmpl_core.item_title')):?>
					<td class="has-context">
						<div class="relative_ctrls">
							<?php if($this->user->get('id')):?>
								<div class="user-ctrls">
									<div class="btn-group" style="display: none;">
										<?php echo HTMLFormatHelper::bookmark($item, $this->submission_types[$item->type_id], $params);?>
										<?php echo HTMLFormatHelper::follow($item, $this->section);?>
										<?php echo HTMLFormatHelper::repost($item, $this->section);?>
										<?php echo HTMLFormatHelper::compare($item, $this->submission_types[$item->type_id], $this->section);?>
									</div>
								</div>
							<?php endif;?>
							<?php if($this->submission_types[$item->type_id]->params->get('properties.item_title')):?>
								<div class="pull-left">
									<<?php echo $params->get('tmpl_core.title_tag', 'h2');?> class="record-title">
										<?php if($params->get('tmpl_core.item_link')):?>
											<a <?php echo $item->nofollow ? 'rel="nofollow"' : '';?> href="<?php echo JRoute::_($item->url);?>">
												<?php echo $item->title?>
											</a>
										<?php else :?>
											<?php echo $item->title?>
										<?php endif;?>
										<?php echo CEventsHelper::showNum('record', $item->id);?>
									</<?php echo $params->get('tmpl_core.title_tag', 'h2');?> class="record-title">
								</div>
							<?php endif;?>
						</div>
					</td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_rating')):?>
					<td nowrap="nowrap" valign="top">
						<?php echo $item->rating?>
					</td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_author_avatar')):?>
					<td>
						<img src="<?php echo CCommunityHelper::getAvatar($item->user_id, $params->get('tmpl_core.item_author_avatar_width', 40), $params->get('tmpl_core.item_author_avatar_height', 40));?>" />
					</td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_author') == 1):?>
					<td nowrap="nowrap"><?php echo CCommunityHelper::getName($item->user_id, $this->section);?>
					<?php if($params->get('tmpl_core.item_author_filter') /* && $item->user_id */):?>
						<?php echo FilterHelper::filterButton('filter_user', $item->user_id, NULL, JText::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $this->section, true)), $this->section);?>
					<?php endif;?>
					</td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_type') == 1):?>
					<td nowrap="nowrap"><?php echo $item->type_name;?>
					<?php if($params->get('tmpl_core.item_type_filter')):?>
						<?php echo FilterHelper::filterButton('filter_type', $item->type_id, NULL, JText::sprintf('CSHOWALLTYPEREC', $item->type_name), $this->section);?>
					<?php endif;?>
					</td>
				<?php endif;?>

				<?php foreach ($this->total_fields_keys AS $field):?>
					<?php if(in_array($field->key, $exclude)) continue; ?>
					<td class="<?php echo $field->params->get('core.field_class')?>"><?php if(isset($item->fields_by_key[$field->key]->result)) echo $item->fields_by_key[$field->key]->result ;?></td>
				<?php endforeach;?>

				<?php if($params->get('tmpl_core.item_user_categories') == 1 && $this->section->params->get('personalize.pcat_submit')):?>
					<td><?php echo $item->ucatname_link;?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_categories') == 1 && $this->section->categories):?>
					<td><?php echo implode(', ', $item->categories_links);?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_ctime') == 1):?>
					<td><?php echo JHtml::_('date', $item->created, $params->get('tmpl_core.item_time_format'));?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_mtime') == 1):?>
					<td><?php echo JHtml::_('date', $item->modify, $params->get('tmpl_core.item_time_format'));?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_extime') == 1):?>
					<td><?php echo ( $item->expire ? JHtml::_('date', $item->expire, $params->get('tmpl_core.item_time_format')) : JText::_('CNEVER'));?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_comments_num') == 1):?>
					<td><?php echo CommentHelper::numComments($this->submission_types[$item->type_id], $item);?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_favorite_num') == 1):?>
					<td><?php echo $item->favorite_num;?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_vote_num') == 1):?>
					<td><?php echo $item->votes;?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_follow_num') == 1):?>
					<td><?php echo $item->subscriptions_num;?></td>
				<?php endif;?>

				<?php if($params->get('tmpl_core.item_hits') == 1):?>
					<td><?php echo $item->hits;?></td>
				<?php endif;?>
			</tr>
		<?php endforeach;?>
	</tbody>
</table>


<?php
/**
 * Get the right records which we need
 * @param array element $item
 * @return boolean
 */
function getEnvironmentRecords($item){
	return (isset($item->type_id) && $item->type_id==4);
}


?>
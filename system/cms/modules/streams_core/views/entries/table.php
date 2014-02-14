<!-- .panel-body -->
<!--<div class="panel-body">-->

	<?php if (! empty($filters)): ?>
		<?php $this->load->view('streams_core/entries/filters'); ?>
	<?php endif; ?>


	<?php if ($entries->count() > 0): ?>

		<table class="table table-hover n-m">
			<thead>
				<tr>
					<?php if ($stream->sorting == 'custom'): ?><th></th><?php endif; ?>
					<?php foreach ($fieldNames as $fieldSlug => $fieldName): ?>
					<?php

						// Replace relation: from Cp voodoo
						$fieldSlug = str_replace('relation:', '', $fieldSlug);

						// Get our query string
						$query_string = array();

						// Parse it into above array
						parse_str($_SERVER['QUERY_STRING'], $query_string);

						$original_query_string = $query_string;

						// Set the order slug
						$query_string['order-'.$stream->stream_namespace.'-'.$stream->stream_slug] = $fieldSlug;

						// Set the sort string
						$query_string['sort-'.$stream->stream_namespace.'-'.$stream->stream_slug] = 
							isset($query_string['sort-'.$stream->stream_namespace.'-'.$stream->stream_slug])
								? ($query_string['sort-'.$stream->stream_namespace.'-'.$stream->stream_slug] == 'ASC'
									? 'DESC'
									: 'ASC')
								: 'ASC';

						// Determine our caret for this item
						$caret = false;

						if (isset($original_query_string['order-'.$stream->stream_namespace.'-'.$stream->stream_slug]) and $original_query_string['order-'.$stream->stream_namespace.'-'.$stream->stream_slug] == $fieldSlug)
							if (isset($original_query_string['sort-'.$stream->stream_namespace.'-'.$stream->stream_slug]))
								if ($original_query_string['sort-'.$stream->stream_namespace.'-'.$stream->stream_slug] == 'ASC')
									$caret = '<span class="fa fa-caret-up"></span>';
								else
									$caret = '<span class="fa fa-caret-down"></span>';
							else
								$caret = '<span class="fa fa-caret-up"></span>';

						?>
						<th>
							<a href="<?php echo site_url(uri_string()).'?'.http_build_query($query_string); ?>">
								<?php echo $fieldName; ?>
                                <?php if ($caret) echo $caret; ?>
							</a>
						</th>

					<?php endforeach; ?>
				    <th></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($entries as $entry) { ?>

				<tr>

					<?php if ($stream->sorting == 'custom'): ?><td width="30" class="handle"><?php echo Asset::img('icons/drag_handle.gif', 'Drag Handle'); ?></td><?php endif; ?>

					<?php if (! empty($viewOptions)): foreach($viewOptions as $viewOption): ?>
					<td>

						<input type="hidden" name="action_to[]" value="<?php echo $entry->getKey();?>" />

						<?php echo $entry->{$viewOption}; ?>

					</td>
					<?php endforeach; endif; ?>
					<td class="text-right">

						<?php

							if (isset($buttons)) {
								$all_buttons = array();

								foreach ($buttons as $button) {

                                    // Html?
                                    if (isset($button['html'])) {
                                        $all_buttons[] = ci()->parser->parse_string($button['html'], $entry->toArray(), true, false, false);
                                        continue;
                                    }

									// The second is kept for backwards compatibility
									$url = ci()->parser->parse_string($button['url'], $entry->toArray(), true);
									$url = str_replace('-entry_id-', $entry->getKey(), $url);

									// Label
									$label = lang_label($button['label']);

									// Remove URL
									unset($button['url'], $button['label']);

									// Parse variables in attributes
									foreach ($button as $key => &$value)
										$value = ci()->parser->parse_string($value, $entry->toArray(), true);

									$all_buttons[] = anchor($url, $label, $button);
								}

								echo implode('&nbsp;', $all_buttons);
								unset($all_buttons);
							}

						?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
	    </table>

	    <div class="panel-footer">
			
			<?php if ($pagination): ?>
                <?php echo $pagination['links']; ?>

                <?php echo form_dropdown(null, array(5 => 5, 10 => 10, 25 => 25, 50 => 50, 100 => 100), ci()->input->get('limit-'.$stream->stream_namespace.'-'.$stream->stream_slug) ?: Settings::get('records_per_page'), 'class="pull-right" style="width: 100px;" onchange="$(\'select#limit-'.$stream->stream_namespace.'-'.$stream->stream_slug.'\').val($(this).val()).closest(\'form\').submit();"'); ?>
            <?php endif; ?>
            <div class="clearfix"></div>
		</div>

	<?php else: ?>

		<div class="alert alert-info m">
			<?php

				if (isset($no_entries_message) and $no_entries_message) {
					echo lang_label($no_entries_message);
				} else {
					echo lang('streams:no_entries');
				}

			?>
		</div><!--.no_data-->

	<?php endif; ?>

<!--</div>-->
<!-- /.panel-body -->

<div class="stats" style="margin-bottom: -45px;">
    <small class="c-gray m-l" style="line-height: 40px;">
        Showing results <?php echo ($pagination['offset']+1).' - '.($pagination['current_page']*$pagination['per_page']).' of '.$pagination['total']; ?>
    </small>
</div>

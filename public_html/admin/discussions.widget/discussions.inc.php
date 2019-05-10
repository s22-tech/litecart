<?php
  $widget_discussions_cache_token = cache::token('widget_discussions', array(), 'file');
  if (cache::capture($widget_discussions_cache_token, 0, true)) {

    try {

      $url = document::link('https://www.litecart.net/feeds/discussions.rss');

      $client = new http_client();
      $client->timeout = 10;
      $response = @$client->call('GET', $url);$response = 'fishy';
      libxml_use_internal_errors(true);
      $rss = simplexml_load_string($response);

      foreach (libxml_get_errors() as $error) throw new Exception($error->message);

      if (!empty($rss->channel->item)) {

        $discussions = array();
        foreach ($rss->channel->item as $item) {
          $discussions[] = $item;
          if (count($discussions) == 16) break;
        }
?>
<style>
#widget-discussions .row [class^="col-"] > * {
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}
#widget-discussions .row [class^="col-"] .description {
  opacity: 0.85;
}
</style>

<div id="widget-discussions" class="widget panel panel-default">
  <div class="panel-heading"><?php echo language::translate('title_most_recent_forum_topics', 'Most Recent Forum Topics'); ?></div>
  <div class="panel-body">
      <div class="row">
      <?php foreach ($discussions as $item) { ?>
      <div class="col-sm-6 col-md-4 col-lg-3">
        <div class="title"><a href="<?php echo htmlspecialchars((string)$item->link); ?>" target="_blank"><?php echo htmlspecialchars((string)$item->title); ?></a></div>
        <div class="description"><?php echo language::strftime('%e %b', strtotime($item->pubDate)); ?> <?php echo language::translate('text_by', 'by'); ?> <?php echo (string)$item->author; ?></div>
      </div>
      <?php } ?>
    </div>
  </div>
</div>
<?php
      }
    } catch(Exception $e) {
      // Do nothing
    }
    cache::end_capture($widget_discussions_cache_token);
  }

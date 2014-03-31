<?php

include 'config.php';
include 'lib/webrtc_archiving_beta.php';
include 'layout.php';

sample_header();

$apiObj = new OpenTokArchivingInterface($config_api_key, $config_api_secret);

$page = 1;
$perpage = 5;

if((int)$_REQUEST['page']) {
  $page = $_REQUEST['page'];
}

$res = $apiObj->getArchives(($page - 1) * $perpage, $perpage);

$archives = $res->body;

$showPrevious = "";
if($page > 1) {
  $showPrevious = "past-archives.php?page=" . ($page - 1);
}

$showNext = "";
if($archives->count > $page * $perpage) {
  $showNext = "past-archives.php?page=" . ($page + 1);
}

?>

  <div class="container bump-me">

    <div class="body-content">

      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Past Recordings</h3>
        </div>
        <div class="panel-body">
          <?php if($archives->items) {  ?>
          <table class="table">
            <thead>
              <tr>
                <th>&nbsp;</th>
                <th>Created</th>
                <th>Duration</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>

              <?php foreach ($archives->items as &$item) { ?>
              <tr data-item-id="<%= item.id %>">
                <td>
                  <?php if($item->status == 'available' && $item->url) { ?>
                    <a href="<?php echo $item->url; ?>">
                  <?php } ?>
                  <?php if($item->name) { echo $item->name; } else { echo "Untitled"; } ?>
                  <?php if($item->status == 'available' && $item->url) { ?>
                    </a>
                  <?php } ?>
                <td><?php echo(date("M j, Y \\a\\t g:i a", $item->createdAt / 1000)); ?></td>
                <td><?php echo $item->duration; ?> seconds</td>
                <td><?php echo $item->status; ?></td>
                <td>
                  <?php if($item->status == 'available') { ?>
                    <a href="delete-archive.php?id=<?php echo $item->id; ?>">Delete</a>
                  <?php } ?>
              </tr>
              <?php } ?>

            </tbody>
          </table>
          <?php } else { ?>
          <p>
            There are no archives currently. Try making one in the <a href="host-view.php">host view</a>.
          </p>
          <?php } ?>
        </div>
        <div class="panel-footer">
          <?php if($showPrevious) { ?>
          <a href="<?php echo $showPrevious; ?>" class="pull-left">&larr; Newer</a>
          <?php } ?>
          &nbsp;
          <?php if($showNext) { ?>
          <a href="<?php echo $showNext; ?>" class="pull-right">Older &rarr;</a>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>

<?php

sample_footer();

?>

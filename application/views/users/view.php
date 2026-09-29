<div class="card">
  <div class="row">
    <div class="col-sm-3 text-center">
      <?php if ($user->profile_pic): ?>
        <img class="avatar avatar-lg" src="<?= base_url('uploads/profiles/' . $user->profile_pic) ?>" alt="">
      <?php else: ?><div class="avatar avatar-lg" style="display:inline-block"></div><?php endif; ?>
    </div>
    <div class="col-sm-9">
      <h3 style="margin-top:0"><?= html_escape($user->name) ?></h3>
      <table class="table table-condensed">
        <tr><th width="120">Email</th><td><?= html_escape($user->email) ?></td></tr>
        <tr><th>Phone</th><td><?= html_escape($user->phone ?: '-') ?></td></tr>
        <tr><th>Gender</th><td><?= ucfirst($user->gender) ?></td></tr>
        <tr><th>Hobbies</th><td><?= $hobby_names ? html_escape(implode(', ', $hobby_names)) : '-' ?></td></tr>
        <tr><th>Created</th><td><?= $user->created_at ?></td></tr>
        <tr><th>Updated</th><td><?= $user->updated_at ?: '-' ?></td></tr>
      </table>
      <a href="<?= site_url('users/edit/' . $user->id) ?>" class="btn btn-warning">Edit</a>
      <a href="<?= site_url('users/send_mail/' . $user->id) ?>" class="btn btn-success">Send Mail</a>
      <a href="<?= site_url('users') ?>" class="btn btn-default">Back</a>
    </div>
  </div>
</div>

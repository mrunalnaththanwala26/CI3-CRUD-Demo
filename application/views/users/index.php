<div class="card">
  <div class="row" style="margin-bottom:15px">
    <div class="col-sm-6"><h3 style="margin:0">Users</h3></div>
    <div class="col-sm-6 text-right">
      <form class="form-inline" method="get" action="<?= site_url('users') ?>">
        <input type="text" name="q" value="<?= html_escape($search) ?>" class="form-control" placeholder="Search name, email, phone">
        <button class="btn btn-default">Search</button>
        <a href="<?= site_url('users/create') ?>" class="btn btn-primary">+ Add User</a>
      </form>
    </div>
  </div>

  <div class="table-responsive">
  <table class="table table-hover">
    <thead><tr>
      <th>#</th><th>Photo</th><th>Name</th><th>Email</th><th>Phone</th><th>Gender</th><th>Hobbies</th><th width="230">Actions</th>
    </tr></thead>
    <tbody>
    <?php if (empty($users)): ?>
      <tr><td colspan="8" class="text-center text-muted">No users found.</td></tr>
    <?php endif; ?>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= $u->id ?></td>
        <td>
          <?php if ($u->profile_pic): ?>
            <img class="avatar" src="<?= base_url('uploads/profiles/' . $u->profile_pic) ?>" alt="">
          <?php else: ?><div class="avatar"></div><?php endif; ?>
        </td>
        <td><?= html_escape($u->name) ?></td>
        <td><?= html_escape($u->email) ?></td>
        <td><?= html_escape($u->phone) ?></td>
        <td><?= ucfirst($u->gender) ?></td>
        <td><?= html_escape($u->hobbies ?: '-') ?></td>
        <td>
          <form method="post" action="<?= site_url('users/delete/' . $u->id) ?>" style="display:inline"
                onsubmit="return confirm('Delete this user?')">
            <a href="<?= site_url('users/view/' . $u->id) ?>" class="btn btn-xs btn-info">View</a>
            <a href="<?= site_url('users/edit/' . $u->id) ?>" class="btn btn-xs btn-warning">Edit</a>
            <a href="<?= site_url('users/send_mail/' . $u->id) ?>" class="btn btn-xs btn-success">Mail</a>
            <button class="btn btn-xs btn-danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?= $links ?>
</div>

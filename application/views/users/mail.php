<div class="card">
  <h3 style="margin-top:0">Send Email to <?= html_escape($user->name) ?></h3>
  <?php if (validation_errors()): ?>
    <div class="alert alert-danger"><?= validation_errors('<div>', '</div>') ?></div>
  <?php endif; ?>
  <?= form_open('users/send_mail/' . $user->id) ?>
    <div class="form-group">
      <label>To</label>
      <input type="text" class="form-control" value="<?= html_escape($user->email) ?>" disabled>
    </div>
    <div class="form-group">
      <label>Subject *</label>
      <input type="text" name="subject" class="form-control" value="<?= set_value('subject') ?>">
    </div>
    <div class="form-group">
      <label>Message *</label>
      <textarea name="message" rows="6" class="form-control"><?= set_value('message') ?></textarea>
    </div>
    <button class="btn btn-success">Send</button>
    <a href="<?= site_url('users') ?>" class="btn btn-default">Cancel</a>
  <?= form_close() ?>
</div>

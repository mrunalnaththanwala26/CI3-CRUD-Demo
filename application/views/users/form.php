<?php $is_edit = !empty($user); ?>
<div class="card">
  <h3 style="margin-top:0"><?= html_escape($title) ?></h3>

  <?php if (validation_errors()): ?>
    <div class="alert alert-danger"><?= validation_errors('<div>', '</div>') ?></div>
  <?php endif; ?>
  <?php if ($upload_error): ?>
    <div class="alert alert-danger"><?= html_escape($upload_error) ?></div>
  <?php endif; ?>

  <?= form_open_multipart($is_edit ? 'users/edit/' . $user->id : 'users/create') ?>
    <div class="form-group">
      <label>Name *</label>
      <input type="text" name="name" class="form-control"
             value="<?= set_value('name', $is_edit ? $user->name : '') ?>">
    </div>
    <div class="form-group">
      <label>Email *</label>
      <input type="email" name="email" class="form-control"
             value="<?= set_value('email', $is_edit ? $user->email : '') ?>">
    </div>
    <div class="form-group">
      <label>Phone</label>
      <input type="text" name="phone" class="form-control"
             value="<?= set_value('phone', $is_edit ? $user->phone : '') ?>">
    </div>

    <div class="form-group">
      <label>Gender *</label><br>
      <?php $g = set_value('gender', $is_edit ? $user->gender : 'male'); ?>
      <?php foreach (['male', 'female', 'other'] as $opt): ?>
        <label class="radio-inline">
          <input type="radio" name="gender" value="<?= $opt ?>" <?= $g === $opt ? 'checked' : '' ?>> <?= ucfirst($opt) ?>
        </label>
      <?php endforeach; ?>
    </div>

    <div class="form-group hobby-box">
      <label>Hobbies</label><br>
      <?php foreach ($hobbies as $h): ?>
        <label>
          <input type="checkbox" name="hobbies[]" value="<?= $h->id ?>"
                 <?= in_array((int) $h->id, array_map('intval', $my_hobbies)) ? 'checked' : '' ?>>
          <?= html_escape($h->name) ?>
        </label>
      <?php endforeach; ?>
    </div>

    <div class="form-group">
      <label>Profile Picture (jpg/png/gif, max 2MB)</label>
      <?php if ($is_edit && $user->profile_pic): ?>
        <div style="margin-bottom:8px">
          <img class="avatar" src="<?= base_url('uploads/profiles/' . $user->profile_pic) ?>" alt="">
          <small class="text-muted">Current photo - choose a new file to replace it</small>
        </div>
      <?php endif; ?>
      <input type="file" name="profile_pic" accept="image/*">
    </div>

    <button class="btn btn-primary"><?= $is_edit ? 'Update' : 'Save' ?></button>
    <a href="<?= site_url('users') ?>" class="btn btn-default">Cancel</a>
  <?= form_close() ?>
</div>

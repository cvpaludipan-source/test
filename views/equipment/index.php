<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Equipment</h3>
  <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">Add Equipment</a>
</div>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="table-responsive">
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>Asset Tag</th>
        <th>Name</th>
        <th>Type</th>
        <th>Status</th>
        <th>Location</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($equipment as $item): ?>
      <tr>
        <td><?php echo htmlspecialchars($item['asset_tag']); ?></td>
        <td><?php echo htmlspecialchars($item['name']); ?></td>
        <td><?php echo htmlspecialchars($item['type']); ?></td>
        <td><span class="badge bg-<?php echo $item['status'] === 'available' ? 'success' : ($item['status']==='borrowed'?'warning':'secondary'); ?>"><?php echo htmlspecialchars($item['status']); ?></span></td>
        <td><?php echo htmlspecialchars($item['location']); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Modal Add -->
<div class="modal fade" id="modalAdd" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="/equipment/create">
        <div class="modal-header"><h5 class="modal-title">Add Equipment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Asset Tag</label>
            <input name="asset_tag" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Type</label>
            <input name="type" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Location</label>
            <input name="location" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

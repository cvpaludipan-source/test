<?php $user = auth_user(); ?>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-3 rounded">
  <div class="container-fluid">
    <a class="navbar-brand" href="/">ICT Borrowing</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if ($user): ?>
        <li class="nav-item"><a class="nav-link" href="/equipment">Equipment</a></li>
        <?php endif; ?>
      </ul>
      <div class="d-flex">
        <?php if ($user): ?>
            <span class="navbar-text me-3">Hello, <?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?></span>
            <a class="btn btn-outline-danger btn-sm" href="/logout">Logout</a>
        <?php else: ?>
            <a class="btn btn-primary btn-sm" href="/login">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

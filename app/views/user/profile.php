<?php include __DIR__ . '/../layouts/header.php'; 
$user         = $user ?? [];
$totalBorrows = $totalBorrows ?? '';
?>

<main class="mb-5 pb-5">
    <div class="container py-5" style="max-width: 560px;">

        <div class="text-center mb-4">
            <div class="profile-avatar mx-auto mb-3">
                <i class="bi bi-person-circle"></i>
            </div>
            <h2 class="profile-name" id="profileDisplayName"><?= htmlspecialchars($user['user_name']) ?></h2>
            <p class="profile-email" id="profileDisplayEmail"><?= htmlspecialchars($user['email']) ?></p>

            <?php if ($user['user_status'] === 'banned'): ?>
                <span class="badge bg-danger mt-1">Account Suspended</span>
            <?php endif; ?>
        </div>

        <!-- ── Stats Card ── -->
        <div class="profile-stats-card mb-4">
            <div class="profile-stat-row">
                <span class="profile-stat-label">Joined Since</span>
                <span class="profile-stat-value"><?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
            </div>
            <div class="profile-stat-row">
                <span class="profile-stat-label">Total Books Borrowed</span>
                <span class="profile-stat-value"><?= $totalBorrows ?></span>
            </div>
            <div class="profile-stat-row">
                <span class="profile-stat-label">Total Books Read</span>
                <span class="profile-stat-value profile-stat-placeholder">— Coming soon</span>
            </div>
        </div>

        <!-- ── Inline feedback ── -->
        <div id="profileFeedback" class="alert py-2 mb-3 d-none" role="alert"></div>

        <!-- ── Actions ── -->
        <div class="d-flex gap-3">
            <button class="btn profile-btn flex-grow-1"
                    data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <i class="bi bi-pencil me-2"></i>Edit Profile
            </button>
            <button class="btn profile-btn flex-grow-1"
                    data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                <i class="bi bi-key me-2"></i>Change Password
            </button>
        </div>

        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>/mybooks" class="profile-link">
                <i class="bi bi-bookmark me-1"></i>View My Books
            </a>
        </div>

    </div>
</main>


<!-- ================================================================
     EDIT PROFILE MODAL
================================================================= -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title mb-0">Edit Profile</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="editProfileError" class="alert alert-danger py-2 small d-none"></div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Full Name</label>
                    <input type="text" id="editName" class="form-control"
                           value="<?= htmlspecialchars($user['user_name']) ?>">
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold small">Email Address</label>
                    <input type="email" id="editEmail" class="form-control"
                           value="<?= htmlspecialchars($user['email']) ?>">
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-dark" id="btnSaveProfile">Save Changes</button>
            </div>
        </div>
    </div>
</div>


<!-- ================================================================
     CHANGE PASSWORD MODAL
================================================================= -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title mb-0">Change Password</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="changePwError" class="alert alert-danger py-2 small d-none"></div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Current Password</label>
                    <div class="input-group">
                        <input type="password" id="currentPassword" class="form-control"
                               placeholder="Your current password">
                        <button class="btn btn-outline-secondary toggle-pw-modal" type="button"
                                data-target="currentPassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">New Password</label>
                    <div class="input-group">
                        <input type="password" id="newPassword" class="form-control"
                               placeholder="Min. 8 characters">
                        <button class="btn btn-outline-secondary toggle-pw-modal" type="button"
                                data-target="newPassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold small">Confirm New Password</label>
                    <input type="password" id="confirmPassword" class="form-control"
                           placeholder="Repeat new password">
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-dark" id="btnChangePassword">Update Password</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {

    // ----------------------------------------------------------------
    // Edit Profile
    // ----------------------------------------------------------------
    document.getElementById('btnSaveProfile').addEventListener('click', function () {
        var btn   = this;
        var name  = document.getElementById('editName').value.trim();
        var email = document.getElementById('editEmail').value.trim();
        var errEl = document.getElementById('editProfileError');

        errEl.classList.add('d-none');

        // Client-side pre-check (server validates too, this just saves a round-trip)
        if (name.length < 2) { showErr(errEl, 'Name must be at least 2 characters.'); return; }
        if (!email)          { showErr(errEl, 'Email is required.');                   return; }

        btn.disabled    = true;
        btn.textContent = 'Saving…';

        $.post(BASE_URL + '/user/updateProfile', { name: name, email: email }, function (res) {
            if (res.success) {
                // Update the page without reloading — navbar still shows old name
                // until next full load, but profile page reflects it immediately
                document.getElementById('profileDisplayName').textContent  = res.name || name;
                document.getElementById('profileDisplayEmail').textContent = email;

                bootstrap.Modal.getInstance(document.getElementById('editProfileModal')).hide();
                showFeedback('Profile updated successfully.', 'success');
            } else {
                showErr(errEl, res.message || 'Update failed.');
            }
        }, 'json')
        .fail(function () { showErr(errEl, 'Network error. Please try again.'); })
        .always(function () {
            btn.disabled    = false;
            btn.textContent = 'Save Changes';
        });
    });

    // ----------------------------------------------------------------
    // Change Password
    // ----------------------------------------------------------------
    document.getElementById('btnChangePassword').addEventListener('click', function () {
        var btn     = this;
        var current = document.getElementById('currentPassword').value;
        var newPw   = document.getElementById('newPassword').value;
        var confirm = document.getElementById('confirmPassword').value;
        var errEl   = document.getElementById('changePwError');

        errEl.classList.add('d-none');

        if (!current || !newPw || !confirm) { showErr(errEl, 'All fields are required.');                         return; }
        if (newPw.length < 8)               { showErr(errEl, 'New password must be at least 8 characters.');     return; }
        if (newPw !== confirm)              { showErr(errEl, 'New passwords do not match.');                      return; }

        btn.disabled    = true;
        btn.textContent = 'Updating…';

        $.post(BASE_URL + '/user/changePassword', {
            current_password: current,
            new_password:     newPw,
            confirm_password: confirm,
        }, function (res) {
            if (res.success) {
                document.getElementById('currentPassword').value = '';
                document.getElementById('newPassword').value     = '';
                document.getElementById('confirmPassword').value = '';

                bootstrap.Modal.getInstance(document.getElementById('changePasswordModal')).hide();
                showFeedback('Password changed successfully.', 'success');
            } else {
                showErr(errEl, res.message || 'Update failed.');
            }
        }, 'json')
        .fail(function () { showErr(errEl, 'Network error. Please try again.'); })
        .always(function () {
            btn.disabled    = false;
            btn.textContent = 'Update Password';
        });
    });

    // ----------------------------------------------------------------
    // Password visibility toggles (show/hide eye icon)
    // ----------------------------------------------------------------
    document.querySelectorAll('.toggle-pw-modal').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input  = document.getElementById(this.dataset.target);
            var icon   = this.querySelector('i');
            var hidden = input.type === 'password';
            input.type     = hidden ? 'text'  : 'password';
            icon.className = hidden ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------
    function showErr(el, msg) {
        el.textContent = msg;
        el.classList.remove('d-none');
    }

    function showFeedback(msg, type) {
        var el = document.getElementById('profileFeedback');
        el.className   = 'alert alert-' + type + ' py-2 mb-3';
        el.textContent = msg;
        // Auto-dismiss after 4 seconds
        setTimeout(function () { el.classList.add('d-none'); }, 4000);
    }

})();
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
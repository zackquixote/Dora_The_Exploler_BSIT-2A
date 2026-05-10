<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name"  content="<?= csrf_token() ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BMIS | <?= isset($title) ? esc($title) : 'Dashboard' ?></title>

    <!-- Design System -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bmis-design-system.css') ?>">

    <!-- AdminLTE (kept for Bootstrap grid/utilities + DataTables compat) -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') ?>">

    <!-- External CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/toastr/toastr.min.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Override AdminLTE defaults with design system -->
    <style>
        .main-sidebar, .main-header, .content-wrapper, .main-footer { display:none !important; }
        body { overflow-x: hidden; }
    </style>
</head>

<body>

    <?= $this->include('theme/navbar') ?>
    <?= $this->include('theme/sidebar') ?>

    <main class="bmis-main">
        <?= $this->renderSection('content') ?>
    </main>

    <footer style="margin-left:var(--sidebar-w);padding:16px 24px;font-size:11px;color:var(--ink-soft);border-top:.5px solid var(--border);background:var(--white);">
        <strong>Copyright &copy; <?= date('Y') ?> <a href="#" style="color:var(--c-teal);text-decoration:none;">Glenn IT Solutions</a></strong>
        &nbsp;·&nbsp; BMIS v2.0
    </footer>

<!-- Scripts -->
<script>window.baseUrl = '<?= base_url() ?>' + (('<?= base_url() ?>'.endsWith('/')) ? '' : '/');</script>
<script src="<?= base_url('assets/adminlte/plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

<!-- DataTables -->
<script src="<?= base_url('assets/adminlte/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') ?>"></script>

<!-- External -->
<script defer src="<?= base_url('assets/adminlte/plugins/toastr/toastr.min.js') ?>"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer src="<?= base_url('js/blotter/notifications.js') ?>"></script>

<?= $this->renderSection('scripts') ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('mainSidebar');
    if (window.innerWidth <= 768 && sidebar) {
        const toggler = document.createElement('button');
        toggler.innerHTML = '<i class="fas fa-bars"></i>';
        toggler.className = 'tb-icon-btn';
        toggler.style.cssText = 'position:fixed;top:10px;left:10px;z-index:1050;';
        toggler.onclick = () => sidebar.classList.toggle('open');
        document.body.appendChild(toggler);
    }
});

$(document).ajaxSend(function(e, xhr, options) {
    if (options.type && options.type.toUpperCase() === 'POST') {
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    }
});

$(document).on('submit', 'form:not([data-confirm])', function() {
    const $form = $(this);
    if ($form.data('submitted') === true) return false;
    $form.data('submitted', true);
});

// Toast Notifications
<?php if (session()->getFlashdata('success')): ?>
    toastr.success('<?= esc(session()->getFlashdata('success')) ?>');
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    toastr.error('<?= esc(session()->getFlashdata('error')) ?>');
<?php endif; ?>

// SweetAlert Confirmations
$(document).on('submit', 'form[data-confirm]', function(e) {
    e.preventDefault();
    const $form = $(this);
    const title = $form.attr('data-confirm') || "Are you sure?";
    
    Swal.fire({
        title: title,
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'var(--c-rose)',
        cancelButtonColor: 'var(--ink-soft)',
        confirmButtonText: 'Yes, proceed!'
    }).then((result) => {
        if (result.isConfirmed) {
            $form.removeAttr('data-confirm');
            $form.submit();
        }
    });
    });
});

// Global Search Logic
if (window.innerWidth > 768) {
    const searchContainer = document.getElementById('desktop-search-container');
    if (searchContainer) searchContainer.style.display = 'block';
}

const $searchInput = $('#globalSearchInput');
const $searchResults = $('#globalSearchResults');
const $searchBody = $('#globalSearchBody');

// Ctrl+K to focus search
document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        $searchInput.focus();
    }
});

let searchTimeout;
$searchInput.on('input', function() {
    clearTimeout(searchTimeout);
    const query = $(this).val().trim();
    
    if (query.length < 2) {
        $searchResults.hide();
        return;
    }
    
    searchTimeout = setTimeout(() => {
        $searchBody.html('<div style="text-align:center;padding:12px;color:var(--ink-soft)"><i class="fas fa-spinner fa-spin"></i> Searching...</div>');
        $searchResults.show();
        
        $.get(window.baseUrl + 'api/search', { q: query }, function(data) {
            if (data.length === 0) {
                $searchBody.html('<div style="text-align:center;padding:12px;color:var(--ink-soft)">No results found for "'+query+'"</div>');
                return;
            }
            
            let html = '';
            data.forEach(item => {
                html += `
                    <a href="${item.url}" style="display:flex;align-items:center;gap:12px;padding:8px;text-decoration:none;border-radius:8px;transition:background .2s;color:var(--ink)" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background='transparent'">
                        <div style="width:32px;height:32px;border-radius:50%;background:var(--c-${item.color}-bg);color:var(--c-${item.color});display:flex;align-items:center;justify-content:center;font-size:12px">
                            <i class="fas ${item.icon}"></i>
                        </div>
                        <div>
                            <div style="font-size:12.5px;font-weight:600">${item.title}</div>
                            <div style="font-size:10.5px;color:var(--ink-muted)">${item.type} • ${item.desc}</div>
                        </div>
                    </a>
                `;
            });
            $searchBody.html(html);
        });
    }, 300);
});

// Close search on click outside
$(document).on('click', function(e) {
    if (!$(e.target).closest('#desktop-search-container').length) {
        $searchResults.hide();
    }
});
</script>
</body>
</html>
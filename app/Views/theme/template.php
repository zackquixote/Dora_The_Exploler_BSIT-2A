<!DOCTYPE html>
<html lang="en">
<?= $this->include('theme/partials/head') ?>

<body>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-theme');
        }
    </script>

    <a href="#main-content" class="skip-link">Skip to Content</a>

    <?= $this->include('theme/navbar') ?>
    <?= $this->include('theme/sidebar') ?>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <main class="bmis-main" id="main-content" tabindex="-1">
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('theme/partials/footer') ?>
    <?= $this->include('theme/partials/scripts') ?>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
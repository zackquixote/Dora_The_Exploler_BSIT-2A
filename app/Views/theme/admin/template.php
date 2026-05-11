<!DOCTYPE html>
<html lang="en">
<?= $this->include('theme/partials/head') ?>

<body>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-theme');
        }
    </script>

    <?= $this->include('theme/navbar') ?>
    <?= $this->include('theme/sidebar') ?>

    <main class="bmis-main">
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('theme/partials/footer') ?>
    <?= $this->include('theme/partials/scripts') ?>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
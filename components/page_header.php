<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10"><?= isset($pageHeader) ? $pageHeader : 'Dashboard' ?></h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
            <?php if(isset($breadcrumbs) && is_array($breadcrumbs)) : ?>
                <?php foreach($breadcrumbs as $crumb) : ?>
                    <li class="breadcrumb-item"><?= $crumb ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="breadcrumb-item"><?= isset($pageHeader) ? $pageHeader : 'Dashboard' ?></li>
            <?php endif; ?>
        </ul>
    </div>
</div>
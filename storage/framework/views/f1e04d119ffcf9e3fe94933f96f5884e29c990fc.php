<?php $__env->startSection('template_title'); ?>
    <?php echo e(trans('installer_messages.final.templateTitle')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('title'); ?>
    <i class="fa fa-flag-checkered fa-fw" aria-hidden="true"></i>
    <?php echo e(trans('installer_messages.final.title')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('container'); ?>
    <p style="color:red;"><strong>Default Super Admin Created : superadmin@example.com / 1234</strong></p>
    <p style="color:red;"><strong>Default Admin Created : owner@example.com / 1234</strong></p>

    <?php if(session('message')['dbOutputLog']): ?>
        <p><strong><small><?php echo e(trans('installer_messages.final.migration')); ?></small></strong></p>
        <pre><code><?php echo e(session('message')['dbOutputLog']); ?></code></pre>
    <?php endif; ?>

    <p><strong><small><?php echo e(trans('installer_messages.final.console')); ?></small></strong></p>
    <pre><code><?php echo e($finalMessages); ?></code></pre>

    <p><strong><small><?php echo e(trans('installer_messages.final.log')); ?></small></strong></p>
    <pre><code><?php echo e($finalStatusMessage); ?></code></pre>

    <p><strong><small><?php echo e(trans('installer_messages.final.env')); ?></small></strong></p>
    <pre><code><?php echo e($finalEnvFile); ?></code></pre>

    <div class="buttons">
        <a href="<?php echo e(url('/')); ?>" class="button"><?php echo e(trans('installer_messages.final.exit')); ?></a>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('vendor.installer.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\wetransfer_scripts-to-install-onn-your-local-machine_2022-05-23_0954\storego\main_file\resources\views/vendor/installer/finished.blade.php ENDPATH**/ ?>
<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Login')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('language-bar'); ?>
        <li class="nav-item">
            <select name="language" id="language" class="btn-primary custom_btn ms-2 me-2" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                <?php $__currentLoopData = App\Models\Utility::languages(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option <?php if($lang == $language): ?> selected <?php endif; ?> value="<?php echo e(route('login',$language)); ?>"><?php echo e(Str::upper($language)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="row align-items-center text-start">
        <div class="col-xl-6">
            <div class="card-body">
                <div class="">
                    <h2 class="mb-3 f-w-600"><?php echo e(__('Login')); ?></h2>
                </div>
                <form method="POST" action="<?php echo e(route('login')); ?>" class="needs-validation" novalidate="">
                    <?php echo csrf_field(); ?>
                    <div>
                        <div class="form-group mb-3">
                            <label class="form-label"><?php echo e(__('Email')); ?></label>
                            <input id="email" type="email" class="form-control  <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(__('E-Mail Address')); ?>" required autofocus>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="error invalid-email text-danger" role="alert">
                                    <small><?php echo e($message); ?></small>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label"><?php echo e(__('Password')); ?></label>
                            <input id="password" type="password" class="form-control  <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" placeholder="<?php echo e(__('Password')); ?>" required>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="error invalid-password text-danger" role="alert">
                                <small><?php echo e($message); ?></small>
                            </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <?php if(Route::has('password.request')): ?>
                            <div class="mt-2">
                                <a href="<?php echo e(route('password.request',$lang)); ?>" class="small text-muted text-underline--dashed border-primar"><?php echo e(__('Forgot Your Password?')); ?></a>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if(env('RECAPTCHA_MODULE') == 'yes'): ?>
                        <div class="form-group col-lg-12 col-md-12 mt-3">
                            <?php echo NoCaptcha::display(); ?>

                            <?php $__errorArgs = ['g-recaptcha-response'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="error small text-danger" role="alert">
                                <strong><?php echo e($message); ?></strong>
                            </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <?php endif; ?>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-block mt-2" tabindex="4"><?php echo e(__('Login')); ?></button>
                        </div>

                        <?php if(Utility::getValByName('signup_button')=='on'): ?>
                            <p class="my-4 text-center"><?php echo e(__("Don't have an account?")); ?>

                                <a href="<?php echo e(route('register',$lang)); ?>" class="my-4 text-primary"><?php echo e(__('Register')); ?></a>
                            </p>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-xl-6 img-card-side">
            <div class="auth-img-content">
                <img src="<?php echo e(asset('assets/images/auth/img-auth-3.svg')); ?>" alt="" class="img-fluid">
                <h3 class="text-white mb-4 mt-5"> <?php echo e(__('“Attention is the new currency”')); ?></h3>
                <p class="text-white"> <?php echo e(__('The more effortless the writing looks, the more effort the writer
                    actually put into the process.')); ?></p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('custom-scripts'); ?>
<script src="<?php echo e(asset('custom/libs/jquery/dist/jquery.min.js')); ?>"></script>
<script>
$(document).ready(function () {
  $(".form_data").submit(function (e) {
      $(".login_button").attr("disabled", true);
      return true;
  });
});
</script>
<?php if(env('RECAPTCHA_MODULE') == 'yes'): ?>
        <?php echo NoCaptcha::renderJs(); ?>

<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\wetransfer_scripts-to-install-onn-your-local-machine_2022-05-23_0954\storego\main_file\resources\views/auth/login.blade.php ENDPATH**/ ?>
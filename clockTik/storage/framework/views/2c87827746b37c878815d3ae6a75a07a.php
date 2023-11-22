<?php $__env->startSection('title'); ?>
    <h2>User Registration</h2>
    <?php if(isset($exists)): ?>
        <p class="emailExists"><?php echo e($exists); ?></p>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('error'); ?>
<div class="loginError">
    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
    <p id='errName' class="text-danger"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    
    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
    <p id='errEmail' class="text-danger"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    
    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
    <div class="text-danger"><?php echo e($message); ?></div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('newUser'); ?>

    <form class="newUserForm" name="newUserForm" action="<?php echo e(route('registrate')); ?>" method="post">

        <?php echo csrf_field(); ?>
        <label class="nameLabel" for="name">Name</label>
        <input id="name" class="name" type="text" name="name">
        

    

        <label class="emailLabel" for="email">Email</label>
        <input id="email_adress" class="email" type="email" name="email">

       

        <label class="passwordLabel" for="password">Password</label>
        <input id="password"  class="password" type="password" name="password">
       
        <label class="passwordConfirmationLabel" for="password_confirmation">Repeat password</label>
        <input  class="passwordConfirmation" type="password" name="password_confirmation">

      

        <input class="registrationButton" type="submit" value="Registrate">

    </form>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Gebruiker\Documents\Toon\Developing\php\Beterams-php\clockTik\resources\views/registration-form.blade.php ENDPATH**/ ?>
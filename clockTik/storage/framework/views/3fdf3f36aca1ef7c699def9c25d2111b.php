<?php $currentUser = auth()->user();?>
<?php $__env->startSection('title'); ?>
    <h1>ClockTik</h1>
    <?php if(isset($currentUser)): ?>
    <p><?php echo e($currentUser->name); ?></p>   
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('error'); ?>

   
<?php if($errors->any()): ?>
<div class="loginError">
    <ul>
<?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <li><?php echo e($error); ?></li>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>  
    </ul>  
</div>    
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('login'); ?>
   
        <form class="loginForm" action="<?php echo e(route('authentication')); ?>" method="post">
            <?php echo csrf_field(); ?>
            <label class="userNameLabel" for="email">Email adress</label>
            <input class="userName" type="email" id='email' name="email" >
            <label class="passLabel" for="password">Password</label>
            <input class="pass" type="password" id="password" name="password">
            <input class="loginButton" type="submit" value="Login">
            <a class='registerLink'href="<?php echo e(route('registration-form')); ?>">Register</a>
            <label class="rememberLabel" for="remember">Remember me</label>
            <input id="remember" type="checkbox" name="remember" class="remeberCheckbox">
        </form>
    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Gebruiker\Documents\Toon\Developing\php\Beterams-php\clockTik\resources\views/login.blade.php ENDPATH**/ ?>
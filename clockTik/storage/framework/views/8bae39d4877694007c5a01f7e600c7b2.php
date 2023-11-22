<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@100;200;300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

     <!-- <link rel="stylesheet" href="<?php echo e(URL::asset('resources/css/app.css')); ?>">  -->
     <!-- <link rel="stylesheet" href="<?php echo e(mix('resources/css/app.css')); ?>"> -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <title>clockTik</title>
</head>

<body>
    <?php $currentUser = auth()->user(); ?>
    <div class="bodyContent">

        <?php echo $__env->yieldContent('error'); ?>
        <?php echo $__env->yieldContent('content'); ?>
        <?php echo $__env->yieldContent('title'); ?>
        
        <header>
            <?php echo $__env->yieldContent('header'); ?>
            <a class="headerLinks" href="<?php echo e(route('home')); ?>">Home</a>
            <?php if(auth()->guard()->check()): ?>
            <?php if($currentUser->admin == false): ?>
                <a class="headerLinks" href="<?php echo e(route('dashboard')); ?>">Timeclock</a>
                <a class="authLinks"href="<?php echo e(route('myProfile')); ?>">Mijn profiel</a>
                <?php else: ?>
                <a class="authLinks" href = "<?php echo e(route('myWorkers')); ?>">Personeel</a>
                <?php endif; ?>
                <a class="authLinks" href="<?php echo e(route('logout')); ?>">Logout</a>
            <?php endif; ?>
            <?php if(auth()->guard()->guest()): ?>
                <a class="authLinks" href="<?php echo e(route('login')); ?>">Login</a>
            <?php endif; ?>
        </header>

        <?php echo $__env->yieldContent('login'); ?>
        <?php echo $__env->yieldContent('userDashboard'); ?>
        <?php echo $__env->yieldContent('newUser'); ?>

        <footer> &copy Toon De Booser</footer>
    </div>
</body>

</html>
<?php /**PATH C:\Users\Gebruiker\Documents\Toon\Developing\php\Beterams-php\clockTik\resources\views/layout.blade.php ENDPATH**/ ?>
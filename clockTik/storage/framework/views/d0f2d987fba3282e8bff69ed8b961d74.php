<?php $__env->startSection('title'); ?>
    <h2>Welcome <?php echo e($user->name); ?></h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('userDashboard'); ?>

<?php if(session('error')): ?>
<div class="error">
    <?php echo e(session('error')); ?>

    <a class="removeError" href="">ok</a>
</div>
<?php endif; ?>
    <?php if($shiftStatus == false): ?>
        <a href="<?php echo e(route('start')); ?>" class="startButton">
            <p class="buttonText">Start</p>
        </a>
    <?php else: ?>
        <?php if($shiftStatus !== false): ?>
          <form method="POST" name="userNoteForm" action="<?php echo e(route('dashboard')); ?>" class="userNoteForm">
                <?php echo csrf_field(); ?>
            <textarea class="userNoteInput" name="userNote" rows="2" cols="30"><?php echo e($userNote); ?></textarea> <br>
            <input class="userNoteSubmit" type="submit" value='Voeg notitie toe'>
            </form>
        <?php endif; ?>
        <?php if($shiftStatus == true && $breakStatus == false): ?>
            <a onclick="return confirm('Are you sure you want to take a break?')" href="<?php echo e(route('break')); ?>"
                class="breakButton">
                <p class="buttonText">Break</p>
            </a>
            <a onclick="return confirm('Are you sure you want to quit your shift?')" href="<?php echo e(route('stop')); ?>"
                class="stopButton">
                <p class="buttonText">Stop</p>
            </a>
          
        <?php else: ?>
            <?php if($shiftStatus == true && $breakStatus == true): ?>
                <a onclick="return confirm('Are you sure you want to start working again?')" href="<?php echo e(route('stopBreak')); ?>"
                    class="breakButton">
                    <p class="buttonText">Back to work</p>
                </a>

                <a onclick="return confirm('Are you sure you want to quit your shift?')" href="<?php echo e(route('stop')); ?>"
                    class="stopButton">
                    <p class="buttonText">Stop</p>
                </a>
            <?php else: ?>
                <div class="text-danger">Something went wrong pls contact tech guy.</div>
            <?php endif; ?>
        <?php endif; ?>

        <?php endif; ?>
        
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Gebruiker\Documents\Toon\Developing\php\Beterams-php\clockTik\resources\views/dashboard.blade.php ENDPATH**/ ?>
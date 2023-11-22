<?php $__env->startSection('content'); ?>
<h2>Nieuw rooster voor: <?php echo e($worker->name); ?></h2>
<?php if(session('error')): ?>
<div class="error"><?php echo e(session('error')); ?> <br>
    <a class="removeError" href=""> ok </a>
</div>
<?php endif; ?>
<form action="<?php echo e(route('newTimesheet')); ?>" class="addNewTimesheetForm" method="POST">
<?php echo csrf_field(); ?>
<input class="newTimesheetDate" type="date" name="newTimesheetDate">
<input class="newTimesheetInput" type="hidden" name="workerId" value="<?php echo e($id); ?>">
<input class="newTimesheetInput" type="time" name="startTime">
<input class="newTimesheetInput" type="time" name="endTime" >
<input class="userNoteSubmit" type="submit" value="Voeg toe">
</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Gebruiker\Documents\Toon\Developing\php\Beterams-php\clockTik\resources\views/addTimesheet.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <h2>Update rooster van: <?php echo e($worker->name); ?></h2>
    <?php
    $specialDays = ['Ziek', 'Weerverlet', 'Onbetaald verlof', 'Betaald verlof', 'Feestdag', 'Solicitatie verlof'];
   if ($timesheet === null) {
    header('Location: /my-workers');
    exit;
}
    ?>
    <div class="formContainer">
        <h3><?php echo e($monthString); ?></h3>
        <form action="<?php echo e(route('updateTimesheet')); ?>" class="updateTimesheet" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo e($worker->id); ?>">
            <input type="hidden" name="timesheet" value="<?php echo e($timesheet->id); ?>">
            <input type="hidden" name="type" value="<?php echo e($timesheet->type); ?>">

            <?php if($timesheet->type == 'workday'): ?>
                <fieldset>
                    <legend>Gewerkte periode</legend>
                    <div>
                        <label for="startTime">Start:</label>
                        <input class="updateStartTime" name="startTime" type="time" value="<?php echo e($startShift); ?>">
                        <label for="endTime">End:</label>
                        <input class="updateEndTime" type="time" name="endTime" value="<?php echo e($endShift); ?>">
                    </div>
                </fieldset>
                <legend>Gepauzeerde periode</legend>
                <div>
                    <label for="startBreak">Start:</label>
                    <input class="updateStartBreak" type="time" name="startBreak" value="<?php echo e($startBreak); ?>">
                    <label for="endBreak">End:</label>
                    <input type="time" name="endBreak" class="updateEndBreak" value="<?php echo e($endBreak); ?>">
                </div>
                </fieldset>
                <?php else: ?>

            <select name="updateSpecial" size="1">
                <?php $__currentLoopData = $specialDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $specialDay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($specialDay); ?>" <?php echo e($specialDay == $timesheet->type? 'selected':''); ?>><?php echo e($specialDay); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php endif; ?>
            <input class="updateTimesheetSubmit" type="submit" value="update">
        </form>
    </div>
    <form action="<?php echo e(route('delete')); ?>" class="delete" method="POST">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="workerId" value="<?php echo e($worker->id); ?>">
        <input type="hidden" name="deleteSheet" value="<?php echo e($timesheet->id); ?>">
        <input type="hidden" name="date" value="<?php echo e($timesheet->Month); ?>">
        <input onclick="return confirm('zedde zeker ?')" class="submit" type="image" src="<?php echo e(asset('/images/1843344.png')); ?>"
                    name="deleteThisSheet" alt="Delete">
    </form>
    <?php if($timesheet->userNote !== null): ?>
        <fieldset class="userNoteContainer">
            <div><b>Notitie:</b></div>
            <div class="userNote"><?php echo e($timesheet->userNote); ?></div>
        </fieldset>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Gebruiker\Documents\Toon\Developing\php\Beterams-php\clockTik\resources\views/updateTimesheet.blade.php ENDPATH**/ ?>
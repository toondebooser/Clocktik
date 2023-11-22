<?php $__env->startSection('content'); ?>
    <?php if(isset($setForTimesheet) && $setForTimesheet == true): ?>
        <h2>Uurroosters</h2>
    <?php else: ?>
        <h2>Voor wie ?</h2>
    <?php endif; ?>

    <div class="workersForm">
        <?php $__currentLoopData = $workers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $worker): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!$worker->admin): ?>
                <form class='workerForm'
                    action="
        <?php if(isset($setForTimesheet) && $setForTimesheet == true): ?> <?php echo e(route('getData')); ?>

        <?php elseif(isset($setForTimesheet) && $setForTimesheet == false): ?>
        <?php echo e(route('specials')); ?> <?php endif; ?>
        "
                    method="post">
                    <?php echo csrf_field(); ?>
                    <button class='workerButton' type="submit" name='worker' value="<?php echo e($worker->id); ?>">
                        <?php echo e($worker->name); ?>

                        <?php switch(true):
                            case ($worker->timelogs[0]->ShiftStatus == true && $worker->timelogs[0]->BreakStatus == false): ?>
                                <div class="working"></div>
                            <?php break; ?>

                            <?php case ($worker->timelogs[0]->ShiftStatus == true && $worker->timelogs[0]->BreakStatus == true): ?>
                                <div class="onBreak"></div>
                            <?php break; ?>

                            <?php default: ?>
                                <div class="notWorking"></div>
                        <?php endswitch; ?>
                    </button>
                </form>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if(isset($setForTimesheet) && $setForTimesheet == false): ?>
            <form class="workerForm" method="post" action="<?php echo e(route('specials')); ?>">
                <?php echo csrf_field(); ?>
                <button class="workerButton" type="submit" name="worker" value="<?php echo e($workers); ?>">
                    Voor iedereen</button>
            </form>
        <?php endif; ?>
    </div>
    <?php if(isset($setForTimesheet) && $setForTimesheet == true): ?>
        <a href="<?php echo e(route('forWorker')); ?>" class='specialsButton'>Dagen instellen</a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Gebruiker\Documents\Toon\Developing\php\Beterams-php\clockTik\resources\views/my-workers.blade.php ENDPATH**/ ?>
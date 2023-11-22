<?php $__env->startSection('title'); ?>
<script>
    <?php if($user->admin): ?>
        window.location.href = "<?php echo e(route('myWorkers')); ?>";
    <?php endif; ?>
</script>

    <?php
        $userId = $user->id;
        if(isset($timesheet[0]))$month = $timesheet[0]->Month;
        // $totalJSONstring = json_encode($monthlyTotal);
        
    ?>
    <h2>
        <?php echo e($user->name); ?>

    </h2>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('userDashboard'); ?>
    <?php if(session('error')): ?>
        <div class="error">
            <?php echo e(session('error')); ?> <br>
            <a class="removeError" href=""> ok </a>
        </div>
    <?php endif; ?>

    <div class="profileContent">
        <form class="timesheetForm" method="POST" action="<?php echo e(route('getData')); ?>">
            <?php echo csrf_field(); ?>
            <select class="dropDownMonth" name="month" size="1">
                <?php $__currentLoopData = $clockedMonths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allMonths): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $currentMonth = \Carbon\Carbon::now()->month;
                        
                    ?>
                    <option value="<?php echo e($allMonths->month); ?>" <?php echo e($allMonths->month == $currentMonth ? 'selected' : ''); ?>>
                        <?php
                            $carbonDate = \Carbon\Carbon::create(null, $allMonths->month, 1);
                            echo $carbonDate->format('F');
                        ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php if(isset($user)): ?>
                <input type="hidden" name="worker" value='<?php echo e($user->id); ?>'>
            <?php endif; ?>
            <input class="getMonthButton" type="submit" value="Ga naar maand">
        </form>
        <?php if(auth()->user()->admin == true): ?>
            <form class="dagenInstellen" action="<?php echo e(route('specials')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="worker" value="<?php echo e($user->id); ?>">
                <input class="submit" type="image"
                    src="<?php echo e(asset('/images/2849830-gear-interface-multimedia-options-setting-settings_107986.png')); ?>"
                    name="submitUserId" alt="Submit">
            </form>
            <form class="timesheetToevoegen" action="<?php echo e(route('timesheetForm')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="worker" value="<?php echo e($user->id); ?>">
                <input class="submit" type="image" src="<?php echo e(asset('/images/image_processing20210616-17152-dcj4lq.png')); ?>"
                    name="submitUserId" alt="Submit">
            </form>
        <?php endif; ?>
        <div class="timesheetHeader">

            <?php if(isset($timesheet) && count($timesheet) != 0): ?>
                <?php echo e(date('F', strtotime($timesheet[0]->Month))); ?>

            <?php endif; ?>
        </div>
        <table class="timesheetTable">
            <thead class="stikyHeader">
                <tr>
                    <th>Date</th>
                    <th>Regular hours</th>
                    <th>Break hours</th>
                    <th>Overtime</th>
                </tr>
            </thead>
            <?php if($timesheet->count() > 0): ?>
                <?php if(auth()->user()->admin == true): ?>
                <a class="previewLink"
                    href="<?php echo e(route('exportPdf', ['userId' => $userId, 'month' => $month, 'type' => 'preview'])); ?>"
                    target="_blank">
                    <img class="previewIcon" src="<?php echo e(asset('/images/preview-65.png')); ?>" alt="Preview">
                </a>
                <a class="downloadLink"
                    href="<?php echo e(route('exportPdf', ['userId' => $userId, 'month' => $month, 'type' => 'download'])); ?>">
                    <img class="downloadIcon" src="<?php echo e(asset('/images/2021663-200.png')); ?>" alt="Download">
                </a>
                <?php endif; ?>
                <?php $__currentLoopData = $timesheet; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="timesheetRow">
                        <td class="date" id="<?php echo e($item->id); ?>">
                            <a class='displayDay'href="<?php echo e(route('update', ['id' => $user->id, 'timesheet' => $item])); ?>">
                                <?php
                                $toTime = strtotime($item->ClockedIn);
                                $days = ['Mon' => 'Ma', 'Tue' => 'Di', 'Wed' => 'Wo', 'Thu' => 'Do', 'Fri' => 'Vr', 'Sat' => 'Za', 'Sun' => 'Zo'];
                                $englishDay = date('D', $toTime);
                                $dutchDay = $days[$englishDay];
                                $dayOfMonth = date('d', $toTime);
                                echo $dutchDay . ' ' . $dayOfMonth;
                                ?>
                        </a>
                            <?php if($item->userNote !== null): ?>
                            <img class="noteIcon"src="<?php echo e(asset('/images/148883.png')); ?>" alt="Icon">
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="displayRegular">
                                <?php if($item->RegularHours < 7.6 && $item->Weekend == false && $item->type == 'workday'): ?>
                                    <s><?php echo e($item->RegularHours); ?></s>
                                    => 7.60
                                <?php elseif($item->Weekend == true && $item->type == 'workday'): ?>
                                    Weekend
                                <?php elseif($item->Weekend == false && $item->type !== 'workday'): ?>
                                    <?php echo e($item->type); ?>

                                <?php else: ?>
                                    <?php echo e($item->RegularHours); ?>

                                <?php endif; ?>


                            </div>
                        </td>
                        <td>
                            <div class="displayBreak">
                                
                                <?php echo e($item->BreakHours); ?>

                                
                                
                            </div>
                        </td>
                        <td>
                            <div class="displayOvertTime">
                                <?php echo e($item->OverTime); ?>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <p class="text-danger">No data</p>
            <?php endif; ?>

        </table>
    </div>
    <?php if(isset($monthlyTotal)): ?>
        <?php $__currentLoopData = $monthlyTotal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="displayTotalRegular">
                Regular <?php echo e($item->RegularHours); ?>

            </div>
            <div class="displayTotalBreak">
                Break <?php echo e($item->BreakHours); ?>

            </div>
            <div class="displayTotalOverTime">
                Overtime <?php echo e($item->OverTime); ?>

            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <div class="text-danger">Something went wrong pls call Toon.</div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Gebruiker\Documents\Toon\Developing\php\Beterams-php\clockTik\resources\views/profile.blade.php ENDPATH**/ ?>
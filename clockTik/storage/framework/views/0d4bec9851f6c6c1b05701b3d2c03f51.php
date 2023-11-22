<?php $__env->startSection('content'); ?>
    <h2 class='instellenVoor'>Instellen voor <?php echo e($forWho); ?></h2>

    <?php if(session('error')): ?>
        <div class="error">
            <?php $__currentLoopData = session('error'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userError): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $findUser = \App\Models\User::find($userError['id']);
                    $findUser? $user = $findUser->name: $user = 'iedereen'
                ?>
                <p class='specifiedError'>Voor uurrooster van: <?php echo e($user); ?></p>
                <?php echo e($userError['errorList']); ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <a class="removeError" href="">ok</a>
        </div>
    <?php elseif(session('errors')): ?>
        <div class="error">
            <?php $__currentLoopData = session('errors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userError): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $findUser = \App\Models\User::find($userError['id']);
                    $findUser? $user = $findUser->name: $user = 'iedereen'
                ?>
                <p class="specifiedError">Voor uurrooster van: <?php echo e($user); ?></p>
                <?php $__currentLoopData = $userError['errorList']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($error); ?><br>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <br>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <a class="removeError" href="">ok</a>
    </div>
    <?php endif; ?>
    <div class="specialDays">
        <?php if(isset($specialDays)): ?>
            <form action="<?php echo e(route('setSpecial')); ?>" method="POST" class="specialDayForm">
                <?php echo csrf_field(); ?>
                <span class="radioInput">
                    <?php $__currentLoopData = $specialDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $specialDay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="checkboxContainer" for="<?php echo e($specialDay); ?>">
                            <input class="radioBox" type="radio" id='<?php echo e($specialDay); ?>' name="specialDay"
                                value="<?php echo e($specialDay); ?>" <?php if($loop->first): ?> required <?php endif; ?>>
                            <?php echo e($specialDay); ?>

                            <span class="checkMark"></span>
                        </label>
                        <br>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </span>
                <input type="hidden" name="worker" value="<?php echo e($worker); ?>">
                <span class="dateInput">
                    <input type="date" name="singleDay" id="singleDayInput"> <br>
                    <input class="dagSubmit" type="submit" name="submitType" value="Dag Toevoegen"><br>
                    <hr>

                    <input class="startDateInput" type="date" name="startDate" id="startDateInput">
                    <input class="endDateInput" type="date" name="endDate" id="endDateInput"><br>
                    <input class="periodeSubmit" type="submit" name="submitType" value="Periode Toevoegen">
            </form>
            </span>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Gebruiker\Documents\Toon\Developing\php\Beterams-php\clockTik\resources\views/specials.blade.php ENDPATH**/ ?>
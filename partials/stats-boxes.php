<div class="row">
    <div class="col-lg-4 col-md-6 col-12">
        <!-- small box -->
        <div class="small-box bg-primary">
            <div class="inner">
                <h3><?php echo $crud->statsBoxes()['numStudent']; ?></h3>

                <p>Students</p>
            </div>
            <div class="icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <a href="<?php echo 'index.php?page=students'; ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-md-6 col-12">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?php echo $crud->statsBoxes()['numTeacher']; ?></h3>

                <p>Teachers</p>
            </div>
            <div class="icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <a href="<?php echo 'index.php?page=teachers'; ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-md-6 col-12">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>53<sup style="font-size: 20px">%</sup></h3>

                <p>Fees Payment</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill"></i>
            </div>
            <a href="<?php echo 'index.php?page=fees'; ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>
<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">

                <div class="col-lg-12">
                    <div class="card alert">
                        <div class="card-header">
                            <h4>Filters</h4>
                        </div>
                        <div class="card-body">
                            <div class="horizontal-form-elements">
                                <form class="form-horizontal" method="get" action="<?php echo base_url('blacklist/blacklistIP'); ?>">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" name="global" value="<?php echo @$_GET['global'];  ?>" placeholder="Global Search">
                                                        </div>       
                                                    </div>       
                                                    <div class="col-lg-2">
                                                        <input type="submit" class="form-control btn btn-dark" >
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <input type="submit" name="reset" value="Reset" class="form-control btn btn-default" >
                                                    </div>
      
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /# card -->
                </div>

            </div>
            <!-- /# row -->
            <section id="main-content">
                <div class="row">
                    
                    <!-- /# column -->
                    <div class="col-lg-12">
                        <div class="card alert">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-10">
                                        <h4>Blacklist IP</h4>
                                    </div>
                                    <div class="col-lg-2">
                                        <a href="<?php echo base_url('blacklist/addEditBlacklistIP'); ?>" class="btn btn-info" style="margin-bottom: 5px; float: right; " ><i class="fa fa-plus" aria-hidden="true"></i> Add</a>    
                                    </div> 
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>IP</th>
                                                <th>Blacklisted By</th>
                                                <th>Added Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                            if(!empty($listArr)) {
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                    $i++;
                                            ?>
                                                <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <td><?php echo $curEntry['ip']; ?></td>
                                                    <td><?php echo $curEntry['added_by']; ?></td>
                                                    <td><?php echo date('d-m-Y H:i:s', strtotime($curEntry['created_at'])); ?></td>
                                                    <td>
                                                        <button class="btn btn-danger delete-blacklist-ip" data-id="<?=$curEntry['id']?>">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php }
                                            }
                                            ?>
                                            
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6" style="padding-top: 25px;">
                                        <div class="datatable_pageinfo"><?php echo @$pageinfo; ?></div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="paginate_links pull-right"><?php echo @$links; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <!-- /# row -->

            </section>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        var BASE_URL = '<?php echo base_url(); ?>';
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: false,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        $('.delete-blacklist-ip').click(function(){
            const blacklistIPId = $(this).data('id');

            if(blacklistIPId != '') {
                Swal.fire({
                    title: 'Are you sure delete this record?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    width: 400,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: BASE_URL + 'blacklist/deleteBlacklistIP',
                            type: 'post',
                            data: {
                                blacklistIPId: blacklistIPId
                            },
                            success: function(response) {
                                var response = JSON.parse(response);
                                if(response.result == 'success') {
                                    Toast.fire({
                                        icon: 'success',
                                        title: 'Deleted!'
                                    })
                                    setTimeout(() => {
                                            location.reload();
                                    }, 1000);
                                }
                            }
                        });
                    }
                })
            }
        });
    });
</script>

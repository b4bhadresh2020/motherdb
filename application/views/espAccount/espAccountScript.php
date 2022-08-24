<script type="text/javascript">
    var BASE_URL = '<?php echo base_url(); ?>';
    var CURRENT_IP = '';

    $(document).ready(function() {
        // get ip
        $.getJSON("https://api.ipify.org?format=json", function(data) {
            CURRENT_IP = data.ip;
        })

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

        // confirm popup when account status updated
        $('#switch-btn .checkbox').on('change', function() {
            let status = !$(this).is(':checked');
            let msg, accountStatus = "";
            let accountId = $(this).val();
            let esp = $(this).data('esp');
            const accountTable = getAccountTable(esp);

            if (status) {
                $(this).prop('checked', true);
                msg = 'please enter password to deactivate this account';
                accountStatus = 1;
            } else {
                $(this).prop('checked', false);
                msg = 'please enter password to activate this account'
                accountStatus = 0;
            }

            Swal.fire({
                title: msg,
                //input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
                confirmButtonColor: '#03A9F5',
                showLoaderOnConfirm: true,
                // preConfirm: (password) => {
                //     return $.post(BASE_URL + `EspAccount/verifyCredential`, { esp: esp, accountId: accountId, password: password })
                //     .then(response => {                        
                //         return response
                //     })
                // },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                console.log(result);
                //var ajxResult = JSON.parse(result.value)
                if (result.isConfirmed) {
                    //if (ajxResult.result == "success") {
                    $.ajax({
                        url: BASE_URL + 'EspAccount/updateStatus',
                        type: 'post',
                        data: {
                            accountId: accountId,
                            accountStatus: accountStatus,
                            accountTable: accountTable,
                            esp: esp,
                            ip: CURRENT_IP
                        },
                        success: function(response) {
                            var response = JSON.parse(response);
                            if (response.result == 'success') {
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Success!'
                                })
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            }
                        }
                    });
                    // } else {
                    //     Toast.fire({
                    //         icon: 'error',
                    //         title: 'Wrong password!'
                    //     })
                    // }
                }
            })
        });
    });

    function getAccountTable(esp) {
        var table = "";
        if (esp == 9) {
            table = 'mailjet_accounts';
        } else if (esp == 12) {
            table = 'ontraport_accounts';
        } else if (esp == 13) {
            table = 'active_campaign_accounts';
        } else if (esp == 14) {
            table = 'expert_sender_accounts';
        } else if (esp == 15) {
            table = 'clever_reach_accounts';
        } else if (esp == 16) {
            table = 'omnisend_accounts';
        } else if (esp == 5) {
            table = 'sendgrid_accounts';
        }
        return table;
    }
</script>
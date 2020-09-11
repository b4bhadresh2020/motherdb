<script type="text/javascript">
    var BASE_URL = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript">
    $(document).ready(function(){

        $('#uploadCsv').change( function(event) {
            
            $('#sucErrMsg').text('').removeClass('alert alert-danger');

            if($('#uploadCsv')[0].files.length >= 1){

                var fileName = $('#uploadCsv').val().split('\\').pop();
                var ext = $('#uploadCsv').val().split('.').pop().toLowerCase();

                if($.inArray(ext, ['csv']) == -1) {
                        
                    $('#sucErrMsg').text('Please select csv file only').addClass('alert alert-danger');
                    scrollToErrorSuccessMsg();
                    return false;

                }else{
                    //max size limit will be here
                    $('#uploadLable').text(fileName).css({'color':'#e6a1f2'});
                    
                }
                
            }else{
                $('#uploadLable').text('');
            }
            
        });


        $('.fieldsName').change(function() {
            
            var dataSelectId = $(this).attr("data-select-id");
            if($(this).is(":checked") == true) {
                $("#lookingFor_" + dataSelectId).prop("disabled", true);
                $("#lookingFor_" + dataSelectId).prop("checked", false);
            }else{
                $("#lookingFor_" + dataSelectId).prop("disabled", false);
            }
                    
        });

        $('#btn_insert').click(function(){
            
            $('#sucErrMsg').text('').removeClass('alert alert-danger');

            var uploadCsvVal = $('#uploadCsv').val();
            var groupName = $('#groupName').val();
            var keyword = $('#keyword').val();

            if (uploadCsvVal == '') {

                $('#sucErrMsg').text('Please select CSV file to upload').addClass('alert alert-danger');
                scrollToErrorSuccessMsg();
                return false;

            }else if (groupName == '') {

                $('#sucErrMsg').text('Please Enter Group Name').addClass('alert alert-danger');
                scrollToErrorSuccessMsg();
                return false;

            }else if (keyword == '') {

                $('#sucErrMsg').text('Please Enter Keyword').addClass('alert alert-danger');
                scrollToErrorSuccessMsg();
                return false;

            } else {

                var ext = $('#uploadCsv').val().split('.').pop().toLowerCase();

                if($.inArray(ext, ['csv']) == -1) {
                        
                    $('#sucErrMsg').text('Please select csv file only').addClass('alert alert-danger');
                    scrollToErrorSuccessMsg();
                    return false;

                }else{
                    //max size limit will be here

                    var fieldsNameArrText = [];
                    var fieldsNameArrVal = [];
                    var colNumber = [];
                    var isDupColNumber = 0;

                    $.each($(".fieldsName:checked"), function(){            

                        fieldsNameArrText.push($(this).attr('data-check-text'));
                        fieldsNameArrVal.push($(this).val());

                        var dataSelectId = $(this).attr('data-select-id');
                        var colNumberVal = $("#colNumber_" + dataSelectId).val(); 

                        if ($.inArray(colNumberVal, colNumber) !== -1) {
                            isDupColNumber = 1;
                        }else{
                            if (colNumberVal != '') {
                                colNumber.push(colNumberVal);    
                            }
                        }
                        

                    });
                    
                    if (colNumber.length == 0) {

                        $('#sucErrMsg').text('Please enter atleast one column').addClass('alert alert-danger');
                        scrollToErrorSuccessMsg();
                        return false;

                    }else if (isDupColNumber == 1) {

                        $('#sucErrMsg').text('Duplicate column number found').addClass('alert alert-danger');
                        scrollToErrorSuccessMsg();
                        return false;

                    }else if(colNumber.length < fieldsNameArrVal.length){
                        
                        $('#sucErrMsg').text('You have selected checkbox but did not write colum number').addClass('alert alert-danger');
                        scrollToErrorSuccessMsg();
                        return false;

                    }else{

                        var htmlTable = '';

                        htmlTable += '<div class="table-responsive">'; 
                            htmlTable += '<table class="table">';
                                htmlTable += '<thead>';
                                    htmlTable += '<tr>';
                                        htmlTable += '<th>Column Number</th>';
                                        htmlTable += '<th>Field Name</th>';
                                    htmlTable += '</tr>';
                                htmlTable += '</thead>';
                                htmlTable += '<tbody>';

                                    for (var i = 0; i < colNumber.length; i++) {

                                        var fn = fieldsNameArrText[i];

                                        htmlTable += '<tr>';
                                            htmlTable += '<td>' + colNumber[i] + '</td> ';
                                            htmlTable += '<td>' + fn + '</td> ';
                                        htmlTable += '</tr>';
                                    }
                                    
                                htmlTable += '</tbody>';
                            htmlTable += '</table>';
                        htmlTable += '</div>';

                        $('#confirmTable').html(htmlTable);
                        $("#progressBar").hide();
                        $("#btn_div").show();
                        $('#approvedRejectPop').modal('show');
                        return false;
                    }
                      
                }

            }

        }); 

    });


    function upload_csv(){
        
        $("#btn_div").hide();

        var fieldsName = [];
        var colNumber = [];
        var lookingFor = [];

        $.each($(".fieldsName:checked"), function(){            

            fieldsName.push($(this).val());

            var dataSelectId = $(this).attr('data-select-id');
            var colNumberVal = $("#colNumber_" + dataSelectId).val(); 

            colNumber.push(colNumberVal);

        });


        $.each($(".lookingFor:checked"), function(){            
            lookingFor.push($(this).val());
        });

        var groupName = $('#groupName').val();
        var keyword = $('#keyword').val();

        var search_against_groupName = $('#search_against_groupName').val();
        var search_against_keyword = $('#search_against_keyword').val();
        var search_against_country = $('#search_against_country').val();

        var formData = new FormData();
        formData.append('uploadCsv', $('#uploadCsv')[0].files[0]); // since this is your file input
        formData.append('fieldsName',JSON.stringify(fieldsName));
        formData.append('colNumber',JSON.stringify(colNumber));
        formData.append('lookingFor',JSON.stringify(lookingFor));
        formData.append('groupName',groupName);
        formData.append('keyword',keyword);
        formData.append('search_against_groupName',search_against_groupName);
        formData.append('search_against_keyword',search_against_keyword);
        formData.append('search_against_country',search_against_country);

        $.ajax({

            url: BASE_URL + 'enrichment/addEdit',
            type: "post",
            dataType: 'json',
            processData: false, // important
            contentType: false, // important
            data: formData,
            success: function(response) {

                var listArr = response; // json array

                if(listArr.err == 0)
                {
                    $('#approvedRejectPop').modal('hide');
                    $("#sucErrMsg").html(listArr.msg).addClass('alert alert-success');
                    scrollToErrorSuccessMsg();

                    var timeoutVar = setTimeout(function(){ 
                        clearTimeout(timeoutVar);
                        reloadCurrentPage();
                    }, 2000);
                }
                else
                {
                   $("#progressBar").hide();
                   $('#approvedRejectPop').modal('hide');
                   $("#sucErrMsg").html(listArr.msg).addClass('alert alert-danger');
                   scrollToErrorSuccessMsg();
                }
            },
            error: function(e) {
                /*console.log('e>>>',e);*/
                $("#progressBar").hide();
                $('#approvedRejectPop').modal('hide');
                $("#sucErrMsg").text("There is some problem occur. Please try again later.").addClass('alert alert-danger');
                scrollToErrorSuccessMsg();

                var timeoutVar = setTimeout(function(){ 
                    clearTimeout(timeoutVar);
                    reloadCurrentPage();
                }, 2000);
            },
            xhr: function(){ // file upload progress (%)
                //Get XmlHttpRequest object
                var xhr = $.ajaxSettings.xhr() ;

                //Set onprogress event handler
                xhr.upload.onprogress = function(data){

                var perc = Math.round((data.loaded / data.total) * 100);
                    $("#progressBar").show();
                    $("#progressbarStriped").css("width", perc+"%");
                    $("#percentage_span").text(perc+'%');
                    
                };
                return xhr ;
            }
        });

    }


    function reloadCurrentPage(){
        location.reload();
    }

    function scrollToErrorSuccessMsg(){
        window.scrollTo(0, $('#sucErrMsg')[0].offsetTop);
    }

    $(document).ready(function(){
        $("#approvedRejectPop").on("hidden.bs.modal", function () {
            if($('#countButton').is(":visible") == true){
                location.reload();
            }
        });
    });

</script>

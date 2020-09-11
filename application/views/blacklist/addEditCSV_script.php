<script type="text/javascript">
    var BASE_URL = '<?php echo base_url(); ?>';
    var max_size = '<?php echo MAX_SIZE; ?>';
</script>
<script type="text/javascript">
    $(document).ready(function(){

        var autoNum = 0;
        
        $('#addExraField').click(function(){
            autoNum++;
            var addFieldHtml = '';
                addFieldHtml += '<div class="row addFieldRow_'+ autoNum +'">'; 
                    addFieldHtml += '<div class="col-lg-3">';
                        addFieldHtml += '<div class="form-group">';
                            addFieldHtml += '<label>Column Number</label>';
                            addFieldHtml += '<input type="text" class="form-control colNumber" name="colNumber[]"> ';
                        addFieldHtml += '</div>';
                    addFieldHtml += '</div>';
                    addFieldHtml += '<div class="col-lg-3"> ';
                        addFieldHtml += '<div class="form-group"> ';
                            addFieldHtml += '<label>Field Name</label>'; 
                            addFieldHtml += '<select class="form-control fieldsName" data-select-id = "'+ autoNum +'" id="fieldsName_'+ autoNum +'"  name = "fieldsName[]">'; 
                                addFieldHtml += '<option value = "firstName">Firstname</option>';
                                addFieldHtml += '<option value = "lastName">Lastname</option>';
                                addFieldHtml += '<option value = "emailId">Email</option>'; 
                                addFieldHtml += '<option value = "phone">Phone</option>'; 
                                addFieldHtml += '<option value = "gender">Gender</option>'; 
                            addFieldHtml += '</select>'; 
                        addFieldHtml += '</div>'; 
                    addFieldHtml += '</div>'; 
                    addFieldHtml += '<div class="col-lg-3">'; 
                        addFieldHtml += '<div class="form-group">'; 
                            addFieldHtml += '<button type="button" class="btn btn-dark m-b-10 m-l-5" onclick = "javascript:removeExtraRowDiv(this);"  data-removeid = "'+ autoNum +'" style="margin-top: 15%;"> <i class="ti-minus"></i> </button>'; 
                        addFieldHtml += '</div>'; 
                    addFieldHtml += '</div>'; 
                addFieldHtml += '</div> ';


            $('#addExtraRowDiv').append(addFieldHtml);
        });

        $(document.body).on('change','.fieldsName',function(){
           
            var dataSelectId = $(this).attr('data-select-id');
            $('.customField_'+ dataSelectId).hide();
            $('.customField_'+ dataSelectId).find(":input").attr("disabled","disabled").val('');

        });

        $('#uploadCsv').change( function(event) {
            
            $('#postMsg').text('').removeClass('alert alert-danger alert-success');
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


        $('#btn_insert').click(function(){
            
            $('#sucErrMsg').text('').removeClass('alert alert-danger');
            $('#postMsg').text('').removeClass('alert alert-danger alert-success');

            var uploadCsvVal = $('#uploadCsv').val();

            if (uploadCsvVal == '') {

                $('#sucErrMsg').text('Please select CSV file to upload').addClass('alert alert-danger');
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

                    var fieldsNameArr = [];
                    var fieldsNameArrText = [];
                    var colNumber = [];
                    var isDupFieldName = 0;
                    var isDupColNumber = 0;
                    var hasNoEmailNoPhone = 0;

                    $(".fieldsName").each(function () {
                        
                        var dataSelectId = $(this).attr('data-select-id');
                        var selectVal = $('#fieldsName_'+ dataSelectId +' :selected').val();  
                        var selectText = $('#fieldsName_'+ dataSelectId +' :selected').text();    

                        if (selectVal == 'emailId' || selectVal == 'phone') {
                            hasNoEmailNoPhone = 1;
                        }
                        
                        if ($.inArray(selectVal, fieldsNameArr) !== -1) {
                            isDupFieldName = 1;
                        }else{
                            fieldsNameArr.push(selectVal);   
                        } 

                        fieldsNameArrText.push(selectText);
                    });

                    $(".colNumber").each(function(){
            
                        var numberVal = this.value; 

                        if (numberVal != '') {
                            if ($.inArray(numberVal,colNumber) !== -1) {
                                isDupColNumber = 1;
                            }else{
                                colNumber.push(numberVal);
                            }    
                        }
                    });

                    if (colNumber.length == 0) {
                        $('#sucErrMsg').text('Please enter atleast one column').addClass('alert alert-danger');
                        scrollToErrorSuccessMsg();
                        return false;
                    }else if (hasNoEmailNoPhone == 0) {
                        $('#sucErrMsg').text('Selection of Email OR Phone is required').addClass('alert alert-danger');
                        scrollToErrorSuccessMsg();
                        return false;
                    }else if (isDupColNumber == 1) {
                        $('#sucErrMsg').text('Duplicate column number found').addClass('alert alert-danger');
                        scrollToErrorSuccessMsg();
                        return false;
                    }else if (isDupFieldName == 1) {
                        $('#sucErrMsg').text('Duplicate field name found').addClass('alert alert-danger');
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

    function removeExtraRowDiv(curObj){
        
        var removeId = $(curObj).attr('data-removeid');
        $('.addFieldRow_' + removeId).html('');
    }

    function upload_csv(){
        
        $("#btn_div").hide();

        var fieldsName = [];
        var colNumber = [];

        $(".fieldsName").each(function () {
                        
            var dataSelectId = $(this).attr('data-select-id');
            var selectVal = $('#fieldsName_'+ dataSelectId +' :selected').val();  
            
            fieldsName.push(selectVal);   
            
        });

        $(".colNumber").each(function(){

            var numberVal = this.value; 

            if (numberVal != '') {
                colNumber.push(numberVal);
            }
        });

        var formData = new FormData();
        formData.append('uploadCsv', $('#uploadCsv')[0].files[0]); // since this is your file input
        formData.append('fieldsName',JSON.stringify(fieldsName));
        formData.append('colNumber',JSON.stringify(colNumber));

        $.ajax({

            url: BASE_URL + 'blacklist/addEditCSV',
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

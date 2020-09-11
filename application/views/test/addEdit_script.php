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
                                addFieldHtml += '<option value = "address">Address</option>'; 
                                addFieldHtml += '<option value = "postCode">Postcode</option>'; 
                                addFieldHtml += '<option value = "city">City</option>'; 
                                addFieldHtml += '<option value = "country">Country</option>'; 
                                addFieldHtml += '<option value = "phone">Phone</option>'; 
                                addFieldHtml += '<option value = "gender">Gender</option>'; 
                                addFieldHtml += '<option value = "birthdateDay">Birthdate Day</option>'; 
                                addFieldHtml += '<option value = "birthdateMonth">Birthdate Month</option>'; 
                                addFieldHtml += '<option value = "birthdateYear">Birthdate Year</option>'; 
                                addFieldHtml += '<option value = "ip">Ip</option>'; 
                                addFieldHtml += '<option value = "participated">Participated (timestamp)</option>'; 
                                addFieldHtml += '<option value = "campaignSource">Campaign Source</option>'; 
                                addFieldHtml += '<option value = "isUserActive">User Active (0=No,1=Yes)</option>'; 
                                addFieldHtml += '<option value = "other">Custom</option>'; 
                            addFieldHtml += '</select>'; 
                        addFieldHtml += '</div>'; 
                    addFieldHtml += '</div>'; 
                    addFieldHtml += '<div class="col-lg-3 customField_'+ autoNum +'" style="display: none;" >'; 
                       addFieldHtml += '<div class="form-group">'; 
                            addFieldHtml += '<label>Custom Field Name</label>'; 
                            addFieldHtml += '<input type="text" class="form-control customfieldsName" name="customfieldsName[]" disabled="disabled">'; 
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
            var selectVal = $('#fieldsName_'+ dataSelectId +' :selected').val();
            
            if (selectVal == 'other') {
                $('.customField_'+ dataSelectId).show();
                $('.customField_'+ dataSelectId).find(":input").removeAttr("disabled");
            }else{
                $('.customField_'+ dataSelectId).hide();
                $('.customField_'+ dataSelectId).find(":input").attr("disabled","disabled").val('');
            }

        });

        $('#uploadCsv').change( function(event) {
            
            $('#postMsg').text('').removeClass('alert alert-danger alert-success');
            $('#sucErrMsg').text('').removeClass('alert alert-danger');

            if($('#uploadCsv')[0].files.length >= 1){

                var fileName = $('#uploadCsv').val().split('\\').pop();
                var ext = $('#uploadCsv').val().split('.').pop().toLowerCase();

                if($.inArray(ext, ['csv']) == -1) {
                        
                    $('#sucErrMsg').text('Please select csv file only').addClass('alert alert-danger');
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
            var groupName = $('#groupName').val();
            var keyword = $('#keyword').val();

            if (uploadCsvVal == '') {

                $('#sucErrMsg').text('Please select CSV file to upload').addClass('alert alert-danger');
                return false;

            }else if (groupName == '') {

                $('#sucErrMsg').text('Please Enter Group Name').addClass('alert alert-danger');
                return false;

            }else if (keyword == '') {

                $('#sucErrMsg').text('Please Enter Keyword').addClass('alert alert-danger');
                return false;

            } else {

                var ext = $('#uploadCsv').val().split('.').pop().toLowerCase();

                if($.inArray(ext, ['csv']) == -1) {
                        
                    $('#sucErrMsg').text('Please select csv file only').addClass('alert alert-danger');
                    return false;

                }else{
                    //max size limit will be here

                    var fieldsNameArr = [];
                    var fieldsNameArrText = [];
                    var otherTextArr = [];
                    var colNumber = [];
                    var customfieldsNameValArr = [];
                    var isDupFieldName = 0;
                    var isDupColNumber = 0;

                    $(".fieldsName").each(function () {
                        
                        var dataSelectId = $(this).attr('data-select-id');
                        var selectVal = $('#fieldsName_'+ dataSelectId +' :selected').val();  
                        var selectText = $('#fieldsName_'+ dataSelectId +' :selected').text();    
                        
                        if (selectVal != 'other') {
                            if ($.inArray(selectVal, fieldsNameArr) !== -1) {
                                isDupFieldName = 1;
                            }else{
                                fieldsNameArr.push(selectVal);   
                            }    
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

                    $(".customfieldsName").each(function(){
            
                        var customFieldNameVal = this.value; 
                        customfieldsNameValArr.push(customFieldNameVal);
                            
                    });

                    if (colNumber.length == 0) {

                        $('#sucErrMsg').text('Please enter atleast one column').addClass('alert alert-danger');
                        return false;

                    }else if (isDupColNumber == 1) {
                        $('#sucErrMsg').text('Duplicate column number found').addClass('alert alert-danger');
                        return false;
                    }else if (isDupFieldName == 1) {
                        $('#sucErrMsg').text('Duplicate field name found').addClass('alert alert-danger');
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

                                        if (fieldsNameArrText[i].toLowerCase() == 'custom' ) {
                                            fn = customfieldsNameValArr[i];
                                        }
                                        
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
        /*$('.addFieldRow_' + removeId).find(":input").attr("disabled","disabled");
        $('.addFieldRow_' + removeId).hide();*/
        $('.addFieldRow_' + removeId).html('');
    }


    function upload_csv(){
        
        $("#btn_div").hide();

        var fieldsName = [];
        var colNumber = [];
        var customfieldsName = [];

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

        $(".customfieldsName").each(function(){

            var customFieldNameVal = this.value; 

            if(customFieldNameVal != ''){
                customfieldsName.push(customFieldNameVal);
            }
                
        });

        var groupName = $('#groupName').val();
        var keyword = $('#keyword').val();

        
        var formData = new FormData();
        formData.append('uploadCsv', $('#uploadCsv')[0].files[0]); // since this is your file input
        formData.append('fieldsName',JSON.stringify(fieldsName));
        formData.append('colNumber',JSON.stringify(colNumber));
        formData.append('customfieldsName',JSON.stringify(customfieldsName));
        formData.append('groupName',groupName);
        formData.append('keyword',keyword);

        $.ajax({

            url: BASE_URL + 'test/addEdit',
            type: "post",
            dataType: 'json',
            processData: false, // important
            contentType: false, // important
            data: formData,
            success: function(response) {

                var listArr = response; // json array

                if(listArr.err == 0)
                {
                    /*var totalEntires = 'Total '+listArr.entries+' Records Inserted';
                    $('#total_enrty_label').text(totalEntires);
                    $('#countButton').show();*/
                    $('#approvedRejectPop').modal('hide');
                    $("#sucErrMsg").html(listArr.msg).addClass('alert alert-success');

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
                }
            },
            error: function(e) {
                /*console.log('e>>>',e);*/
                $("#progressBar").hide();
                $('#approvedRejectPop').modal('hide');
                $("#sucErrMsg").text("There is some problem occur. Please try again later.").addClass('alert alert-danger');

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

    $(document).ready(function(){
        $("#approvedRejectPop").on("hidden.bs.modal", function () {
            if($('#countButton').is(":visible") == true){
                location.reload();
            }
        });
    });

</script>

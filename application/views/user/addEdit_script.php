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
                                addFieldHtml += '<option value = "phone">Phone</option>'; 
                                addFieldHtml += '<option value = "gender">Gender</option>'; 
                                addFieldHtml += '<option value = "birthdateDay">Birthdate Day</option>'; 
                                addFieldHtml += '<option value = "birthdateMonth">Birthdate Month</option>'; 
                                addFieldHtml += '<option value = "birthdateYear">Birthdate Year</option>'; 
                                addFieldHtml += '<option value = "age">Age</option>'; 
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
            var groupName = $('#groupName').val();
            var keyword = $('#keyword').val();
            var country = $('#country').val();

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

            }else if (country == '') {

                $('#sucErrMsg').text('Please Select Country').addClass('alert alert-danger');
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
        var country = $('#country').val();
        var campaign = $('#campaign').val();
        var providerName = $('#providerName').val();
        var providerList = $('#providerList').val();
        var perDayRecord = $('#perDayRecord').val();
        var fromDate = $('#fromDate').val();
        var startTime = $('#startTime').val();
        var endTime = $('#endTime').val();
        
        var formData = new FormData();
        formData.append('uploadCsv', $('#uploadCsv')[0].files[0]); // since this is your file input
        formData.append('fieldsName',JSON.stringify(fieldsName));
        formData.append('colNumber',JSON.stringify(colNumber));
        formData.append('customfieldsName',JSON.stringify(customfieldsName));
        formData.append('groupName',groupName);
        formData.append('keyword',keyword);
        formData.append('country',country);
        formData.append('campaign',campaign);
        formData.append('providerName',providerName);
        formData.append('providerList',providerList);
        formData.append('perDayRecord',perDayRecord);
        formData.append('fromDate',fromDate);
        formData.append('startTime',startTime);
        formData.append('endTime',endTime);


        $.ajax({

            url: BASE_URL + 'user/addEdit',
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

    $(document).on("change","#providerName",function(){
        var providerId = $(this).val();
        var providerList = "";
        if(providerId == 1){
            providerList +="<option value='1'>Velkomstgaven.com (Norway)</option>";
            providerList +="<option value='2'>Gratispresent.se (Sweden)</option>";
            providerList +="<option value='3'>Velkomstgaven.dk (Denmark)</option>";
            providerList +="<option value='4'>Freecasinodeal.com/no  (Norway)</option>";
            providerList +="<option value='5'>Freecasinodeal.com/fi (Finland)</option>";
            providerList +="<option value='6'>Freecasinodeal.com</option>";
            providerList +="<option value='7'>FI - Katariinasmail</option>";
            providerList +="<option value='8'>NO - Signesmail</option>";
            providerList +="<option value='9'>SE - Frejasmail</option>";
            providerList +="<option value='10'>CA - Getspinn</option>";
            providerList +="<option value='11'>NO - Getspinn</option>";
            providerList +="<option value='12'>NZ - Getspinn</option>";
            providerList +="<option value='13'>Freecasinodeal.com/nz (New Zealand)</option>";
            providerList +="<option value='14'>DK - Signesmail</option>";
            providerList +="<option value='15'>DK - abbie</option>";
            providerList +="<option value='16'>FI - abbie</option>";
            providerList +="<option value='17'>NO - abbie</option>";
            providerList +="<option value='18'>SE - abbie</option>";
            providerList +="<option value='19'>FreeCasinodeal/ca (Canada)</option>";
            providerList +="<option value='20'>FelinaFinans/se</option>";
            providerList +="<option value='21'>New_gratispresent</option>";
            providerList +="<option value='22'>New_velkomstgaven_dk</option>";
            providerList +="<option value='23'>New_velkomstgaven_com</option>";
            providerList +="<option value='24'>New_velkomstgaven1_com</option>";
            providerList +="<option value='25'>New_unelmalaina</option>";           
            providerList +="<option value='26'>Freecasinodeal/NZ (olivia)</option>";
            providerList +="<option value='27'>Freecasinodeal/CA (sofia)</option>";
            providerList +="<option value='28'>Freecasinodeal/NO (emma)</option>";
            providerList +="<option value='29'>Freecasinodeal/FI (aida)</option>";
            providerList +="<option value='30'>Frejasmail1/SE</option>";
            providerList +="<option value='31'>Frejasmail2/SE</option>";
            providerList +="<option value='32'>Signesmail1/DK</option>";
            providerList +="<option value='33'>Katariinasmail1/FI</option>";
            providerList +="<option value='34'>Signesmail1/NO</option>";
            providerList +="<option value='35'>Signesmail2/NO</option>";
            providerList +="<option value='36'>Abbiesmail1/CA</option>";
            providerList +="<option value='37'>Abbiesmail2/CA</option>";
            providerList +="<option value='38'>Ashleysmail/NZ</option>";
            providerList +="<option value='39'>Ashleysmail1/NZ</option>";
            providerList +="<option value='40'>Signesmail/DK</option>";
            providerList +="<option value='41'>Velkomstgaven/NO</option>";
            providerList +="<option value='42'>Velkomstgaven1/NO</option>";
            providerList +="<option value='43'>Gratispresent/SE</option>";
            providerList +="<option value='44'>Gratispresent1/SE</option>";
            providerList +="<option value='45'>FelinaFinans/SE</option>";
            providerList +="<option value='46'>FelinaFinans1/SE</option>";
            providerList +="<option value='47'>FelinaFinansmail/SE</option>";
            providerList +="<option value='48'>Unelmalaina/FI</option>";
            providerList +="<option value='49'>Unelmalaina1/FI</option>";
            providerList +="<option value='50'>Velkomstgaven/DK</option>";
            providerList +="<option value='51'>Velkomstgaven1/DK</option>";
            providerList +="<option value='52'>Getspinn1/CA</option>";
            providerList +="<option value='53'>Getspinnmail/CA</option>";
            providerList +="<option value='54'>Freecamail/CA</option>";
        }else if(providerId == 2){
            providerList +="<option value='1'>NO - deveroper</option>";
            providerList +="<option value='2'>SE - deveroper - Loan</option>";
            providerList +="<option value='3'>SE - deveroper</option>";
            providerList +="<option value='4'>FI - deveroper</option>";
            providerList +="<option value='5'>NO Casino - eonoc</option>";
            providerList +="<option value='6'>FI Casino - eonoc</option>";
            providerList +="<option value='7'>FI - eacademyzone</option>";
            providerList +="<option value='8'>NO - eacademyzone</option>";
            providerList +="<option value='9'>SE - eacademyzone</option>";
            providerList +="<option value='10'>SE - Loan - eacademyzone</option>";
            providerList +="<option value='11'>Global Casino Dollars - divinecareca</option>";
            providerList +="<option value='12'>Global Casino EUR - divinecareca</option>";
            providerList +="<option value='13'>NO Casino - divinecareca</option>";
            providerList +="<option value='14'>FI - ElasticEmail</option>";
            providerList +="<option value='15'>SE - ElasticEmail</option>";
            providerList +="<option value='16'>NO - ElasticEmail</option>";
            providerList +="<option value='17'>NO - SparkPost</option>";
            providerList +="<option value='18'>SE - SparkPost</option>";
            providerList +="<option value='19'>FI - SparkPost</option>";
            providerList +="<option value='20'>NO - Amazon</option>";
            providerList +="<option value='21'>SE - Amazon</option>";
            providerList +="<option value='22'>FI - Amazon</option>";
        }else if(providerId == 4){
            providerList +="<option value='1'>SE - Test</option>";
            providerList +="<option value='2'>DK - Test</option>";
            providerList +="<option value='3'>Canada - Camilla</option>";
            providerList +="<option value='4'>Canada - Kare</option>";
            providerList +="<option value='5'>Sweden - Camilla</option>";
            providerList +="<option value='6'>Sweden - Kare</option>";
            providerList +="<option value='7'>Norway - Camilla</option>";
            providerList +="<option value='8'>Norway - Kare</option>";
            providerList +="<option value='9'>Finland  - Camilla</option>";
            providerList +="<option value='10'>Finland  - Kare</option>";
            providerList +="<option value='11'>New Zealand  - Camilla</option>";
            providerList +="<option value='12'>NO - Test</option>";
            providerList +="<option value='13'>Denmark  - Kare</option>";
            providerList +="<option value='14'>Denmark  - Camilla</option>";
            providerList +="<option value='15'>FI - Test</option>";
        }else if(providerId == 5){
            providerList +="<option value='1'>CA</option>";
        }else if(providerId == 6){
            providerList +="<option value='1'>NO</option>";
            providerList +="<option value='2'>CA</option>";
            providerList +="<option value='3'>NZ</option>";
            providerList +="<option value='4'>SE</option>";
        }else{
            providerList +="<option value='0'>Select Provider List</option>";
        }

        $("#providerList").html(providerList);
    });

</script>

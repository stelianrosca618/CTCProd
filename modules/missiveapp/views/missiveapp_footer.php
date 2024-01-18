<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js" integrity="sha512-VvWznBcyBJK71YKEKDMpZ0pCVxjNuKwApp4zLF3ul+CiflQi6aIJR+aZCP/qWsoFBA28avL5T5HA+RE+zrGQYg==" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://integrations.missiveapp.com/missive.js"></script>
<script type="text/javascript">
    let countries = [];
    let prodItems = [];
    let sourcelist = [];
    const element = $("body").find("input.tagsinput");
    var toDate = new Date();
    var toDayStr = toDate.getFullYear() + '-' + (toDate.getMonth() + 1) + '-' + toDate.getDate();
    //$('.new-proposl-relType').selectpicker();
</script>
<script type="text/javascript">
    function showHideTagsPlaceholder($tagit) {
        var $input = $tagit.data("ui-tagit").tagInput,
            placeholderText = $tagit.data("ui-tagit").options.placeholderText;
        $tagit.tagit("assignedTags").length > 0 ?
            $input.removeAttr("placeholder") :
            $input.attr("placeholder", placeholderText);
    }

    function loadNewProposalData() {
        $.get(site_url + 'missiveapp/loadNewProposalData').done(function(response) {
            var newRes = JSON.parse(response);
            console.log(newRes);
            countries = [];
            newRes.data.countries.map(citem => {
                countries.push({
                    id: citem.country_id,
                    text: citem.short_name
                })
            })
            prodItems = [];
            newRes.data.items.map(pitem => {
                prodItems.push(
                    pitem.description
                )
            })
            sourcelist = [];
            newRes.data.sources.map(sItem => {
                sourcelist.push({
                    id: sItem.id,
                    text: sItem.name
                })
            })
            $('select[name="newcontact_country"]').select2({
                theme: "classic",
                data: countries
            });
            $('select[name="newlead_products"]').select2({
                theme: "classic",
                data: prodItems
            });
            $('select[name="newLead-source"]').select2({
                theme: "classic",
                data: sourcelist
            })
            var subjectInput = $('#new-proposal-form').find('input#subject');
            subjectInput.val(newRes.data.subject);
        })
    }
    //loadNewProposalData();
    // Function to store data in local storage
    function storeSet(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
            return true;
        } catch (error) {
            console.error('Error storing data:', error);
            return false;
        }
    }

    // Function to retrieve data from local storage
    function storeGet(key) {
        try {
            const storedValue = localStorage.getItem(key);
            return storedValue ? JSON.parse(storedValue) : null;
        } catch (error) {
            console.error('Error retrieving data:', error);
            return null;
        }
    }

    function loadInitCRM(emailAddress) {

        var initContent = `<div class="box">
            <div class="box-content">
            <form id="newLead-form" autocomplete="off" method="post" novalidate="novalidate">
                <div class="contact-information">
                    <div class="light-box contact-card p-1">
                        <h5 class="text-center">Contact</h5>
                        <div class="contact-data-row">
                            <div class="data-Name">COMPANY</div>
                            <div class="data-value">
                                <input type="text" name="company" value="" class="newLead-contact-input" placeholder="Company Name"/>
                            </div>
                        </div>
                        <div class="contact-data-row">
                            <div class="data-Name">CREATED</div>
                            <div class="data-value">
                                <input type="text" name="createAt" value="${toDayStr}" class="newLead-contact-input" readOnly/>
                            </div>
                        </div>
                        <div class="contact-data-row">
                            <div class="data-Name">COUNTRY</div>
                            <div class="data-value">
                                <select name="newcontact_country" class="w-full"></select>
                            </div>
                        </div>
                        <div class="contact-data-row">
                            <div class="data-Name">TIER</div>
                            <div class="data-value">
                                <Select name="Tierlevel" class="form-control">
                                    <option value="Tier 1">Tier 1</option>
                                    <option value="Tier 2">Tier 2</option>
                                    <option value="Tier 3">Tier 3</option>
                                </Select>
                                <!--<input type="text" name="Tierlevel" value="" class="newLead-contact-input"  placeholder="Company Name"/>-->
                            </div>
                        </div>
                        <div class="contact-data-row">
                            <div class="data-Name">NAME</div>
                            <div class="data-value">
                                <input type="text" name="firstname" value="" class="newLead-contact-input" placeholder="FirstName"/>
                                <input type="text" name="lastname" value="" class="newLead-contact-input" placeholder="LastName"/>
                            </div>
                        </div>
                        <div class="contact-data-row">
                            <div class="data-Name">EMAIL</div>
                            <div class="data-value">
                                <input type="text" name="email" value="${emailAddress}" class="newLead-contact-input" placeholder="Email"/>
                            </div>
                        </div>
                        <div class="contact-data-row">
                            <div class="data-Name">SOURCE</div>
                            <div class="data-value">
                                <select name="newLead-source"></select>
                            </div>
                        </div>
                        <div class="contact-data-row">
                            <div class="data-Name">STATUS</div>
                            <div class="data-value">
                                <input type="text" name="status" value="Lead" class="newLead-contact-input" readonly/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="contact-information margin-top-medium">
                    <div class="light-box contact-card p-1">
                        <h5 class="text-center"><img class="missive-img" src="/assets/images/missive/target-center-2.png"/>LEAD DETAILS</h5>    
                        <div class="contact-data-row">
                            <div class="data-Name">PRODUCTS</div>
                            <div class="data-value">
                                <select name="newlead_products" multiple="multiple"></select>
                            </div>
                        </div>
                        <div class="contact-data-row">
                            <div class="data-Name">DATA SHEETS</div>
                            <div class="data-value">
                                <input type="checkbox" name="new_dataSheet_check" id="new_dataSheet_check" class="seles-invoiced-checked lead-check" />
                                <input type="text" name="dataSheet_date" id="newLead_TDS" value="" class="newLead-contact-input" placeholder="Date Sheets Date"/>
                            </div>
                        </div>
                        <div class="contact-data-row">
                            <div class="data-Name">SAMPLES</div>
                            <div class="data-value">
                                <input type="checkbox" name="new_sample_check" id="new_sample_check" class="seles-invoiced-checked lead-check"/>
                                <input type="text" name="sample_date" id="newLead_sample" value="" class="newLead-contact-input" placeholder="Sample Date"/>
                            </div>
                        </div>
                        <div class="contact-data-row">
                            <div class="data-Name">TRACKING N</div>
                            <div class="data-value">
                                <input type="text" name="trackNumber" id="newLead_trackNum" class="newLead-contact-input" value="" placeholder="Track Number"/>
                            </div>
                        </div>
                        <div class="contact-data-row">
                            <div class="data-Name">FORECAST</div>
                            <div class="data-value">
                                <input type="text" name="qtyyear" class="newLead-contact-input" id="newlead-qtyyear" value="" placeholder="Forecast (mt / year)"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="contact-information margin-top-medium">
                    <div class="light-box contact-card p-1 text-center">
                        <button type="submit" class="button lead-create-btn">Create Lead</button>
                    </div>
                </div>
            </form>
            </div>
        </div>`;
        $('#fetch-users').html(initContent);
        $(document).on('change', 'input#new_sample_check', function(e) {
            $('input#newLead_sample').val(toDayStr);
        })
        $(document).on('change', 'input#new_dataSheet_check', function(e) {
            $('input#newLead_TDS').val(toDayStr);
        })

        $('#newLead-form').validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            submitHandler: function(form) {
                console.log(form);
                var customfields = [];
                var newDate = new Date();
                var customValStr = [];
                var customDates = [];
                if ($('input#new_sample_check:checked').length > 0) {
                    customValStr.push('Sample');
                    customDates.push($('input#newLead_sample').val());
                }
                if ($('input#new_dataSheet_check:checked').length > 0) {
                    customValStr.push('TDS');
                    customDates.push($('input#newLead_TDS').val());
                }
                customfields.push({
                    fieldid: 1,
                    fieldto: 'leads',
                    value: customValStr.toString(),
                    dates: customDates.toString(),
                });
                if ($('input[name="trackNumber"]').val()) {
                    customfields.push({
                        fieldid: 2,
                        fieldto: 'leads',
                        value: $('input[name="trackNumber"]').val(),
                        dates: ''
                    });
                }
                if ($('select[name="newlead_products"]').val()) {
                    customfields.push({
                        fieldid: 4,
                        fieldto: 'leads',
                        value: $('select[name="newlead_products"]').val().toString(),
                        dates: ''
                    });
                }
                if ($('input[name="qtyyear"]').val()) {
                    customfields.push({
                        fieldid: 5,
                        fieldto: 'leads',
                        value: $('input[name="qtyyear"]').val(),
                        dates: ''
                    });
                }
                var newLeadData = {
                    company: $('input[name="company"].newLead-contact-input').val(),
                    country: $('select[name="newcontact_country"]').val(),
                    tier: $('select[name="Tierlevel"]').val(),
                    fname: $('input[name="firstname"].newLead-contact-input').val(),
                    lname: $('input[name="lastname"].newLead-contact-input').val(),
                    email: $('input[name="email"].newLead-contact-input').val(),
                    created: $('input[name="createAt"].newLead-contact-input').val(),
                    source: $('select[name="newLead-source"]').val(),
                    customFields: customfields,
                }
                console.log('new Lead form', newLeadData);
                if (newLeadData.fname && newLeadData.email && newLeadData.company) {
                    //Missive.alert({title: 'Okay'})
                    $.ajax({
                        url: site_url + 'missiveapp/lead_Create',
                        type: 'post',
                        data: newLeadData,
                        beforeSend: function() {
                            $('.lead-create-btne').button('loading');
                        },
                        complete: function() {
                            Missive.reload();
                        },
                        success: function(json) {


                            if (json['success']) {
                                // Missive.reload();

                                //      $('select[name="country"]').prop("disabled", false);
                            }
                            Missive.reload();

                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                } else {
                    Missive.alert({
                        title: 'Wrong Fields'
                    });
                }
                return false;
            }
        })
    }

    function loadCRMContent(emailData) {
        $.ajax({
            type: 'get',
            data: {
                'email': emailData
            },
            url: site_url + 'missiveapp/contact'
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success) {
                fullMissiveData = response;
                console.log('here is my missiveAPP log', response);
                let customerBg = '#99ec8a';
                switch (response.contact.customerGroups) {
                    case "Tier 1":
                        customerBg = '#99ec8a';
                        break;
                    case "Tier 2":
                        customerBg = '#8088f0';
                        break;
                    case "Tier 3":
                        customerBg = '#eed887';
                        break;
                }
                //$('#fetch-users').html('<div><ul class="nav nav-tabs contact-nav-tabs" role="tablist"><li role="presentation" class="active"><a href="#contact" aria-controls="contact" role="tab" data-toggle="tab">Contact</a></li><li role="presentation"><a href="#sales" aria-controls="sales" role="tab" data-toggle="tab">Sales</a></li></ul><div class="tab-content"><div role="tabpanel" class="tab-pane active" id="contact"><div class="box"><div class="box-content"><div class="contact-information"><div class="list-items light-box"><div class="list-item padding-small"><div class="columns-middle"><span>Company: ' + response.contact.company + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Client: ' + ((response.contact.leadid != null && response.contact.leadid != '') ? 'Yes' : 'Yes') + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Created: ' + response.contact.created_date + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Level: ' + ((response.contact.customerGroups != null && response.contact.customerGroups != '') ? response.contact.customerGroups : '--') + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Country: ' + response.contact.short_name + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Name: ' + response.contact.firstname + ' ' + response.contact.lastname + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Email: ' + response.contact.email + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Status: ' + ((response.contact.leadid != null && response.contact.leadid != '') ? response.lead.status_name : '--') + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Source: ' + ((response.contact.leadid != null && response.contact.leadid != '') ? response.lead.source_name : '--') + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Products: ' + response.products + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Consumption: ' + response.consumption + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><button type="button" class="btn btn-default button-contact-edit">Edit</button></div></div></div></div><div class="contact-edit-information hide"><form id="contact-form" autocomplete="off" method="post" novalidate="novalidate"><input type="hidden" name="' + response.csrf_name + '" value="' + response.csrf_hash + '"><div class="panel panel-default"><div class="panel-body"><div class="alert alert-vk alert-danger edit-error-area hide"><strong>Error!</strong> <span></span></div><input type="hidden" name="contactid" value="' + response.contact.contact_id + '"><div class="form-group" app-field-wrapper="firstname"><label for="firstname" class="control-label"> <small class="req text-danger">* </small>First Name</label><input type="text" id="firstname" name="firstname" class="form-control" value="' + response.contact.firstname + '"></div><div class="form-group" app-field-wrapper="lastname"><label for="lastname" class="control-label"> <small class="req text-danger">* </small>Last Name</label><input type="text" id="lastname" name="lastname" class="form-control" value="' + response.contact.lastname + '"></div><div class="form-group" app-field-wrapper="title"><label for="title" class="control-label">Position</label><input type="text" id="title" name="title" class="form-control" value="' + response.contact.title + '"></div><div class="form-group" app-field-wrapper="email"><label for="email" class="control-label"> <small class="req text-danger">* </small>Email</label><input type="email" id="email" name="email" class="form-control" value="' + response.contact.email + '" readonly></div><div class="form-group" app-field-wrapper="phonenumber"><label for="phonenumber" class="control-label">Phone</label><input type="text" id="phonenumber" name="phonenumber" class="form-control" autocomplete="off" value="' + ((response.contact.phonenumber != '') ? response.contact.phonenumber : response.contact.calling_code) + '"></div></div><div class="panel-footer"><button type="submit" class="btn btn-primary button-contact-update" data-loading-text="Please wait..." autocomplete="off" data-form="#contact-form">Save</button><button type="button" class="btn btn-default button-contact-cancel">Cancel</button></div></div></form></div><div class="contact-information"><div class="box margin-top-medium"><div class="box-content">Fixed Notes:<div>' + response.fixed_notes + '</div></div></div><div class="box margin-top-medium"><div class="box-content">Last Notes:<div>' + response.last_notes + '</div></div></div></div></div></div></div><div role="tabpanel" class="tab-pane" id="sales"><div class="box box-sales"><div class="box-content">Quotations:' + response.quotation_html + '</div></div><div class="box box-sales margin-top-medium"><div class="box-content">Invoices:' + response.invoice_html + '</div></div><div class="box box-sales margin-top-medium"><div class="box-content">Payments:' + response.payment_html + '</div></div><div class="box box-sales margin-top-medium"><div class="box-content">Consumption:${response.consumption_html}</div></div></div></div></div>');
                var lastNotesHtml = ''
                if (response.last_notes.length > 0) {
                    response.last_notes.map(noteItm => {
                        lastNotesHtml += `<div class="lastNote-row"><div class="lastNote-description">${noteItm.description}</div><div class="lastNote-date">${noteItm.dateadded} by ${noteItm.firstname} ${noteItm.lastname}</div></div>`
                    })
                }
                var leadDataHtml = '';
                var contactDataHtml = '';
                if (response.status == 'lead') {
                    var leadfields = {
                        id: response.contact.id
                    };
                    /*leadfields.Products = null;
                    leadfields.prodId = null;
                    leadfields.customId = null;
                    leadfields.dataSheet = null;
                    leadfields.dataSheetId = null;
                    leadfields.samples = null;
                    leadfields.sampleId = null;
                    leadfields.leadQuotation = null;*/
                    leadfields.forecast = 'null';
                    leadfields.forecastId = null;
                    leadfields.trankIn = 'null';
                    leadfields.trankInid = null;
                    if (response.leadFields.length > 0) {
                        response.leadFields.map(fItem => {
                            if (fItem.fieldid == 1) {
                                var valStr = fItem.value.split(' ');
                                valStr.map((vItem, index) => {
                                    if (vItem.includes('TDS')) {
                                        leadfields.dataSheet = fItem.dates ? fItem.dates.split(',')[index] : '';
                                        leadfields.dataSheetId = fItem.id;
                                    }
                                    if (vItem.includes('Sample')) {
                                        leadfields.samples = fItem.dates ? fItem.dates.split(',')[index] : '';
                                        leadfields.sampleId = fItem.id;
                                    }
                                })

                                leadfields.customId = fItem.id;
                            } else if (fItem.fieldid == 2) {
                                leadfields.trankIn = fItem.value;
                                leadfields.trankInid = fItem.id;
                            } else if (fItem.fieldid == 4) {
                                leadfields.Products = fItem.value;

                                leadfields.prodId = fItem.id;
                            } else if (fItem.fieldid == 5) {
                                leadfields.forecast = fItem.value;
                                leadfields.forecastId = fItem.id;
                            }
                        })
                    }
                    if (response.leadQuataintion.isActive) {
                        leadfields.leadQuotation = response.leadQuataintion.Date;
                    }
                    var leadData = response.contact;
                    leadData.tier = leadData.tier ? leadData.tier : 'Tier 1';
                    leadData.fname = response.contact.name.split(' ')[0];
                    leadData.lname = response.contact.name.split(' ').length > 1 ? response.contact.name.split(' ')[1] : '';
                    const addedDate = leadData.created_date ? new Date(leadData.created_date) : new Date(leadData.dateadded);
                    const dateOptions = {
                        year: 'numeric',
                        month: 'numeric',
                        day: 'numeric',
                    };
                    contactDataHtml = ` <div class="light-box contact-card p-1">
                                            <h5 class="text-center">${leadData.company}</h5>
                                            <form id="contact-form" autocomplete="off" method="post" novalidate="novalidate">
                                            <input type="hidden" name="${response.csrf_name}" value="${ response.csrf_hash}">
                                            <input type="hidden" name="contactid" value="${leadData.id}">
                                            <div class="contact-card-head">
                                                <button type="button" class="button button-small button-contact-edit">Edit</button>
                                                <div class="contact-update-btns hide">
                                                <button type="submit" class="button button-small button-contact-update" data-loading-text="Please wait..." autocomplete="off" data-form="#contact-form">Save</button>
                                                <button type="button" class="button button-small button-contact-cancel">Cancel</button>
                                                </div>
                                                
                                                <span class="customerGroup-badge" style="background: ${customerBg};">${leadData.tier}</span>
                                                <span class="contact-date">${addedDate.toLocaleDateString(undefined, dateOptions)}</span>
                                            </div>
                                            <div class="contact-data-row hide customerGroup-edit">
                                                <div class="data-Name">Tier Level</div>
                                                <div class="data-value">
                                                    <select name="customerTierLevel" class="tw-w-full">
                                                        <option value="Tier 1" ${leadData.tier == "Tier 1"? 'selected' : ''}>Tier 1</option>
                                                        <option value="Tier 2" ${leadData.tier == "Tier 2"? 'selected' : ''}>Tier 2</option>
                                                        <option value="Tier 3" ${leadData.tier == "Tier 3"? 'selected' : ''}>Tier 3</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="contact-data-row">
                                                <div class="data-Name">COUNTRY</div>
                                                <div class="data-value">
                                                    <div class="contact-country-selector tw-w-full hide">
                                                        <select name="country[]" class="w-full"></select>
                                                    </div>
                                                    <div class="contact-country-viewer">
                                                        <span class="contact-country-span" id="${leadData.country}">${response.contact.countryNames? response.contact.countryNames : ''}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="contact-data-row">
                                                <div class="data-Name">NAME</div>
                                                <div class="data-value">
                                                    <input type="text" name="firstname" value="${leadData.fname}" class="contact-input" readOnly/>
                                                    <input type="text" name="lastname" value="${leadData.lname}" class="contact-input" readOnly/>
                                                </div>
                                            </div>
                                            <div class="contact-data-row">
                                                <div class="data-Name">EMAIL</div>
                                                <div class="data-value">
                                                    <input type="text" name="email" value="${leadData.email}" class="contact-input"  readOnly/>
                                                </div>
                                            </div>
                                            <div class="contact-data-row">
                                                <div class="data-Name">SOURCE</div>
                                                <div class="data-value">
                                                    <div class="contact-source-selector tw-w-full hide">
                                                        <select name="source"></select>
                                                    </div>
                                                    
                                                    <div class="contact-source-viewer">
                                                        <span>${leadData.source_name}</span>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            <div class="contact-data-row">
                                                <div class="data-Name">STATUS</div>
                                                <div class="data-value">
                                                    <span class="${response.status}-badge">${response.status}</span>
                                                </div>
                                            </div>
                                            </form>
                                        </div>`;
                    leadDataHtml += `
                                    <div class="contact-information margin-top-medium">
                                        <div class="light-box contact-card p-1">
                                            <form id="lead-form" autocomplete="off" method="post" novalidate="novalidate">
                                            <input type="hidden" name="${response.csrf_name}" value="${ response.csrf_hash}">
                                            <input type="hidden" name="leadId" value="${leadfields.id}">
                                            <div class="contact-card-head">
                                                <button type="button" class="button button-small button-lead-edit">Edit</button>
                                                <div class="lead-update-btns hide">
                                                <button type="submit" class="button button-small button-lead-update" data-loading-text="Please wait..." autocomplete="off" data-form="#contact-form">Save</button>
                                                <button type="button" class="button button-small button-lead-cancel">Cancel</button>
                                                </div>
                                                <h5 class="text-center"><img class="missive-img" src="/assets/images/missive/target-center-2.png"/>LEAD DETAILS</h5>    
                                                
                                                <span class="contact-date"></span>
                                            </div>
                                            <div class="contact-data-row">
                                                <div class="data-Name">PRODUCTS</div>
                                                <div class="data-value">
                                                    <div class="leadProd-selector tw-w-full hide">
                                                        <select name="lead_products" multiple="multiple" id="${leadfields.prodId}" value="${leadfields.Products}"></select>
                                                    </div>
                                                    <div class="leadProd-content tw-w-full ">
                                                        
                                                        <span class="lead_prods_span">${leadfields.Products}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="contact-data-row">
                                                <div class="data-Name">DATA SHEETS</div>
                                                <div class="data-value">
                                                    <input type="hidden" name="lead_customId" value="${leadfields.customId}" />
                                                    <input type="checkbox" name="dataSheet_check" id="dataSheet_check" class="seles-invoiced-checked lead-check" ${leadfields.dataSheetId? "checked" : "" }   readonly/>
                                                    <input type="text" name="dataSheet_date" id=${leadfields.dataSheetId} value="${leadfields.dataSheet?leadfields.dataSheet : ''}" class="lead-input" readOnly/>
                                                </div>
                                            </div>
                                            <div class="contact-data-row">
                                                <div class="data-Name">SAMPLES</div>
                                                <div class="data-value">
                                                    <input type="checkbox" name="sample_check" id="sample_check" class="seles-invoiced-checked lead-check" ${leadfields.sampleId? "checked" : "" }  readonly/>
                                                    <input type="text" name="sample_date" id=${leadfields.sampleId} value="${leadfields.samples?leadfields.samples:''}" class="lead-input" readOnly/>
                                                </div>
                                            </div>
                                            <div class="contact-data-row">
                                                <div class="data-Name">TRACKING N</div>
                                                <div class="data-value">
                                                    <input type="text" name="trackNumber" id=${leadfields.trankInid} class="lead-input" value="${leadfields.trankIn == 'null'? '' : leadfields.trankIn }" readOnly/>
                                                </div>
                                            </div>
                                            <div class="contact-data-row">
                                                <div class="data-Name">FORECAST</div>
                                                <div class="data-value">
                                                    <!--<span>${leadfields.forecast} mt/year</span>-->
                                                    <input type="input" name="lead_forecast" id="${leadfields.forecastId}" class="lead-input" value="${leadfields.forecast || leadfields.forecast == 'null'? '':leadfields.forecast }" readOnly  />
                                                </div>
                                            </div>
                                            <div class="contact-data-row">
                                                <div class="data-Name">QUOTATION</div>
                                                <div class="data-value">
                                                    <input type="checkbox" class="seles-invoiced-checked" ${leadfields.leadQuotation? 'checked' : ''} readonly/>
                                                    <span>- ${leadfields.leadQuotation?leadfields.leadQuotation:''}</span>
                                                    <!--<input type="input" name="status" class="lead-quatation" value="${leadfields.leadQuotation}" readOnly  />-->
                                                </div>
                                            </div>
                                            </form>
                                        </div>
                                    </div> 
                                    `
                } else if (response.status == "Client") {
                    const contactAddDate = new Date(response.contact.created_date);
                    const dateOptions = {
                        year: 'numeric',
                        month: 'numeric',
                        day: 'numeric',
                    };
                    contactDataHtml = `<div class="light-box contact-card p-1">
                                            <h5 class="text-center">${response.contact.company}</h5>
                                            <form id="contact-form" autocomplete="off" method="post" novalidate="novalidate">
                                            <input type="hidden" name="${response.csrf_name}" value="${ response.csrf_hash}">
                                            <input type="hidden" name="contactid" value="${response.contact.contact_id}">
                                            <input type="hidden" name="leadid" value="${response.contact.leadid}">
                                            <div class="contact-card-head">
                                                <button type="button" class="button button-small button-contact-edit">Edit</button>
                                                <div class="contact-update-btns hide">
                                                <button type="submit" class="button button-small button-contact-update" data-loading-text="Please wait..." autocomplete="off" data-form="#contact-form">Save</button>
                                                <button type="button" class="button button-small button-contact-cancel">Cancel</button>
                                                </div>
                                                
                                                <span class="customerGroup-badge" style="background: ${customerBg};">${response.contact.customerGroups}</span>
                                                <span class="contact-date">${contactAddDate.toLocaleDateString(undefined, dateOptions)}</span>
                                            </div>
                                            <div class="contact-data-row hide customerGroup-edit">
                                                <div class="data-Name">Tier Level</div>
                                                <div class="data-value">
                                                    <select name="customerTierLevel" class="tw-w-full">
                                                        <option value="Tier 1" ${response.lead?.tier == "Tier 1"? 'selected' : ''}>Tier 1</option>
                                                        <option value="Tier 2" ${response.lead?.tier == "Tier 2"? 'selected' : ''}>Tier 2</option>
                                                        <option value="Tier 3" ${response.lead?.tier == "Tier 3"? 'selected' : ''}>Tier 3</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="contact-data-row">
                                                <div class="data-Name">COUNTRY</div>
                                                <div class="data-value">
                                                    <div class="contact-country-selector tw-w-full hide">
                                                        <select name="country[]" id="client-country" multiple="multiple"></select>
                                                    </div>
                                                    <div class="contact-country-viewer">
                                                        <span class="contact-country-span" id="contryNames">${response.contact.countryNames? response.contact.countryNames : ''}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="contact-data-row">
                                                <div class="data-Name">NAME</div>
                                                <div class="data-value">
                                                    <input type="text" name="firstname" value="${response.contact.firstname}" class="contact-input" readOnly/>
                                                    <input type="text" name="lastname" value="${response.contact.lastname}" class="contact-input" readOnly/>
                                                </div>
                                            </div>
                                            <div class="contact-data-row">
                                                <div class="data-Name">EMAIL</div>
                                                <div class="data-value">
                                                    <input type="text" name="email" value="${response.contact.email}" class="contact-input"  readOnly/>
                                                </div>
                                            </div>
                                            <div class="contact-data-row">
                                                <div class="data-Name">SOURCE</div>
                                                <div class="data-value">
                                                    ${((response.contact.leadid != null ) ? '<div class="contact-source-selector tw-w-full hide"><select name="source"></select></div>' : '')}
                                                    
                                                    <div class="contact-source-viewer">
                                                        <span>${response.lead? response.lead.source_name : 'Website'}</span>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            <div class="contact-data-row">
                                                <div class="data-Name">STATUS</div>
                                                <div class="data-value">
                                                    <span class="${response.status}-badge">${response.status}</span>
                                                    <!--<input type="input" name="status" class="contact-input" value="${((response.contact.leadid != null && response.contact.leadid != '') ? 'client' : 'lead')}" readOnly  />-->
                                                </div>
                                            </div>
                                            </form>
                                        </div>`;
                }
                var consumnHtml = `<div class="contact-information margin-top-medium">
                                        <div class="light-box contact-card p-1">
                                            <h5 class="text-center"><img class="missive-img" src="/assets/images/missive/shipment-in-transit.png"/>CONSUMPTION</h5>
                                            ${response.consumption_html}
                                        </div>
                                    </div>`;
                $('#fetch-users').html(`<div>
                    <ul class="nav nav-tabs contact-nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#contact" aria-controls="contact" role="tab" data-toggle="tab"><i class="fa fa-user mr-2"></i> Contact</a>
                        </li>
                        <li role="presentation">
                            <a href="#sales" aria-controls="sales" role="tab" data-toggle="tab"><i class="fas fa-box mr-2"></i> Sales</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="box">
                            <div class="box-content">
                                <div class="contact-information">
                                ${contactDataHtml}
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane active" id="contact">
                            <div class="box">
                                <div class="box-content">
                                    
                                    <div class="contact-information margin-top-medium">
                                    ${leadDataHtml}
                                    </div>
                                    ${response.status == 'Client'? consumnHtml : ''}
                                    <div class="contact-information margin-top-medium">
                                        <div class="light-box contact-card p-1">
                                        
                                            <div class="contact-card-head">
                                                <button class="button button-small button-fixedNote-edit">Edit</button>
                                                <div class="fixedNotes-update-btns hide">
                                                    <button type="submit" class="button button-small button-fixedNote-update">Save</button>
                                                    <button type="button" class="button button-small button-fixedNote-cancel">Cancel</button>
                                                </div>
                                                <h5 class="text-center"><img class="missive-img" src="/assets/images/missive/style-three-pin-user.png"/>Fixed Notes</h5>
                                                <span></span>
                                            </div>
                                            <div class="contact-data-row">
                                                <textarea class="fixedNote-text" date-noteId="${response.fixedNote_id}" rows="3" readOnly>${response.fixed_notes}</textarea>
                                            </div>
                                        
                                        </div>
                                    </div>
                                    <div class="contact-information margin-top-medium">
                                        <div class="light-box contact-card p-1">
                                            <div class="contact-card-head">
                                                <button class="button button-small button-lastNote-edit">Add</button>
                                                <div class="lastNotes-update-btns hide">
                                                    <button type="submit" class="button button-small button-lastNotes-update">Save</button>
                                                    <button type="button" class="button button-small button-lastNotes-cancel">Cancel</button>
                                                </div>
                                                <h5 class="text-center"><img class="missive-img" src="/assets/images/missive/notes-clock.png"/>Last Notes</h5>
                                                <span></span>
                                            </div>

                                            <div class="contact-data-row" id="lastNote-newForm">
                                                <textarea class="lastNotes-Newtext hide" rows="3"></textarea>
                                            </div>
                                            ${lastNotesHtml}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="sales">
                            <div class="box">
                                <div class="box-content">
                                    
                                    <div class="sales-information margin-top-medium">
                                        <div class="light-box sales-card">
                                            <div class="contact-card-head">
                                                <button class="button button-small button-new-Proposal">New</button>
                                                <h5 class="text-center"><img class="missive-img" src="/assets/images/missive/tags-cash.png"/>QUOTATIONS</h5>
                                                <span></span>
                                            </div>
                                            ${response.quotation_html}
                                        </div>
                                    </div>
                                    <div class="sales-information margin-top-medium">
                                        <div class="light-box sales-card">
                                            <div class="contact-card-head">
                                                <button class="button button-small button-all-Contracts">View all</button>
                                                <h5 class="text-center"><img class="missive-img" src="/assets/images/missive/tags-cash.png"/>CONTRACTS</h5>
                                                <span></span>
                                            </div>
                                            ${response.invoice_html}
                                        </div>
                                    </div>
                                    <div class="sales-information margin-top-medium">
                                        <div class="light-box sales-card">
                                            <div class="contact-card-head">
                                                <span></span>
                                                <h5 class="text-center"><img class="missive-img" src="/assets/images/missive/analytics-pie-2.png"/>CONSUMPTION</h5>
                                                <span></span>
                                            </div>
                                            ${response.consumption_html}
                                        </div>
                                    </div>
                                    <div class="sales-information margin-top-medium">
                                        <div class="light-box sales-card">
                                            <div class="contact-card-head">
                                                <span></span>
                                                <h5 class="text-center"><img class="missive-img" src="/assets/images/missive/official-building-3.png"/>PAYMENTS</h5>
                                                <span></span>
                                            </div>
                                            ${response.payment_html}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`);
                if (response.status == 'lead') {
                    console.log('load leadProds', prodItems);
                    $(document).on('change', 'input#sample_check', function(e) {
                        $('input[name="sample_date"]').val(toDayStr);
                    })
                    $(document).on('change', 'input#dataSheet_check', function(e) {
                        $('input[name="dataSheet_date"]').val(toDayStr);
                    })

                    //$('select[name="lead_products"]').val(leadData.Products.split(', '));
                    //val(leadData.Products);
                    $('#lead-form').validate({
                        errorElement: 'span',
                        errorClass: 'help-block',
                        focusInvalid: false,
                        submitHandler: function(form) {
                            var leadPayload = {
                                leadId: response.contact.id,
                                customFields: []
                            };
                            var customValStr = [];
                            var customDates = [];
                            var newDate = new Date();
                            if ($('input[name="sample_check"]:checked').length > 0) {
                                customValStr.push('Sample');
                                customDates.push(newDate.getFullYear() + '-' + (newDate.getMonth() + 1) + '-' + newDate.getDate());
                            }
                            if ($('input[name="dataSheet_check"]:checked').length > 0) {
                                customValStr.push('TDS');
                                customDates.push(newDate.getFullYear() + '-' + (newDate.getMonth() + 1) + '-' + newDate.getDate());
                            }
                            console.log($('input[name="sample_check"]:checked'), $('input[name="sample_check"]').checked);
                            leadPayload.customFields.push({
                                id: $('input[name="lead_customId"]').val(),
                                value: customValStr.toString(),
                                dates: customDates.toString(),
                                fieldto: 1,
                            })
                            leadPayload.customFields.push({
                                id: $('input[name="trackNumber"]').attr('id'),
                                value: $('input[name="trackNumber"]').val(),
                                dates: '',
                                fieldto: 2,
                            })
                            leadPayload.customFields.push({
                                id: $('select[name="lead_products"]').attr('id'),
                                value: $('select[name="lead_products"]').val().toString(),
                                dates: '',
                                fieldto: 4,
                            })
                            leadPayload.customFields.push({
                                id: $('input[name="lead_forecast"]').attr('id'),
                                value: $('input[name="lead_forecast"]').val(),
                                dates: '',
                                fieldto: 5,
                            })
                            // var leadPayload = {
                            //     customId: $('input[name="lead_customId"]').val(),
                            //     customVal: customValStr,
                            //     customDates: customDates,
                            //     trankNum: $('input[name="trackNumber"]').val(),
                            //     trackId: $('input[name="trackNumber"]').attr('id'),
                            // }
                            console.log(leadPayload);
                            $.ajax({
                                url: site_url + 'missiveapp/leadFields_Update',
                                type: 'post',
                                data: leadPayload,
                                dataType: 'json',
                                beforeSend: function() {
                                    $('.button-lead-update').button('loading');
                                    $('.button-lead-cancel').button('loading');
                                },
                                complete: function() {
                                    $('.button-lead-update').button('reset');
                                    $('.button-lead-cancel').button('reset');
                                    //Missive.reload();
                                },
                                success: function(json) {
                                    console.log('getting', json);
                                    if (json.success == true) {
                                        //Missive.reload();
                                        $('.lead-update-btns').removeClass('hide');
                                        $('.button-lead-edit').addClass('hide');
                                        //   Missive.reload();
                                        Missive.reload();
                                        //      $('select[name="country"]').prop("disabled", false);
                                    }
                                    //
                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                }
                            });

                            return false;
                        }
                    })
                }
                console.log('loadCountries', countries);

                //$('select[name="country"]').val(1);
                $('#contact-form').validate({
                    errorElement: 'span',
                    errorClass: 'help-block',
                    focusInvalid: false,
                    rules: {
                        firstname: {
                            required: true
                        },
                        lastname: {
                            required: true
                        },
                        email: {
                            required: true
                        },
                    },

                    invalidHandler: function(event, validator) { //display error alert on form submit   

                    },
                    highlight: function(element) { // hightlight error inputs
                        $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
                    },
                    success: function(label) {
                        label.closest('.form-group').removeClass('has-error');
                        label.remove();
                    },

                    errorPlacement: function(error, element) {},

                    submitHandler: function(form) {
                        var contactSubmitUrl = site_url + 'missiveapp/contact_update';
                        if (fullMissiveData.status == "Client") {
                            contactSubmitUrl = site_url + 'missiveapp/contact_update';
                        } else {
                            contactSubmitUrl = site_url + 'missiveapp/lead_update';
                        }
                        $.ajax({
                            url: contactSubmitUrl,
                            type: 'post',
                            dataType: 'json',
                            data: new FormData($('#contact-form')[0]),
                            cache: false,
                            contentType: false,
                            processData: false,
                            beforeSend: function() {
                                $('.button-contact-update').button('loading');
                                $('.button-contact-cancel').button('loading');
                            },
                            complete: function() {
                                $('.button-contact-update').button('reset');
                                $('.button-contact-cancel').button('reset');
                            },
                            success: function(json) {
                                if (json['error']) {
                                    $('.edit-error-area span').html(json['message']);
                                    $('.edit-error-area').removeClass('hide');
                                }

                                if (json['success']) {
                                    Missive.reload();
                                    $('.contact-update-btns').addClass('hide');
                                    $('.button-contact-edit').removeClass('hide');
                                }
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                            }
                        });

                        return false;
                    }
                });
            } else {
                // $('#fetch-users').html('<div class="box"><div class="box-header columns-middle"><span class="column-grow ellipsis">Contact</span></div><div class="box-content"><div class="list-items light-box"><div class="list-item padding-small"><div class="columns-middle align-center"><span>No contact found.</span></div></div></div></div></div>');
                loadInitCRM(emailData[0]);
            }
        }).fail(function(error) {
            //$('#fetch-users').html('<div class="box"><div class="box-header columns-middle"><span class="column-grow ellipsis">Contact</span></div><div class="box-content"><div class="list-items light-box"><div class="list-item padding-small"><div class="columns-middle align-center"><span>No contact found.</span></div></div></div></div></div>');
            loadInitCRM(emailData[0])
        });
    }

    const cceUser = storeGet('cceUserData');
    const cceUserFlag = storeGet('cceUserFlag');
    let cceUserId = null;
    let fullMissiveData = null;

    if (cceUser != null && typeof(cceUser.id) != "undefined") {
        cceUserId = cceUser.id;
        if (cceUserFlag == null) {
            storeSet('cceUserFlag', 'true');
            Missive.reload();
        }
        // Missive.alert({'title': JSON.stringify(cceUser, null, 4)});
    } else {
        <?php if (!isset($login_page)) { ?>
            location = site_url + 'missiveapp/login';
        <?php } ?>
    }

    Missive.on('change:conversations', (ids) => {

        Missive.fetchConversations(ids).then((conversations) => {
            loadNewProposalData();
            // Missive.alert({'title': JSON.stringify(conversations, null, 4)})
            //$('<p>hERE dATA</p>').appendTo( '#fetch-users' );
            console.log('conversationData', conversations);

            if (cceUserId != null && typeof(conversations[0]) != "undefined") {
                var emailData = null;
                var emailList = [];
                var email_addresses = conversations[0].email_addresses;
                var latest_message = conversations[0].latest_message;
                if (typeof(latest_message) != "undefined" && latest_message.from_field) {
                    emailData = latest_message.from_field.address;
                    latest_message.to_fields.map(toItem => {
                        emailList.push(toItem.address);
                    })
                    emailList.push(latest_message.from_field.address);
                } else if (typeof(email_addresses) != "undefined" && email_addresses.length > 0) {
                    email_addresses.map(emailItem => {
                        emailList.push(emailItem.address);
                    })
                } else if (conversations[0].users) {
                    // emailData = conversations[0].users[0].email;
                }
                //emailData = 'second@email.com';
                // Missive.alert({'title': "Email:" + emailData});

                if (emailList.length > 0) {
                    loadCRMContent(emailList);
                } else {
                    // $('#fetch-users').html('<div class="box"><div class="box-header columns-middle"><span class="column-grow ellipsis">Contact</span></div><div class="box-content"><div class="list-items light-box"><div class="list-item padding-small"><div class="columns-middle align-center"><span>No contact found.</span></div></div></div></div></div>');
                    loadInitCRM('');
                }
            } else {
                //$('#fetch-users').html('<div class="box"><div class="box-header columns-middle"><span class="column-grow ellipsis">Contact</span></div><div class="box-content"><div class="list-items light-box"><div class="list-item padding-small"><div class="columns-middle align-center"><span>No contact found.</span></div></div></div></div></div>');
                loadInitCRM('');
            }
        });
    });

    jQuery(document).ready(function() {

        $(document).on('click', '.button-lead-edit', function(e) {
            $('.button-lead-edit').addClass('hide');
            $('.lead-update-btns').removeClass('hide');
            $('.leadProd-selector').removeClass('hide');
            $('.leadProd-content').addClass('hide');
            $('.lead-input').attr("readonly", false);
            $('.lead-check').attr("readonly", false);
            $('select[name="lead_products"]').select2({
                theme: "classic",
                data: prodItems
            })
            $('select[name="lead_products"]').val($('.lead_prods_span').html().split(',')).trigger('change');
            $('input.lead-input').addClass('active');
        })

        $(document).on('click', '.button-lead-cancel', function(e) {
            $('.button-lead-edit').removeClass('hide');
            $('.lead-update-btns').addClass('hide');
            $('.leadProd-selector').addClass('hide');
            $('.leadProd-content').removeClass('hide');
            $('.lead-input').attr("readonly", true);
            $('.lead-check').attr("readonly", true);
            $('input.lead-input').removeClass('active');
        })

        $(document).on('click', '.button-new-Proposal', function(e) {
            var relType = fullMissiveData.contact.leadid ? 'lead' : 'customer';
            Missive.openURL('https://crm.ctc.expert/admin/proposals/proposal?rel_id=' + fullMissiveData.contact.userid + '&rel_type=' + relType);
        })
        $(document).on('click', '.button-all-Contracts', function(e) {
            Missive.openURL('https://crm.ctc.expert/admin/invoices?clientid=' + fullMissiveData.contact.userid);
        })
        $(document).on('click', '.contract-view', function(e) {
            Missive.openURL('https://crm.ctc.expert/invoice/' + e.target.id + '/' + $(this).attr('data-hash'));
        })
        $(document).on('click', '.contract-edit', function(e) {
            Missive.openURL('https://crm.ctc.expert/admin/invoices/invoice/' + e.target.id);
        })
        $(document).on('click', '.proposal-copy', function(e) {
            Missive.openURL('https://crm.ctc.expert/admin/proposals/repeat/' + e.target.id);
        })
        $(document).on('click', '.proposal-view', function(e) {
            Missive.openURL('https://crm.ctc.expert/proposal/' + e.target.id + '/' + $(this).attr('data-hash'));
        })
        $(document).on('click', '.proposal-edit', function(e) {
            console.log(e, this, $(this));
            Missive.openURL('https://crm.ctc.expert/admin/proposals/proposal/' + e.target.id);
        })

        $(document).on('click', '.button-lastNote-edit', function(e) {
            e.preventDefault();
            $('.lastNotes-update-btns').removeClass('hide');
            $('.button-lastNote-edit').addClass('hide');
            $('.lastNotes-Newtext').removeClass('hide')
        })
        $(document).on('click', '.button-lastNotes-update', function(e) {
            e.preventDefault();
            $('.button-lastNote-edit').removeClass('hide');
            $('.lastNotes-update-btns').addClass('hide');
            $('.lastNotes-Newtext').addClass('hide')
            console.log($('.lastNotes-Newtext').val(), fullMissiveData)
            var lastNotePayload = {
                description: $('.lastNotes-Newtext').val(),
                addedfrom: 1,
                rel_type: fullMissiveData.status == 'Client' ? 'customer' : 'lead',
                rel_id: fullMissiveData.status == 'lead' ? fullMissiveData.contact.id : fullMissiveData.contact.contact_id,
            }
            $.post(site_url + 'missiveapp/addLastNote', lastNotePayload).done(function(response) {
                console.log(response);
                var res = JSON.parse(response)
                if (res.data) {
                    Missive.reload();
                }

            });
        })
        $(document).on('click', '.button-lastNotes-cancel', function(e) {
            e.preventDefault();
            $('.button-lastNote-edit').removeClass('hide');
            $('.lastNotes-update-btns').addClass('hide');
            $('.lastNotes-Newtext').addClass('hide')
        })
        $(document).on('click', '.button-fixedNote-edit', function(e) {
            e.preventDefault();
            $('.button-fixedNote-edit').addClass('hide');
            $('.fixedNotes-update-btns').removeClass('hide');
            $('.fixedNote-text').attr('readonly', false)
        })
        $(document).on('click', '.button-fixedNote-update', function(e) {
            e.preventDefault();
            $('.button-fixedNote-edit').removeClass('hide');
            $('.fixedNotes-update-btns').addClass('hide');
            $('.fixedNote-text').attr('readonly', true);
            console.log($('.fixedNote-text').attr('date-noteId'));
            if ($('.fixedNote-text').attr('date-noteId') != "null") {
                var fixedNotePayload = {
                    csrf_token_name: $('input[name="csrf_token_name"]').val(),
                    description: $('.fixedNote-text').val(),
                    id: $('.fixedNote-text').attr('date-noteId'),
                }
                console.log('update Note Tex', fixedNotePayload);
                var postHead = {
                    'X-CSRF-TOKEN': $('input[name="csrf_token_name"]').val()
                };

                $.post(site_url + 'missiveapp/editFixedNote', fixedNotePayload).done(function(response) {
                    console.log(response);
                    var res = JSON.parse(response)
                    //$('.fixedNote-text').val(res.data.description);
                    Missive.reload();
                });
            } else {
                var lastNotePayload = {
                    description: $('.fixedNote-text').val(),
                    addedfrom: 1,
                    rel_type: fullMissiveData.status == 'Client' ? 'customer' : 'lead',
                    rel_id: fullMissiveData.status == 'lead' ? fullMissiveData.contact.id : fullMissiveData.contact.contact_id,
                }
                $.post(site_url + 'missiveapp/addLastNote', lastNotePayload).done(function(response) {
                    console.log(response);
                    var res = JSON.parse(response)
                    if (res.data) {
                        Missive.reload();
                    }

                });
            }

        })

        $(document).on('click', '.button-fixedNote-cancel', function(e) {
            e.preventDefault();
            $('.button-fixedNote-edit').removeClass('hide');
            $('.fixedNotes-update-btns').addClass('hide');
            $('.fixedNote-text').attr('readonly', true);
        })
        $(document).on('click', '.button-contact-edit', function(e) {
            e.preventDefault();
            //$('select[name="country"]').prop("disabled", false);
            $('input.contact-input').attr("readonly", false);
            $('input.contact-input').addClass('active');
            // console.log('inputCountry', $('select[name="country"]'));
            $('.button-contact-edit').addClass('hide');
            $('.contact-update-btns').removeClass('hide');
            $('.contact-country-selector').removeClass('hide');
            $('.contact-country-viewer').addClass('hide');
            $('select[name="country[]"]').select2({
                theme: "classic",
                data: countries
            });

            $('.customerGroup-badge').addClass('hide');
            $('.customerGroup-edit').removeClass('hide');
            if (fullMissiveData.status == "Client") {
                //$('select[name="country"]').val(fullMissiveData.contact.country_id).trigger('change');
                $('select[name="country[]"]').val(fullMissiveData.contact.country).trigger('change');
                if (fullMissiveData.contact.leadid) {
                    $('.contact-source-selector').removeClass('hide');
                    $('.contact-source-viewer').addClass('hide');
                    $('select[name="source"]').select2({
                        theme: "classic",
                        data: sourcelist
                    });
                    $('select[name="source"]').val(fullMissiveData.lead.source).trigger('change');
                }
            } else {
                //$('select[name="country"]').val(fullMissiveData.contact.country).trigger('change');
                $('.contact-source-selector').removeClass('hide');
                $('.contact-source-viewer').addClass('hide');
                $('select[name="source"]').select2({
                    theme: "classic",
                    data: sourcelist
                });
                $('select[name="source"]').val(fullMissiveData.contact.source).trigger('change');
            }
            //$('.contact-information').addClass('hide');
            //$('.contact-edit-information').removeClass('hide');
        });
        $(document).on('click', '.button-contact-cancel', function(e) {
            e.preventDefault();
            $('input.contact-input').attr("readonly", true);
            $('input.contact-input').removeClass('active');

            $('.contact-update-btns').addClass('hide');
            $('.button-contact-edit').removeClass('hide');
            $('.contact-country-selector').addClass('hide');
            $('.contact-country-viewer').removeClass('hide');
            $('.customerGroup-badge').removeClass('hide');
            $('.customerGroup-edit').addClass('hide');
            if (fullMissiveData.contact.leadid) {
                $('.contact-source-selector').addClass('hide');
                $('.contact-source-viewer').removeClass('hide');
            }

            ///$('.contact-edit-information').addClass('hide');
            //$('.contact-information').removeClass('hide');
        });
        if ($('.login-form').length > 0) {
            $('.login-form').validate({
                errorElement: 'span',
                errorClass: 'help-block',
                focusInvalid: false,
                rules: {
                    email: {
                        required: true
                    },
                    password: {
                        required: true
                    }
                },

                invalidHandler: function(event, validator) { //display error alert on form submit   

                },
                highlight: function(element) { // hightlight error inputs
                    $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
                },
                success: function(label) {
                    label.closest('.form-group').removeClass('has-error');
                    label.remove();
                },

                errorPlacement: function(error, element) {},

                submitHandler: function(form) {

                    $.ajax({
                        url: site_url + 'missiveapp/auth',
                        type: 'post',
                        dataType: 'json',
                        data: new FormData($('#login-form')[0]),
                        cache: false,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            $('#button-login').button('loading');
                        },
                        complete: function() {
                            $('#button-login').button('reset');
                        },
                        success: function(json) {
                            if (json['error']) {
                                $('.form-error-area span').html(json['message']);
                                $('.form-error-area').removeClass('hide');
                                $('.form-group-cptcha').removeClass('hide');
                            }

                            if (json['success']) {
                                // Storing data
                                storeSet('cceUserData', json['user']);

                                // try {
                                //     Missive.storeSet('cceUserData', JSON.stringify(json['user']));
                                // } catch (error) {
                                //     console.error(error); // Handle any errors
                                // }

                                // // console.log(Missive.storeGet('cceUserData'));
                                // Missive.storeGet('cceUserData').then(data => {
                                //     console.error(data);
                                // }).catch(error => {
                                //     console.error(error); // Handle any errors
                                // });

                                location = site_url + 'missiveapp';
                            }
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });

                    return false;
                }
            });
        }
    });
</script>

</body>

</html>
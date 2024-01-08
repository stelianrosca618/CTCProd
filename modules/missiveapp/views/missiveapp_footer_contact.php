<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://integrations.missiveapp.com/missive.js"></script>

<script type="text/javascript">
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

    const cceUser = storeGet('cceUserData');
    const cceUserFlag = storeGet('cceUserFlag');
    let cceUserId = null;

    if (cceUser != null && typeof (cceUser.id) != "undefined") {
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
            // Missive.alert({'title': JSON.stringify(conversations, null, 4)})
            console.log('missvi', conversations);
            if (cceUserId != null && typeof(conversations[0]) != "undefined") {
                var emailData = null;
                var email_addresses = conversations[0].email_addresses;
                var latest_message = conversations[0].latest_message;
                if (typeof(latest_message) != "undefined" && latest_message.from_field) {
                    emailData = latest_message.from_field.address;
                } else if (typeof(email_addresses) != "undefined" && email_addresses.length > 0) {
                    emailData = email_addresses[0].address;
                }

                // Missive.alert({'title': "Email:" + emailData});

                if (emailData != null) {
                    $.ajax({
                        type: 'get',
                        data: {'email': emailData},
                        url: site_url + 'missiveapp/contact'
                    }).done(function(response) {
                        response = JSON.parse(response);
                        if (response.success) {
                            $('#fetch-users').html('<div class="contact-information"><div class="list-items light-box"><div class="list-item padding-small"><div class="columns-middle"><span>Company: ' + response.contact.company + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Client: ' + ((response.contact.leadid != null && response.contact.leadid != '') ? 'Yes' : 'Yes') + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Created: ' + response.contact.created_date + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Level: ' + ((response.contact.customerGroups != null && response.contact.customerGroups != '') ? response.contact.customerGroups : '--') + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Country: ' + response.contact.short_name + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Name: ' + response.contact.firstname + ' ' + response.contact.lastname + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Email: ' + response.contact.email + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Status: ' + ((response.contact.leadid != null && response.contact.leadid != '') ? response.lead.status_name : '--') + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Source: ' + ((response.contact.leadid != null && response.contact.leadid != '') ? response.lead.source_name : '--') + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Products: ' + response.products + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Consumption: ' + response.consumption + '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><button type="button" class="btn btn-default button-contact-edit">Edit</button></div></div></div></div><div class="contact-edit-information hide"><form id="contact-form" autocomplete="off" method="post" novalidate="novalidate"><input type="hidden" name="' + response.csrf_name + '" value="' + response.csrf_hash + '"><div class="panel panel-default"><div class="panel-body"><div class="alert alert-vk alert-danger edit-error-area hide"><strong>Error!</strong> <span></span></div><input type="hidden" name="contactid" value="' + response.contact.contact_id + '"><div class="form-group" app-field-wrapper="firstname"><label for="firstname" class="control-label"> <small class="req text-danger">* </small>First Name</label><input type="text" id="firstname" name="firstname" class="form-control" value="' + response.contact.firstname + '"></div><div class="form-group" app-field-wrapper="lastname"><label for="lastname" class="control-label"> <small class="req text-danger">* </small>Last Name</label><input type="text" id="lastname" name="lastname" class="form-control" value="' + response.contact.lastname + '"></div><div class="form-group" app-field-wrapper="title"><label for="title" class="control-label">Position</label><input type="text" id="title" name="title" class="form-control" value="' + response.contact.title + '"></div><div class="form-group" app-field-wrapper="email"><label for="email" class="control-label"> <small class="req text-danger">* </small>Email</label><input type="email" id="email" name="email" class="form-control" value="' + response.contact.email + '" readonly></div><div class="form-group" app-field-wrapper="phonenumber"><label for="phonenumber" class="control-label">Phone</label><input type="text" id="phonenumber" name="phonenumber" class="form-control" autocomplete="off" value="' + ((response.contact.phonenumber != '') ? response.contact.phonenumber : response.contact.calling_code) + '"></div></div><div class="panel-footer"><button type="submit" class="btn btn-primary button-contact-update" data-loading-text="Please wait..." autocomplete="off" data-form="#contact-form">Save</button><button type="button" class="btn btn-default button-contact-cancel">Cancel</button></div></div></form></div><div class="contact-information"><div class="box margin-top-medium"><div class="box-content">Fixed Notes:<div>' + response.fixed_notes + '</div></div></div><div class="box margin-top-medium"><div class="box-content">Last Notes:<div>' + response.last_notes + '</div></div></div></div>');

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

                              errorPlacement: function(error, element) {
                              },

                              submitHandler: function(form) {
                                  $.ajax({
                                    url: site_url + 'missiveapp/contact_update',
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
                            
                            $('#fetch-users').html('<div class="list-item padding-small"><div class="columns-middle align-center"><span>No contact found.</span></div></div>');
                        }
                    }).fail(function(error) {
                        $('#fetch-users').html('<div class="list-item padding-small"><div class="columns-middle align-center"><span>No contact found.</span></div></div>');
                    });
                } else {
                    $('#fetch-users').html('<div class="list-item padding-small"><div class="columns-middle align-center"><span>No contact found.</span></div></div>');
                }
            } else {
                $('#fetch-users').html('<div class="list-item padding-small"><div class="columns-middle align-center"><span>No contact found.</span></div></div>');
            }
        });
    });
    
    jQuery(document).ready(function() {
        $(document).on('click', '.button-contact-edit', function(e) {
            e.preventDefault();
            $('.contact-information').addClass('hide');
            $('.contact-edit-information').removeClass('hide');
        });
        $(document).on('click', '.button-contact-cancel', function(e) {
            e.preventDefault();            
            $('.contact-edit-information').addClass('hide');
            $('.contact-information').removeClass('hide');
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

              errorPlacement: function(error, element) {
              },

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
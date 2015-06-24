jQuery(document).ready(function($){

    $(document).on('submit', 'form.leyka-pm-form', function(e){

        e.preventDefault();

        // Donation form validation is already passed in the main script (public.js)

        var $form = $(this),
            data_array = $form.serializeArray(),
            data = {};

        for(var i=0; i<data_array.length; i++) {
            data[data_array[i].name] = data_array[i].value;
        }
        data.action = 'leyka_ajax_donation_submit';

        $.ajax({
            type: 'post',
            url: leyka.ajaxurl,
            data: data,
            beforeSend: function(xhr){
                /** @todo Show some loader */
            }
        }).done(function(response){

            response = $.parseJSON(response);
            if( !response || !response.status ) {

                /** @todo Show some error message on the form */
                return false;

            } else if(response.status == 0 && response.message) {

                /** @todo Show response.message on the form */
                return false;

            } else if( !response.public_id ) {

                /** @todo Show response.message on the form */
                return false;
            }

            var widget = new cp.CloudPayments();
            widget.charge({
                publicId: response.public_id,
                description: response.payment_title,
                amount: parseFloat(response.amount),
                currency: response.currency,
                invoiceId: parseInt(response.donation_id),
                accountId: response.donor_email /*,
                data: {
                    myProp: 'myProp value'
                }*/
            }, function(options){ // success callback
                console.log('Success!', options);
            }, function(reason, options){ // fail callback
                console.log('Fail!', options);
            });
        });
    });
});